<?php

namespace Database\Seeders;

use App\Models\RipsCollectionConcept;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RipsCollectionConceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/rips_collection_concept.csv');
        $rows = array_map('str_getcsv', file($path));

        // Saltar encabezado
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            RipsCollectionConcept::create([
                'code' => $row[0],
                'name' => $row[1],
            ]);
        }
    }
}
