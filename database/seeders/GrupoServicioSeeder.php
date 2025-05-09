<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GrupoServicio;


class GrupoServicioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/gruposervicios.csv');
        $rows = array_map('str_getcsv', file($path));

        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            GrupoServicio::create([
                'codigo' => $row[0],
                'nombre' => $row[1],
            ]);
        }
    }
}
