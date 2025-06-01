<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rips\RipsPayerType;

class RipsPayerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'EPS', 'description' => 'Entidad Promotora de Salud'],
            ['name' => 'EAPB', 'description' => 'Entidad Adaptada'],
            ['name' => 'Empresa', 'description' => 'Empresa privada o contratante'],
            ['name' => 'ARL', 'description' => 'Administradora de Riesgos Laborales'],
            ['name' => 'SOAT', 'description' => 'Seguro Obligatorio de Accidentes de Tránsito'],
            ['name' => 'ADRES', 'description' => 'Administradora de Recursos del Sistema de Salud'],
            ['name' => 'Secretaría de Salud', 'description' => 'Entidad Territorial de Salud'],
            ['name' => 'Plan Voluntario de Salud', 'description' => 'Medicina prepagada o plan voluntario de salud'],
        ];

        foreach ($types as $type) {
            RipsPayerType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}
