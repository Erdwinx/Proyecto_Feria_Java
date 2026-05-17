<?php

namespace Tests\Feature;

use App\Models\ScanLogEntry;
use App\Models\Ticket;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_individual_qr_scan()
    {
        $ticket = Ticket::create([
            'id' => 'TE00000001',
            'nombre' => 'Test Ticket',
            'fecha_evento' => '2026-05-10',
            'tipo_evento' => 'feria',
            'escaneado' => false,
            'customer_id' => null,
        ]);

        // Create a valid QR
        $secret = env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $issuedAt = 0;
        $nonce = 'STATIC';
        $payload = $ticket->id . '|' . $ticket->nombre . '|' . $issuedAt . '|' . $nonce;
        $digest = hash_hmac('sha256', $payload, $secret, true);
        $signature = rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
        
        $encodedName = rtrim(strtr(base64_encode($ticket->nombre), '+/', '-_'), '=');
        $qrText = implode('|', ['FERIAQR', $ticket->id, $encodedName, (string)$issuedAt, $nonce, $signature]);

        $response = $this->postJson('/api/scan/validate', [
            'qrText' => $qrText,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => true]);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'escaneado' => true]);
    }

    public function test_cannot_scan_already_scanned_ticket()
    {
        $ticket = Ticket::create([
            'id' => 'TE00000002',
            'nombre' => 'Scanned Ticket',
            'fecha_evento' => '2026-05-10',
            'tipo_evento' => 'feria',
            'escaneado' => true,
            'customer_id' => null,
        ]);

        $secret = env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $issuedAt = 0;
        $nonce = 'STATIC';
        $payload = $ticket->id . '|' . $ticket->nombre . '|' . $issuedAt . '|' . $nonce;
        $digest = hash_hmac('sha256', $payload, $secret, true);
        $signature = rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
        
        $encodedName = rtrim(strtr(base64_encode($ticket->nombre), '+/', '-_'), '=');
        $qrText = implode('|', ['FERIAQR', $ticket->id, $encodedName, (string)$issuedAt, $nonce, $signature]);

        $response = $this->postJson('/api/scan/validate', [
            'qrText' => $qrText,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => false, 'message' => 'Boleto ya escaneado']);
    }

    public function test_invalid_qr_format()
    {
        $response = $this->postJson('/api/scan/validate', [
            'qrText' => 'INVALID_QR_FORMAT',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['valid' => false]);
    }

    public function test_recover_ticket_clears_scan_log_and_restores_ticket(): void
    {
        $ticket = Ticket::create([
            'id' => 'TE00000003',
            'nombre' => 'Recoverable Ticket',
            'fecha_evento' => '2026-05-10',
            'tipo_evento' => 'feria',
            'escaneado' => false,
            'customer_id' => null,
        ]);

        $secret = env('APP_QR_SECRET', 'CAMBIAR_EN_PRODUCCION');
        $issuedAt = 0;
        $nonce = 'STATIC';
        $payload = $ticket->id . '|' . $ticket->nombre . '|' . $issuedAt . '|' . $nonce;
        $digest = hash_hmac('sha256', $payload, $secret, true);
        $signature = rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
        $encodedName = rtrim(strtr(base64_encode($ticket->nombre), '+/', '-_'), '=');
        $qrText = implode('|', ['FERIAQR', $ticket->id, $encodedName, (string) $issuedAt, $nonce, $signature]);

        $scanResponse = $this->postJson('/api/scan/validate', [
            'qrText' => $qrText,
        ]);

        $scanResponse->assertStatus(200);
        $scanResponse->assertJsonFragment(['valid' => true]);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'escaneado' => true]);
        $this->assertDatabaseHas('scan_log', ['ticket_id' => $ticket->id]);

        $recoverResponse = $this->postJson('/api/scan/recover', [
            'key' => 'RECUPERAR-2026',
            'ticketId' => $ticket->id,
        ]);

        $recoverResponse->assertStatus(200);
        $recoverResponse->assertJsonFragment(['id' => $ticket->id, 'escaneado' => false]);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'escaneado' => false]);
        $this->assertDatabaseMissing('scan_log', ['ticket_id' => $ticket->id]);
    }
}
