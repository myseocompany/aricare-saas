<?php

namespace Database\Seeders;

use App\Models\Rda\EncounterType;
use Illuminate\Database\Seeder;

class RdaEncounterTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'AMB', 'name' => 'Ambulatorio'],
            ['code' => 'EMER', 'name' => 'Urgencias'],
            ['code' => 'IMP', 'name' => 'Hospitalización'],
            ['code' => 'VR', 'name' => 'Teleconsulta'],
            ['code' => 'HH', 'name' => 'Atención domiciliaria'],
        ];

        foreach ($types as $type) {
            EncounterType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
