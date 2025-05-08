<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CupsTableSeeder extends Seeder
{
    public function run(): void
{
    $path = database_path('seeders/data/cups.csv');

    if (!File::exists($path)) {
        throw new \Exception("El archivo cups.csv no se encuentra en database/seeders/data");
    }

    $file = fopen($path, 'r');
    $headers = fgetcsv($file, 0, ',');

    $count = 0;

    while (($row = fgetcsv($file, 0, ',')) !== false) {
        
        if (!isset($row[1], $row[2], $row[3], $row[8], $row[9])) continue;

        DB::table('cups')->insert([
            'code' => trim($row[1]),
            'name' => trim($row[2]),
            'description' => trim($row[3]),
            'group' => trim($row[8]),
            'subgroup_code' => trim($row[9]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $count++;
    }

    fclose($file);

    $this->command->info("Se importaron $count códigos CUPS correctamente.");
}

}
