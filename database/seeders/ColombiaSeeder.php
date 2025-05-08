<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\DepartmentCountry;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Exception;

class ColombiaSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Municipality::truncate();
            DepartmentCountry::truncate();
            Country::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Leer el archivo JSON
            $json = file_get_contents(storage_path('app/data/colombia.json'));
            $dataArray = json_decode($json, true);

            // Validar JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error en la decodificación del JSON: " . json_last_error_msg());
            }

            // Validar que hay al menos un país
            if (empty($dataArray) || !isset($dataArray[0]['name']) || !isset($dataArray[0]['states'])) {
                throw new Exception("Datos incompletos en el archivo JSON.");
            }

            $data = $dataArray[0]; // 👈 Toma el primer (y único) país del arreglo

            // Crear país
            $country = Country::create([
                'name' => $data['name'],
                'code' => $data['iso3'] ?? null,
                'is_active' => true,
            ]);

            // Crear departamentos y municipios
            foreach ($data['states'] as $state) {
                if (empty($state['name']) || empty($state['cities'])) {
                    continue;
                }

                $department = DepartmentCountry::create([
                    'name' => $state['name'],
                    'country_id' => $country->id,
                    'is_active' => true,
                ]);

                foreach ($state['cities'] as $city) {
                    if (empty($city['name'])) {
                        continue;
                    }

                    Municipality::create([
                        'name' => $city['name'],
                        'department_country_id' => $department->id,
                        'is_active' => true,
                    ]);
                }
            }

            echo "Seeder ejecutado exitosamente\n";

        } catch (Exception $e) {
            echo "Error al ejecutar el seeder: " . $e->getMessage() . "\n";
        }
    }
}
