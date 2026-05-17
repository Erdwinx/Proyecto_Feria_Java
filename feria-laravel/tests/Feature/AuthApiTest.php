<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register()
    {
        $response = $this->postJson('/api/customers/register', [
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'customer']);
        $this->assertDatabaseHas('customers', ['email' => 'test@example.com']);
    }

    public function test_register_fails_with_duplicate_email()
    {
        Customer::create([
            'nombre' => 'Existing User',
            'email' => 'existing@example.com',
            'password_hash' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/customers/register', [
            'nombre' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'El correo ya esta registrado']);
    }

    public function test_register_rejects_missing_required_fields()
    {
        $response = $this->postJson('/api/customers/register', [
            'nombre' => 'Test User',
            'email' => 'bad@example.com',
            // missing password
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Datos inválidos']);
    }

    public function test_customer_can_login()
    {
        $customer = Customer::create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password_hash' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/customers/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'customer']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        Customer::create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password_hash' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/customers/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'Credenciales invalidas']);
    }
}
