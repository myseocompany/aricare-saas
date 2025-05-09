<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoIdentificacionPisis;


class TipoIdentificacionPisisSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tipoidpisis.csv');
        $rows = array_map('str_getcsv', file($path));

        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            TipoIdentificacionPisis::create([
                'codigo' => $row[0],
                'nombre' => $row[1],
            ]);
        }
    }

}
