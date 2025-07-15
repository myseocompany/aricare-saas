<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rips\RipsTerritorialZoneType;


class RipsTerritorialZoneTypeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_territorial_zone_types.csv');
        $rows = array_map('str_getcsv', file($path));

        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsTerritorialZoneType::create([
                'code' => $row[0],
                'name' => $row[1],
            ]);
        }
    }
}
