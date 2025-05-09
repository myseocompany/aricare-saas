<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zona;


class ZonaSeeder extends Seeder
{
    public function run(): void
{
    $path = database_path('seeders/data/zonaversion2.csv');
    $rows = array_map('str_getcsv', file($path));

    $headerSkipped = false;

    foreach ($rows as $row) {
        if (!$headerSkipped) {
            $headerSkipped = true;
            continue;
        }

        if (count($row) < 2) continue;

        Zona::create([
            'codigo' => $row[0],
            'nombre' => $row[1],
        ]);
    }
}

}
