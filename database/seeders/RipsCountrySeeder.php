<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsCountry;

class RipsCountrySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_countries.csv');
        $rows = array_map('str_getcsv', file($path));
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 4) continue;

            RipsCountry::create([
                'code' => $row[0],
                'name' => $row[1],
                'alpha2' => $row[2],
                'alpha3' => $row[3],
            ]);
        }
    }
}
