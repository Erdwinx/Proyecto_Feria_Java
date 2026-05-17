<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTicketTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nombre' => 'Test Customer',
            'email' => 'customer@example.com',
            'password_hash' => bcrypt('password123'),
        ]);

        // Get token from login
        $response = $this->postJson('/api/customers/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        $this->token = $response->json('token');
    }

    public function test_customer_can_purchase_fair_tickets()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-05-10',
                'items' => [
                    [
                        'name' => 'Boleto GENERAL',
                        'category' => 'general',
                        'seatNumbers' => [],
                        'price' => 100,
                        'qty' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '0' => ['id', 'nombre', 'fechaEvento', 'tipoEvento', 'escaneado'],
        ]);
        $this->assertDatabaseHas('tickets', ['tipo_evento' => 'feria']);
    }

    public function test_customer_can_purchase_concert_package()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '0' => ['id', 'nombre', 'fechaEvento', 'tipoEvento', 'packageId', 'seatNumbers'],
        ]);
        $this->assertDatabaseHas('packages', ['tipo_evento' => 'concierto']);
        $this->assertDatabaseHas('tickets', ['tipo_evento' => 'concierto', 'package_id' => $response->json('0.packageId')]);
    }

    public function test_purchase_requires_authentication()
    {
        $response = $this->postJson('/api/customers/tickets', [
            'nombre' => 'Test Customer',
            'fechaEvento' => '2026-05-10',
            'items' => [],
        ]);

        $response->assertStatus(401);
    }

    public function test_purchase_validates_required_fields()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-05-10',
                // Missing items
            ]);

        $response->assertStatus(422);
    }

    public function test_concert_purchase_rejects_already_sold_seats()
    {
        $firstPurchase = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $firstPurchase->assertStatus(200);

        $secondPurchase = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $secondPurchase->assertStatus(409);
        $secondPurchase->assertJsonFragment([
            'message' => 'Uno o más asientos ya no están disponibles',
        ]);
    }

    public function test_available_seats_endpoint_hides_sold_concert_seats()
    {
        $purchase = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $purchase->assertStatus(200);

        $availability = $this->getJson('/api/tickets/available-seats?fechaEvento=2026-06-15&category=vip');

        $availability->assertStatus(200);
        $availability->assertJsonFragment([
            'category' => 'vip',
            'tipoEvento' => 'concierto',
        ]);

        $availableSeats = $availability->json('availableSeats');
        $soldSeats = $availability->json('soldSeats');

        $this->assertIsArray($availableSeats);
        $this->assertIsArray($soldSeats);
        $this->assertNotContains('V1', $availableSeats);
        $this->assertContains('V1', $soldSeats);
        $this->assertContains('V4', $soldSeats);
    }

    public function test_seats_bought_by_one_customer_are_unavailable_to_another_customer()
    {
        $firstPurchase = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $firstPurchase->assertStatus(200);

        $secondCustomer = Customer::create([
            'nombre' => 'Second Customer',
            'email' => 'second@example.com',
            'password_hash' => bcrypt('password123'),
        ]);

        $secondLogin = $this->postJson('/api/customers/login', [
            'email' => 'second@example.com',
            'password' => 'password123',
        ]);

        $secondToken = $secondLogin->json('token');
        $this->assertNotEmpty($secondToken);

        $availability = $this->withHeader('Authorization', 'Bearer ' . $secondToken)
            ->getJson('/api/tickets/available-seats?fechaEvento=2026-06-15&category=vip');

        $availability->assertStatus(200);

        $availableSeats = $availability->json('availableSeats');
        $soldSeats = $availability->json('soldSeats');

        $this->assertNotContains('V1', $availableSeats);
        $this->assertNotContains('V4', $availableSeats);
        $this->assertContains('V1', $soldSeats);
        $this->assertContains('V4', $soldSeats);

        $secondPurchase = $this->withHeader('Authorization', 'Bearer ' . $secondToken)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Second Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V1, V2, V3, V4',
                        'category' => 'vip',
                        'seatNumbers' => ['V1', 'V2', 'V3', 'V4'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $secondPurchase->assertStatus(409);
        $secondPurchase->assertJsonFragment([
            'message' => 'Uno o más asientos ya no están disponibles',
        ]);
    }

    public function test_available_seats_endpoint_supports_grada_for_concerts()
    {
        $purchase = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto GRADA - Asientos A1, A2, A3, A4',
                        'category' => 'grada',
                        'seatNumbers' => ['A1', 'A2', 'A3', 'A4'],
                        'price' => 180,
                        'qty' => 1,
                    ],
                ],
            ]);

        $purchase->assertStatus(200);

        $availability = $this->getJson('/api/tickets/available-seats?fechaEvento=2026-06-15&category=grada');

        $availability->assertStatus(200);
        $availability->assertJsonFragment([
            'category' => 'grada',
            'tipoEvento' => 'concierto',
        ]);

        $allowedCategories = $availability->json('allowedCategories');
        $availableSeats = $availability->json('availableSeats');
        $soldSeats = $availability->json('soldSeats');

        $this->assertSame(['grada', 'vip'], $allowedCategories);
        $this->assertIsArray($availableSeats);
        $this->assertIsArray($soldSeats);
        $this->assertNotContains('A1', $availableSeats);
        $this->assertContains('A1', $soldSeats);
        $this->assertContains('A4', $soldSeats);
    }

    public function test_customer_can_enqueue_purchase_and_check_status()
    {
        config(['queue.default' => 'sync']);

        $queued = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/customers/tickets/queue', [
                'nombre' => 'Test Customer',
                'fechaEvento' => '2026-06-15',
                'items' => [
                    [
                        'name' => 'Boleto VIP - Asientos V9, V10, V11, V12',
                        'category' => 'vip',
                        'seatNumbers' => ['V9', 'V10', 'V11', 'V12'],
                        'price' => 250,
                        'qty' => 1,
                    ],
                ],
            ]);

        $queued->assertStatus(202);
        $requestId = $queued->json('requestId');
        $this->assertNotEmpty($requestId);

        $status = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/customers/tickets/queue/' . $requestId);

        $status->assertStatus(200);
        $status->assertJsonFragment(['status' => 'completed']);
        $this->assertIsArray($status->json('result.ticketIds'));
    }
}
