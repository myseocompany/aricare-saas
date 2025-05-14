<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RipsServiceGroup;

class RipsServiceGroupSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_service_group.csv');
        $rows = array_map('str_getcsv', file($path));

        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsServiceGroup::create([
                
                
                'name' => $row[1],
            ]);
        }
    }
}
