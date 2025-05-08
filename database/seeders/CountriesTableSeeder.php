<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country; // Importa el modelo Country

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Colombia', 'code' => 'COL'],
            ['name' => 'Estados Unidos', 'code' => 'USA'],
            ['name' => 'México', 'code' => 'MEX'],
            ['name' => 'España', 'code' => 'ESP'],
            ['name' => 'Argentina', 'code' => 'ARG'],
            ['name' => 'Panamá', 'code' => 'PAN'],
            ['name' => 'Brasil', 'code' => 'BRA'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
