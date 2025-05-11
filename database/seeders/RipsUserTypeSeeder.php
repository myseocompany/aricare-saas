<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsUserType;

class RipsUserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_user_types.csv');
        $rows = array_map('str_getcsv', file($path));
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsUserType::create([
                'id' => trim($row[0]),
                'name' => trim($row[1]),
            ]);
        }
    }
}
