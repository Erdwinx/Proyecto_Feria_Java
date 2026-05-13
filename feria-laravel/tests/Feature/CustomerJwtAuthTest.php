<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerJwtAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_register_returns_jwt_and_customer_data(): void
    {
        $response = $this->postJson('/api/customers/register', [
            'nombre' => 'Ana Perez',
            'email' => 'ana@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'customer' => ['id', 'nombre', 'email'],
            ]);
    }

    public function test_customers_me_requires_authentication(): void
    {
        $this->getJson('/api/customers/me')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'No autenticado',
            ]);
    }

    public function test_customer_can_fetch_profile_and_purchase_with_jwt(): void
    {
        $register = $this->postJson('/api/customers/register', [
            'nombre' => 'Luis Soto',
            'email' => 'luis@example.com',
            'password' => 'secret123',
        ]);

        $token = $register->json('token');

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/customers/me')
            ->assertStatus(200)
            ->assertJson([
                'nombre' => 'Luis Soto',
                'email' => 'luis@example.com',
            ]);

        $purchase = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/customers/tickets', [
            'nombre' => 'Boleto General',
            'fechaEvento' => '2026-06-20',
        ]);

        $purchase
            ->assertStatus(200)
            ->assertJsonFragment([
                'nombre' => 'Boleto General',
                'fechaEvento' => '2026-06-20',
                'escaneado' => false,
            ]);
    }
}
