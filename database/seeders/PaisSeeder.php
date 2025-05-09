<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/pais.csv'); // o 'data/Pais.csv' si ya lo moviste
        $rows = array_map('str_getcsv', file($path));

        // Saltar encabezado (primera fila)
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 4) continue;

            Pais::create([
                'codigo' => $row[0],
                'nombre' => $row[1],
                'iso2' => $row[2],
                'iso3' => $row[3],
            ]);
        }
    }
}
