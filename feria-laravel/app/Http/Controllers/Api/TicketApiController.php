<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanLogEntry;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketApiController extends Controller
{
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

    public function createTicket(Request $request)
    {
        $payload = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'fechaEvento' => ['required', 'date_format:Y-m-d'],
        ]);

        $ticket = Ticket::create([
            'id' => $this->nextTicketId($payload['nombre']),
            'nombre' => $payload['nombre'],
            'fecha_evento' => $payload['fechaEvento'],
            'escaneado' => false,
            'customer_id' => null,
        ]);

        return response()->json($this->ticketToArray($ticket));
    }

    public function getTicket(string $id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        return response()->json($this->ticketToArray($ticket));
    }

    public function getCurrentQr(string $id)
    {
        $ticket = Ticket::query()->find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }
        if ($ticket->escaneado) {
            return response()->json(['message' => 'El boleto ya fue escaneado'], 409);
        }

        return response()->json([
            'ticketId' => $ticket->id,
            'nombre' => $ticket->nombre,
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

        if (count($parts) !== 6 || $parts[0] !== 'FERIAQR') {
            return response()->json($this->scanResponse(false, 'Formato de QR invalido', null, null, $evaluatedAt, null));
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

    public function recoverTicket(Request $request)
    {
        $payload = $request->validate([
            'key' => ['required', 'string'],
            'ticketId' => ['required', 'string'],
        ]);

        $recoverKey = (string) env('APP_RECOVER_KEY', 'RECUPERAR-2026');
        if (!hash_equals($recoverKey, $payload['key'])) {
            return response()->json(['message' => 'Clave invalida'], 401);
        }

        $ticket = Ticket::query()->find($payload['ticketId']);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }

        if ($ticket->escaneado) {
            $ticket->escaneado = false;
            $ticket->save();
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

    private function signQr(string $ticketId, string $nombre, int $issuedAt, string $nonce): string
    {
        $secret = (string) env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $payload = $ticketId.'|'.$nombre.'|'.$issuedAt.'|'.$nonce;
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

        return [
            'id' => $ticket->id,
            'nombre' => $ticket->nombre,
            'fechaEvento' => (string) $fechaEvento,
            'escaneado' => (bool) $ticket->escaneado,
            'customerId' => $ticket->customer_id,
        ];
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
