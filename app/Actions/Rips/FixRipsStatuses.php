<?php

namespace App\Actions\Rips;

use Illuminate\Support\Facades\DB;

class FixRipsStatuses
{
    public static function run(): void
    {
        DB::transaction(function () {
            DB::table('rips_statuses')->delete();

            DB::table('rips_statuses')->insert([
                ['id' => 1, 'name' => 'Incompleto', 'description' => 'El RIPS ha sido creado y está en fase de diligenciamiento.'],
                ['id' => 2, 'name' => 'Listo',       'description' => 'Tiene diagnóstico o procedimiento. Y si tiene FEV tiene XML'],
                ['id' => 3, 'name' => 'SinEnviar',   'description' => 'El RIPS está listo pero no fue incluido en el envío.'],
                ['id' => 4, 'name' => 'Aceptado',    'description' => 'El RIPS fue aceptado por SISPRO.'],
                ['id' => 5, 'name' => 'Rechazado',   'description' => 'El RIPS fue rechazado por SISPRO.'],
            ]);
        });
    }
}
