<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Ticket::query()->upsert([
            ['id' => 'AN00000001', 'nombre' => 'Ana Perez', 'fecha_evento' => '2026-05-10', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'LU00000002', 'nombre' => 'Luis Soto', 'fecha_evento' => '2026-05-11', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'MA00000003', 'nombre' => 'Maria Gomez', 'fecha_evento' => '2026-05-12', 'escaneado' => false, 'customer_id' => null],
            ['id' => 'JO00000004', 'nombre' => 'Jorge Ruiz', 'fecha_evento' => '2026-05-13', 'escaneado' => false, 'customer_id' => null],
        ], ['id'], ['nombre', 'fecha_evento', 'escaneado', 'customer_id']);
    }
}
