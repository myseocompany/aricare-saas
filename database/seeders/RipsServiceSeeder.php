<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsService;
use App\Models\RipsServiceGroup;

class RipsServiceSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_services.csv');
        $rows = array_map('str_getcsv', file($path));
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 3) continue;

            $code = trim($row[0]);
            $name = ucfirst(strtolower(trim($row[1])));
            $groupName = strtoupper(trim($row[2]));

            $normalizedGroup = match (true) {
                str_contains($groupName, 'CONSULTA') => 'Consulta externa',
                str_contains($groupName, 'DIAGNOSTICO') => 'Apoyo diagnóstico y complementación terapéutica',
                str_contains($groupName, 'INTERNACION') => 'Internación',
                str_contains($groupName, 'QUIRURGICO') => 'Quirúrgico',
                str_contains($groupName, 'ATENCION') => 'Atención inmediata',
                default => null,
            };

            if (!$normalizedGroup) continue;

            $group = RipsServiceGroup::where('name', $normalizedGroup)->first();
            if (!$group) continue;

            RipsService::create([
                'code' => $code,
                'name' => $name,
                'rips_service_group_id' => $group->id,
            ]);
        }
    }
}
