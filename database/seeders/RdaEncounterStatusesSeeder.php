<?php

namespace Database\Seeders;

use App\Models\Rda\EncounterStatus;
use Illuminate\Database\Seeder;

class RdaEncounterStatusesSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'planned', 'name' => 'Planificado'],
            ['code' => 'in-progress', 'name' => 'En curso'],
            ['code' => 'finished', 'name' => 'Finalizado'],
            ['code' => 'cancelled', 'name' => 'Cancelado'],
        ];

        foreach ($statuses as $status) {
            EncounterStatus::updateOrCreate(
                ['code' => $status['code']],
                [
                    'name' => $status['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
