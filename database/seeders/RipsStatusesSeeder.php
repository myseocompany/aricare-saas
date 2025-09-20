<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RipsStatusesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Siembra/actualiza los 5 estados canónicos con IDs fijos
            DB::table('rips_statuses')->upsert([
                [
                    'id' => 1,
                    'name' => 'Incompleto',
                    'description' => 'El RIPS ha sido creado y está en fase de diligenciamiento.',
                ],
                [
                    'id' => 2,
                    'name' => 'Listo',
                    'description' => 'Tiene diagnóstico o procedimiento. Y si tiene FEV tiene XML',
                ],
                [
                    'id' => 3,
                    'name' => 'SinEnviar',
                    'description' => 'El RIPS está listo pero no fue incluido en el envío.',
                ],
                [
                    'id' => 4,
                    'name' => 'Aceptado',
                    'description' => 'El RIPS fue aceptado por SISPRO.',
                ],
                [
                    'id' => 5,
                    'name' => 'Rechazado',
                    'description' => 'El RIPS fue rechazado por SISPRO.',
                ],
            ], ['id'], ['name', 'description']);

            // (Opcional) limpia estados no usados para que la tabla quede igual a la de la nube
            DB::table('rips_statuses')->whereNotIn('id', [1, 2, 3, 4, 5])->delete();
        });
    }
}
