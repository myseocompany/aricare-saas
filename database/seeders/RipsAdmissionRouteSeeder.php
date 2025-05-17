<?php

namespace Database\Seeders;

use App\Models\RipsAdmissionRoute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RipsAdmissionRouteSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/rips_service_reasons.csv');
        $rows = array_map('str_getcsv', file($path));

        // Saltar encabezado
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsAdmissionRoute::create([
                'code' => $row[0],
                'name' => $row[1],
            ]);
        }
    }
}
