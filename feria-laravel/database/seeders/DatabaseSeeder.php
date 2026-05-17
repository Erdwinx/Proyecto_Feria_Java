<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\EventSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        Customer::query()->firstOrCreate(
            ['email' => 'test@feriapass.local'],
            [
                'nombre' => 'Usuario Test',
                'password_hash' => Hash::make('secret123'),
            ]
        );

        // Crear paquetes de conciertos
        $packages = [
            [
                'id' => 'CO00000001',
                'nombre' => 'Concierto Main Stage',
                'tipo_evento' => 'concierto',
                'fecha_evento' => '2026-06-15',
                'qr_text' => null,
                'qr_generated_at' => null,
            ],
            [
                'id' => 'CO00000002',
                'nombre' => 'Concierto VIP Lounge',
                'tipo_evento' => 'concierto',
                'fecha_evento' => '2026-06-16',
                'qr_text' => null,
                'qr_generated_at' => null,
            ],
        ];

        foreach ($packages as $package) {
            Package::query()->firstOrCreate(['id' => $package['id']], $package);
        }

        // Seed events (dates and types)
        $this->call(EventSeeder::class);

        // Crear boletos de eventos de feria (sin paquete)
        Ticket::query()->upsert([
            ['id' => 'AN00000001', 'nombre' => 'Ana Perez', 'fecha_evento' => '2026-05-10', 'tipo_evento' => 'feria', 'package_id' => null, 'escaneado' => false, 'customer_id' => null],
            ['id' => 'LU00000002', 'nombre' => 'Luis Soto', 'fecha_evento' => '2026-05-11', 'tipo_evento' => 'feria', 'package_id' => null, 'escaneado' => false, 'customer_id' => null],
            ['id' => 'MA00000003', 'nombre' => 'Maria Gomez', 'fecha_evento' => '2026-05-12', 'tipo_evento' => 'feria', 'package_id' => null, 'escaneado' => false, 'customer_id' => null],
            ['id' => 'JO00000004', 'nombre' => 'Jorge Ruiz', 'fecha_evento' => '2026-05-13', 'tipo_evento' => 'feria', 'package_id' => null, 'escaneado' => false, 'customer_id' => null],
            
            // Boletos de paquetes de conciertos
            ['id' => 'CO00000101', 'nombre' => 'Juan Martinez', 'fecha_evento' => '2026-06-15', 'tipo_evento' => 'concierto', 'package_id' => 'CO00000001', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'CO00000102', 'nombre' => 'Elena Rodriguez', 'fecha_evento' => '2026-06-15', 'tipo_evento' => 'concierto', 'package_id' => 'CO00000001', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'CO00000103', 'nombre' => 'Carlos Hernandez', 'fecha_evento' => '2026-06-15', 'tipo_evento' => 'concierto', 'package_id' => 'CO00000001', 'escaneado' => false, 'customer_id' => null],
            
            ['id' => 'CO00000201', 'nombre' => 'Sophia Lopez', 'fecha_evento' => '2026-06-16', 'tipo_evento' => 'concierto', 'package_id' => 'CO00000002', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'CO00000202', 'nombre' => 'Diego Morales', 'fecha_evento' => '2026-06-16', 'tipo_evento' => 'concierto', 'package_id' => 'CO00000002', 'escaneado' => false, 'customer_id' => null],
        ], ['id'], ['nombre', 'fecha_evento', 'tipo_evento', 'package_id', 'escaneado', 'customer_id']);
    }
}
