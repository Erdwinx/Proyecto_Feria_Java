<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            ['nombre' => 'Concierto Central', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-05-10'],
            ['nombre' => 'Concierto Central', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-05-14'],
            ['nombre' => 'Concierto Central', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-05-18'],
            ['nombre' => 'Feria Local', 'tipo_evento' => 'feria', 'fecha_evento' => '2026-05-11'],
            ['nombre' => 'Feria Local', 'tipo_evento' => 'feria', 'fecha_evento' => '2026-05-12'],
            ['nombre' => 'Feria Local', 'tipo_evento' => 'feria', 'fecha_evento' => '2026-05-13'],
            ['nombre' => 'Feria Local', 'tipo_evento' => 'feria', 'fecha_evento' => '2026-05-17'],
            ['nombre' => 'Ritmo Urbano', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-15'],
            ['nombre' => 'Ritmo Urbano', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-21'],
            ['nombre' => 'Ritmo Urbano', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-28'],
            ['nombre' => 'Electro Fest', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-19'],
            ['nombre' => 'Electro Fest', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-23'],
            ['nombre' => 'Electro Fest', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-27'],
            ['nombre' => 'Noche de Pop', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-17'],
            ['nombre' => 'Noche de Pop', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-20'],
            ['nombre' => 'Noche de Pop', 'tipo_evento' => 'concierto', 'fecha_evento' => '2026-06-24'],
        ];

        foreach ($events as $ev) {
            DB::table('events')->updateOrInsert(
                ['fecha_evento' => $ev['fecha_evento']],
                $ev
            );
        }
    }
}
