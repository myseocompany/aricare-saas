<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;
use App\Models\Country; // Importa el modelo Country

class MunicipalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipalitiesByCountryCode = [
            'COL' => [
                'Bogotá D.C.', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena',
                'Bucaramanga', 'Manizales', 'Pereira', 'Cúcuta', 'Santa Marta'
            ],
            'USA' => [
                'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'
            ],
            'MEX' => [
                'Ciudad de México', 'Guadalajara', 'Monterrey', 'Puebla', 'Tijuana'
            ],
            'ESP' => [
                'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Zaragoza'
            ],
            'ARG' => [
                'Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'La Plata'
            ],
            'PAN' => [
                'Ciudad de Panamá', 'San Miguelito', 'David', 'La Chorrera', 'Colón'
            ],
            'BRA' => [
                'São Paulo', 'Río de Janeiro', 'Brasilia', 'Salvador', 'Belo Horizonte'
            ],
        ];

        foreach ($municipalitiesByCountryCode as $countryCode => $municipalities) {
            $country = Country::where('code', $countryCode)->first();

            if ($country) {
                foreach ($municipalities as $name) {
                    Municipality::create([
                        'name' => $name,
                        'country_id' => $country->id,
                    ]);
                }
            }
        }
    }
}
