<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RipsDepartment;
use App\Models\RipsMunicipality;
use Illuminate\Support\Facades\File;

class RipsDepartmentMunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/rips_departments_municipalities.csv');

        if (!File::exists($path)) {
            throw new \Exception("Archivo CSV no encontrado en $path");
        }

        $rows = array_map('str_getcsv', file($path));
        array_shift($rows); // omitir cabecera

        foreach ($rows as $row) {
            if (count($row) < 5) {
                continue;
            }

            $departmentCode = (int) trim($row[1]);
            $departmentName = trim($row[2]);

            $department = RipsDepartment::updateOrCreate(
                ['code' => $departmentCode],
                [
                    'name' => $departmentName,
                    'rips_country_id' => 48, // Colombia
                ]
            );

            $municipalityCode = (int) str_replace('.', '', trim($row[3]));
            $municipalityName = trim($row[4]);

            RipsMunicipality::updateOrCreate(
                ['code' => $municipalityCode],
                ['name' => $municipalityName, 'rips_department_id' => $department->id]
            );
        }
    }
}
