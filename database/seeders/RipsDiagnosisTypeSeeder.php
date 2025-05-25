<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsDiagnosisType;

class RipsDiagnosisTypeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_diagnoses_types.csv');
        $rows = array_map('str_getcsv', file($path));

        $headerSkipped = true;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsDiagnosisType::create([
                'code' => trim($row[0]),
                'name' => trim($row[1]),
            ]);
        }

    }
}
