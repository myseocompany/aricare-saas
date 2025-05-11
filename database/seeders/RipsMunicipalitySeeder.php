<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsMunicipality;
use Illuminate\Support\Facades\File;

class RipsMunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_municipalities.csv');

        if (!File::exists($path)) {
            throw new \Exception("Archivo CSV no encontrado en $path");
        }

        $rows = array_map('str_getcsv', file($path));
        foreach ($rows as $row) {
            if (count($row) < 2) {
                continue;
            }

            RipsMunicipality::updateOrCreate(
                ['code' => (int)$row[0]],
                ['name' => trim($row[1])]
            );
        }
    }
}
