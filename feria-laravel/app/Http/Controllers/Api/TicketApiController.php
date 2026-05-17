<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\ScanLogEntry;
use App\Models\Ticket;
use App\Traits\ApiErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketApiController extends Controller
{
    use ApiErrorHandler;

    public function health()
    {
        return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
    }

    public function listTickets()
    {
        $tickets = Ticket::query()->orderBy('fecha_evento')->get();
        return response()->json($tickets->map(fn (Ticket $ticket) => $this->ticketToArray($ticket))->values());
    }

    public function listScans()
    {
        $scans = ScanLogEntry::query()->orderByDesc('scanned_at_epoch_seconds')->get();

        return response()->json($scans->map(function (ScanLogEntry $scan) {
            return [
                'id' => $scan->id,
                'ticketId' => $scan->ticket_id,
                'nombre' => $scan->nombre,
                'scannedAtEpochSeconds' => (int) $scan->scanned_at_epoch_seconds,
            ];
        })->values());
    }

    public function availableSeats(Request $request)
    {
        $payload = $request->validate([
            'fechaEvento' => ['required', 'date_format:Y-m-d'],
            'category' => ['nullable', 'string', 'in:general,grada,vip'],
        ]);
        $tipoEvento = $this->inferEventType($payload['fechaEvento']);
        $category = $payload['category'] ?? ($tipoEvento === 'concierto' ? 'grada' : 'vip');

        $allSeats = $this->seatCatalogForCategory($category);
        $soldSeats = $this->soldSeatsForDateAndCategory($payload['fechaEvento'], $tipoEvento, $category);
        $availableSeats = array_values(array_diff($allSeats, $soldSeats));

        return response()->json([
            'fechaEvento' => $payload['fechaEvento'],
            'tipoEvento' => $tipoEvento,
            'category' => $category,
            'availableSeats' => $availableSeats,
            'soldSeats' => array_values($soldSeats),
            'allowedCategories' => $tipoEvento === 'concierto' ? ['grada', 'vip'] : ['general', 'grada', 'vip'],
        ]);
    }

    public function createTicket(Request $request)
    {
        $payload = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'fechaEvento' => ['required', 'date_format:Y-m-d'],
            'tipoEvento' => ['required', 'string', 'in:concierto,feria'],
            'packageId' => ['nullable', 'string'],
        ]);

        $ticket = Ticket::create([
            'id' => $this->nextTicketId($payload['nombre']),
            'nombre' => $payload['nombre'],
            'fecha_evento' => $payload['fechaEvento'],
            'tipo_evento' => $payload['tipoEvento'],
            'package_id' => $payload['packageId'] ?? null,
            'escaneado' => false,
            'customer_id' => null,
        ]);

        return response()->json($this->ticketToArray($ticket));
    }

    public function getTicket(string $id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) {
            return $this->errorResponse('Ticket no encontrado', 404);
        }

        return response()->json($this->ticketToArray($ticket));
    }

    public function getCurrentQr(string $id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) {
            return $this->errorResponse('Ticket no encontrado', 404);
        }

        // Si es un concierto con paquete, usar QR del paquete
        if ($ticket->tipo_evento === 'concierto' && $ticket->package_id) {
            $package = Package::query()->find($ticket->package_id);
            if (!$package) {
                return $this->errorResponse('Paquete no encontrado', 404);
            }

            // Verificar si algún boleto del paquete ya fue escaneado
            $scannedTicket = $package->tickets()->where('escaneado', true)->first();
            if ($scannedTicket) {
                return $this->errorResponse('El paquete ya fue escaneado', 409);
            }

            // Generar QR del paquete si no existe
            if (!$package->qr_text) {
                $package->qr_text = $this->createPackageQrText($package);
                $package->qr_generated_at = now();
                $package->save();
            }

            return response()->json([
                'ticketId' => $ticket->id,
                'nombre' => $ticket->nombre,
                'packageId' => $package->id,
                'packageName' => $package->nombre,
                'tipoEvento' => $ticket->tipo_evento,
                'qrText' => $package->qr_text,
                'expiresAtEpochSeconds' => 0,
            ]);
        }

        // QR individual para eventos de feria
        if ($ticket->escaneado) {
            return $this->errorResponse('El boleto ya fue escaneado', 409);
        }

        return response()->json([
            'ticketId' => $ticket->id,
            'nombre' => $ticket->nombre,
            'tipoEvento' => $ticket->tipo_evento,
            'qrText' => $this->createCurrentQrText($ticket),
            'expiresAtEpochSeconds' => 0,
        ]);
    }

    public function validateScan(Request $request)
    {
        $payload = $request->validate([
            'qrText' => ['required', 'string'],
        ]);

        $evaluatedAt = time();
        $qrText = trim((string) $payload['qrText']);
        $parts = explode('|', $qrText);

        // Verificar formato básico
        if (count($parts) < 6 || $parts[0] !== 'FERIAQR') {
            return response()->json($this->scanResponse(false, 'Formato de QR invalido', null, null, $evaluatedAt, null));
        }

        // Determinar si es QR de paquete o individual
        $isPackageQr = $parts[1] === 'PKG';

        if ($isPackageQr) {
            // Formato: FERIAQR|PKG|packageId|encodedName|issuedAt|nonce|signature
            if (count($parts) !== 7) {
                return response()->json($this->scanResponse(false, 'Formato de QR de paquete invalido', null, null, $evaluatedAt, null));
            }
            [$prefix, $type, $packageId, $encodedName, $issuedAtRaw, $nonce, $signature] = $parts;
            
            if (!is_numeric($issuedAtRaw)) {
                return response()->json($this->scanResponse(false, 'Tiempo de emision invalido', $packageId, null, $evaluatedAt, null));
            }

            $issuedAt = (int) $issuedAtRaw;
            $decodedBytes = base64_decode(strtr($encodedName, '-_', '+/'), true);
            if ($decodedBytes === false) {
                return response()->json($this->scanResponse(false, 'Nombre codificado invalido', $packageId, null, $evaluatedAt, null));
            }
            $decodedName = $decodedBytes;

            return $this->validatePackageScan($packageId, $decodedName, $issuedAt, $nonce, $signature, $evaluatedAt);
        } else {
            // Formato: FERIAQR|ticketId|encodedName|issuedAt|nonce|signature
            if (count($parts) !== 6) {
                return response()->json($this->scanResponse(false, 'Formato de QR individual invalido', null, null, $evaluatedAt, null));
            }
            [$prefix, $ticketId, $encodedName, $issuedAtRaw, $nonce, $signature] = $parts;

            if (!is_numeric($issuedAtRaw)) {
                return response()->json($this->scanResponse(false, 'Tiempo de emision invalido', $ticketId, null, $evaluatedAt, null));
            }

            $issuedAt = (int) $issuedAtRaw;
            $decodedBytes = base64_decode(strtr($encodedName, '-_', '+/'), true);
            if ($decodedBytes === false) {
                return response()->json($this->scanResponse(false, 'Nombre codificado invalido', $ticketId, null, $evaluatedAt, null));
            }
            $decodedName = $decodedBytes;

            return $this->validateTicketScan($ticketId, $decodedName, $issuedAt, $nonce, $signature, $evaluatedAt);
        }
    }

    private function validateTicketScan(string $ticketId, string $decodedName, int $issuedAt, string $nonce, string $signature, int $evaluatedAt)
    {
        $ticket = Ticket::query()->find($ticketId);
        if (!$ticket) {
            return response()->json($this->scanResponse(false, 'ID no encontrado', $ticketId, $decodedName, $evaluatedAt, null));
        }
        if ($ticket->escaneado) {
            return response()->json($this->scanResponse(false, 'Boleto ya escaneado', $ticketId, $decodedName, $evaluatedAt, null));
        }
        if ($ticket->nombre !== $decodedName) {
            return response()->json($this->scanResponse(false, 'Nombre no coincide con el ID', $ticketId, $decodedName, $evaluatedAt, null));
        }

        $windowSeconds = max(1, (int) env('APP_QR_WINDOW_SECONDS', 240));
        $expiresAt = null;
        if ($issuedAt > 0 && $windowSeconds > 0) {
            $computedExpiresAt = $issuedAt + $windowSeconds;
            if ($evaluatedAt > $computedExpiresAt) {
                return response()->json($this->scanResponse(false, 'QR vencido. Genera uno nuevo', $ticketId, $decodedName, $evaluatedAt, $computedExpiresAt));
            }
            $expiresAt = $computedExpiresAt;
        }

        $expected = $this->signQr($ticketId, $decodedName, $issuedAt, $nonce);
        if (!hash_equals($expected, $signature)) {
            return response()->json($this->scanResponse(false, 'Firma invalida', $ticketId, $decodedName, $evaluatedAt, null));
        }

        DB::transaction(function () use ($ticket, $evaluatedAt) {
            $ticket->escaneado = true;
            $ticket->save();
            ScanLogEntry::query()->create([
                'ticket_id' => $ticket->id,
                'nombre' => $ticket->nombre,
                'scanned_at_epoch_seconds' => $evaluatedAt,
            ]);
        });

        return response()->json($this->scanResponse(true, 'Escaneado con exito', $ticketId, $decodedName, $evaluatedAt, $expiresAt));
    }

    private function inferEventType(string $fechaEvento): string
    {
        $event = \App\Models\Event::query()->whereDate('fecha_evento', $fechaEvento)->first();
        if ($event && isset($event->tipo_evento)) {
            return $event->tipo_evento;
        }

        $concertDates = ['2026-06-15', '2026-06-16'];
        return in_array($fechaEvento, $concertDates, true) ? 'concierto' : 'feria';
    }

    private function seatCatalogForCategory(string $category): array
    {
        return match ($category) {
            'grada' => [
                'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12',
                'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10', 'B11', 'B12',
                'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'C11', 'C12',
            ],
            'vip' => ['V1', 'V2', 'V3', 'V4', 'V5', 'V6', 'V7', 'V8', 'V9', 'V10', 'V11', 'V12'],
            default => [],
        };
    }
    private function soldSeatsForDateAndCategory(string $fechaEvento, string $tipoEvento, string $category): array
    {
        if ($category === 'general') {
            return [];
        }

        // Prefer seat_reservations as authoritative sold/reserved source
        $reserved = \App\Models\SeatReservation::query()
            ->whereDate('fecha_evento', $fechaEvento)
            ->where('category', $category)
            ->whereIn('status', ['reserved', 'sold'])
            ->pluck('seat_number')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($reserved)) {
            return $reserved;
        }

        return Ticket::query()
            ->whereDate('fecha_evento', $fechaEvento)
            ->where('tipo_evento', $tipoEvento)
            ->where('category', $category)
            ->get()
            ->pluck('seat_numbers')
            ->flatten()
            ->filter(fn ($seat) => is_string($seat) && $seat !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function validatePackageScan(string $packageId, string $decodedName, int $issuedAt, string $nonce, string $signature, int $evaluatedAt)
    {
        $package = Package::query()->find($packageId);
        if (!$package) {
            return response()->json($this->scanResponse(false, 'Paquete no encontrado', $packageId, $decodedName, $evaluatedAt, null));
        }

        // Verificar si algún boleto del paquete ya fue escaneado
        $scannedTicket = $package->tickets()->where('escaneado', true)->first();
        if ($scannedTicket) {
            $this->logError('Package already scanned: ' . $packageId, ['packageId' => $packageId], 'warning');
            return response()->json($this->scanResponse(false, 'Paquete ya escaneado', $packageId, $decodedName, $evaluatedAt, null));
        }

        if ($package->nombre !== $decodedName) {
            $this->logError('Package name mismatch during scan: ' . $packageId, ['expected' => $package->nombre, 'provided' => $decodedName], 'warning');
            return response()->json($this->scanResponse(false, 'Nombre no coincide con el ID', $packageId, $decodedName, $evaluatedAt, null));
        }

        $windowSeconds = max(1, (int) env('APP_QR_WINDOW_SECONDS', 240));
        $expiresAt = null;
        if ($issuedAt > 0 && $windowSeconds > 0) {
            $computedExpiresAt = $issuedAt + $windowSeconds;
            if ($evaluatedAt > $computedExpiresAt) {
                return response()->json($this->scanResponse(false, 'QR vencido. Genera uno nuevo', $packageId, $decodedName, $evaluatedAt, $computedExpiresAt));
            }
            $expiresAt = $computedExpiresAt;
        }

        $expected = $this->signPackageQr($packageId, $decodedName, $issuedAt, $nonce);
        if (!hash_equals($expected, $signature)) {
            return response()->json($this->scanResponse(false, 'Firma invalida', $packageId, $decodedName, $evaluatedAt, null));
        }

        // Marcar todos los boletos del paquete como escaneados
        DB::transaction(function () use ($package, $evaluatedAt) {
            $packageTickets = $package->tickets()->get();
            foreach ($packageTickets as $ticket) {
                $ticket->escaneado = true;
                $ticket->save();

                ScanLogEntry::query()->create([
                    'ticket_id' => $ticket->id,
                    'nombre' => $ticket->nombre,
                    'scanned_at_epoch_seconds' => $evaluatedAt,
                ]);
            }
        });

        return response()->json($this->scanResponse(true, 'Paquete escaneado con exito', $packageId, $decodedName, $evaluatedAt, $expiresAt));
    }

    public function recoverTicket(Request $request)
    {
        $payload = $request->validate([
            'key' => ['required', 'string'],
            'ticketId' => ['required', 'string'],
        ]);

        $recoverKey = (string) env('APP_RECOVER_KEY', 'RECUPERAR-2026');
        if (!hash_equals($recoverKey, $payload['key'])) {
            return $this->errorResponse('Clave invalida', 401);
        }

        $ticket = Ticket::query()->find($payload['ticketId']);
        if (!$ticket) {
            return $this->errorResponse('Ticket no encontrado', 404);
        }

        // Si el boleto pertenece a un paquete, recuperar todos los boletos del paquete
        if ($ticket->package_id) {
            $package = Package::query()->find($ticket->package_id);
            if ($package) {
                $packageTickets = $package->tickets()->get();
                foreach ($packageTickets as $packageTicket) {
                    $packageTicket->escaneado = false;
                    $packageTicket->save();
                    ScanLogEntry::query()->where('ticket_id', $packageTicket->id)->delete();
                }
            }
        } else if ($ticket->escaneado) {
            $ticket->escaneado = false;
            $ticket->save();
            ScanLogEntry::query()->where('ticket_id', $ticket->id)->delete();
        }

        return response()->json($this->ticketToArray($ticket));
    }

    private function createCurrentQrText(Ticket $ticket): string
    {
        $issuedAt = 0;
        $nonce = 'STATIC';
        $encodedName = rtrim(strtr(base64_encode($ticket->nombre), '+/', '-_'), '=');
        $signature = $this->signQr($ticket->id, $ticket->nombre, $issuedAt, $nonce);

        return implode('|', ['FERIAQR', $ticket->id, $encodedName, (string) $issuedAt, $nonce, $signature]);
    }

    private function createPackageQrText(Package $package): string
    {
        $issuedAt = 0;
        $nonce = 'STATIC';
        $encodedName = rtrim(strtr(base64_encode($package->nombre), '+/', '-_'), '=');
        $signature = $this->signPackageQr($package->id, $package->nombre, $issuedAt, $nonce);

        return implode('|', ['FERIAQR', 'PKG', $package->id, $encodedName, (string) $issuedAt, $nonce, $signature]);
    }

    private function signQr(string $ticketId, string $nombre, int $issuedAt, string $nonce): string
    {
        $secret = (string) env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $payload = $ticketId.'|'.$nombre.'|'.$issuedAt.'|'.$nonce;
        $digest = hash_hmac('sha256', $payload, $secret, true);

        return rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
    }

    private function signPackageQr(string $packageId, string $nombre, int $issuedAt, string $nonce): string
    {
        $secret = (string) env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $payload = 'PKG|'.$packageId.'|'.$nombre.'|'.$issuedAt.'|'.$nonce;
        $digest = hash_hmac('sha256', $payload, $secret, true);

        return rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
    }

    private function scanResponse(bool $valid, string $message, ?string $ticketId, ?string $nombre, int $evaluatedAtEpochSeconds, ?int $expiresAtEpochSeconds): array
    {
        return [
            'valid' => $valid,
            'message' => $message,
            'ticketId' => $ticketId,
            'nombre' => $nombre,
            'evaluatedAtEpochSeconds' => $evaluatedAtEpochSeconds,
            'expiresAtEpochSeconds' => $expiresAtEpochSeconds,
        ];
    }

    private function ticketToArray(Ticket $ticket): array
    {
        $fechaEvento = $ticket->fecha_evento;
        if ($fechaEvento instanceof \DateTimeInterface) {
            $fechaEvento = $fechaEvento->format('Y-m-d');
        }

        $data = [
            'id' => $ticket->id,
            'nombre' => $ticket->nombre,
            'fechaEvento' => (string) $fechaEvento,
            'tipoEvento' => $ticket->tipo_evento,
            'category' => $ticket->category,
            'seatNumbers' => $ticket->seat_numbers ?? [],
            'escaneado' => (bool) $ticket->escaneado,
            'customerId' => $ticket->customer_id,
        ];

        if ($ticket->tipo_evento === 'concierto' && $ticket->package_id) {
            $package = $ticket->package;
            if ($package) {
                $data['packageId'] = $package->id;
                $data['packageName'] = $package->nombre;
            }
        }

        return $data;
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
        return $prefix.str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    private function buildPrefix(string $nombre): string
    {
        $firstName = preg_split('/\s+/', trim($nombre))[0] ?? '';
        $letters = strtoupper(preg_replace('/[^\p{L}]/u', '', $firstName) ?? '');

        if (strlen($letters) >= 2) {
            return substr($letters, 0, 2);
        }
        if (strlen($letters) === 1) {
            return $letters.'X';
        }

        return 'XX';
    }
}
