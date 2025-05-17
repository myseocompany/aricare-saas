<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsTechnologyPurposes;

class RipsTechnologyPurposesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_technology_purposes.csv');
        $rows = array_map('str_getcsv', file($path));
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsTechnologyPurposes::updateOrCreate(
                ['code' => (int) trim($row[0])],
                ['name' => ucfirst(strtolower(trim($row[1])))]
            );
        }
    }
}
