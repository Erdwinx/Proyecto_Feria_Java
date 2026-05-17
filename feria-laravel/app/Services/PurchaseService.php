<?php

namespace App\Services;

use App\Exceptions\SeatUnavailableException;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Package;
use App\Models\SeatReservation;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * @param array<string, mixed> $payload
     * @return array<int, Ticket>
     */
    public function execute(Customer $customer, array $payload): array
    {
        $tipoEvento = $this->inferEventType((string) $payload['fechaEvento']);
        $createdTickets = [];

        if ($tipoEvento === 'concierto') {
            $itemsByCategory = $this->groupConcertItemsByCategory($payload);
            
            DB::transaction(function () use ($payload, $itemsByCategory, $customer, &$createdTickets) {
                foreach ($itemsByCategory as $category => $items) {
                    $allSeats = [];
                    
                    // Collect all seats for this category
                    foreach ($items as $item) {
                        if (!empty($item['seatNumbers']) && is_array($item['seatNumbers'])) {
                            foreach ($item['seatNumbers'] as $seat) {
                                if (is_string($seat) && $seat !== '') {
                                    $allSeats[] = $seat;
                                }
                            }
                        }
                    }
                    $allSeats = array_values(array_unique($allSeats));
                    
                    // Reserve seats for this category
                    if (!empty($allSeats)) {
                        $conflicts = [];
                        foreach ($allSeats as $seat) {
                            try {
                                SeatReservation::create([
                                    'fecha_evento' => (string) $payload['fechaEvento'],
                                    'category' => $category,
                                    'seat_number' => $seat,
                                    'status' => 'reserved',
                                ]);
                            } catch (QueryException $qe) {
                                $conflicts[] = $seat;
                            }
                        }
                        
                        if (!empty($conflicts)) {
                            throw new SeatUnavailableException($conflicts);
                        }
                    }
                    
                    // Create one package per category
                    $package = Package::create([
                        'id' => $this->nextPackageId(),
                        'nombre' => sprintf('Concierto %s - %s (%d asientos)', (string) $payload['fechaEvento'], strtoupper($category), count($allSeats)),
                        'tipo_evento' => 'concierto',
                        'fecha_evento' => (string) $payload['fechaEvento'],
                        'qr_text' => null,
                        'qr_generated_at' => null,
                    ]);
                    
                    $ticketName = sprintf('%s - Paquete %d asientos', ucfirst($category), count($allSeats));
                    
                    $ticket = Ticket::create([
                        'id' => $this->nextTicketId($ticketName),
                        'nombre' => $ticketName,
                        'fecha_evento' => (string) $payload['fechaEvento'],
                        'tipo_evento' => 'concierto',
                        'category' => $category,
                        'seat_numbers' => $allSeats,
                        'package_id' => $package->id,
                        'escaneado' => false,
                        'customer_id' => $customer->id,
                    ]);
                    
                    if (!empty($allSeats)) {
                        SeatReservation::query()
                            ->whereDate('fecha_evento', (string) $payload['fechaEvento'])
                            ->where('category', $category)
                            ->whereIn('seat_number', $allSeats)
                            ->update(['ticket_id' => $ticket->id, 'status' => 'sold']);
                    }
                    
                    $createdTickets[] = $ticket;
                }
            });
            
            return $createdTickets;
        }

        foreach ((array) $payload['items'] as $item) {
            $createdTickets[] = Ticket::create([
                'id' => $this->nextTicketId((string) $item['name']),
                'nombre' => (string) $item['name'],
                'fecha_evento' => (string) $payload['fechaEvento'],
                'tipo_evento' => $tipoEvento,
                'category' => $item['category'] ?? null,
                'seat_numbers' => [],
                'package_id' => null,
                'escaneado' => false,
                'customer_id' => $customer->id,
            ]);
        }

        return $createdTickets;
    }

    private function inferEventType(string $fechaEvento): string
    {
        $event = Event::query()->whereDate('fecha_evento', $fechaEvento)->first();
        if ($event && isset($event->tipo_evento)) {
            return $event->tipo_evento;
        }

        $concertDates = ['2026-06-15', '2026-06-16'];
        return in_array($fechaEvento, $concertDates, true) ? 'concierto' : 'feria';
    }

    /**
     * Group concert items by category to create separate packages
     * @param array<string, mixed> $payload
     * @return array<string, array>
     */
    private function groupConcertItemsByCategory(array $payload): array
    {
        $grouped = [];
        
        foreach ((array) $payload['items'] as $item) {
            $category = (string) ($item['category'] ?? 'general');
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $item;
        }
        
        return $grouped;
    }

    private function nextPackageId(): string
    {
        $max = Package::query()
            ->select('id')
            ->get()
            ->map(function (Package $package) {
                $raw = $package->id;
                if (strlen($raw) < 3) {
                    return 0;
                }
                return (int) substr($raw, 2);
            })
            ->max();

        $next = ((int) $max) + 1;
        return 'PK' . str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    private function nextTicketId(string $nombre): string
    {
        $prefix = $this->buildPrefix($nombre);
        $max = Ticket::query()
            ->select('id')
            ->get()
            ->map(function (Ticket $ticket) {
                $raw = $ticket->id;
                if (strlen($raw) < 3) {
                    return 0;
                }

                return (int) substr($raw, 2);
            })
            ->max();

        $next = ((int) $max) + 1;
        return $prefix . str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    private function buildPrefix(string $nombre): string
    {
        $firstName = preg_split('/\s+/', trim($nombre))[0] ?? '';
        $letters = strtoupper(preg_replace('/[^\p{L}]/u', '', $firstName) ?? '');

        if (strlen($letters) >= 2) {
            return substr($letters, 0, 2);
        }
        if (strlen($letters) === 1) {
            return $letters . 'X';
        }

        return 'XX';
    }
}
