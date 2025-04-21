<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/CUPS.csv'); // Asegúrate de subirlo ahí

        if (!File::exists($path)) {
            throw new \Exception("El archivo CUPS.csv no se encuentra en storage/app");
        }

        $file = fopen($path, 'r');
        $headers = fgetcsv($file, 0, ';'); // Saltamos encabezados

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            DB::table('cups')->insert([
                'code' => $row[1],
                'name' => $row[2],
                'description' => $row[3],
                'group' => $row[8],             // SUBCATEGORIA
                'subgroup_code' => $row[9],     // Código tipo 01.0.1.01
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($file);
    }
}
