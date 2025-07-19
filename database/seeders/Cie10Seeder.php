<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Cie10Seeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/cie10.csv');

        if (!File::exists($path)) {
            throw new \Exception("El archivo cie10.csv no se encuentra en database/seeders/data");
        }

        $file = fopen($path, 'r');
        $headers = fgetcsv($file, 0, ','); // Saltamos encabezados

        $count = 0;

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            if (!isset($row[1], $row[2])) continue;

            DB::table('cie10')->updateOrInsert(
                ['code' => trim($row[1])],
                [
                    'description' => ucwords(strtolower(trim($row[2]))),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $count++;
        }

        fclose($file);

        $this->command->info("Se importaron o actualizaron $count códigos CIE10 correctamente.");
    }
}
