<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RipsStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rips_statuses')->insert([
            ['name' => 'Creado', 'description' => 'El RIPS ha sido creado y está en fase de diligenciamiento.'],
            ['name' => 'En Construcción', 'description' => 'El RIPS está siendo completado y validado internamente.'],
            ['name' => 'Validado', 'description' => 'El RIPS ha pasado las validaciones iniciales.'],
            ['name' => 'En Espera de Envío', 'description' => 'El RIPS está listo para ser enviado al sistema del Ministerio de Salud.'],
            ['name' => 'Enviado', 'description' => 'El RIPS ha sido enviado al Ministerio de Salud o la DIAN para validación única.'],
            ['name' => 'Validación Exitosa', 'description' => 'El RIPS ha sido validado correctamente.'],
            ['name' => 'Validación Fallida', 'description' => 'El RIPS ha fallado la validación y necesita correcciones.'],
            ['name' => 'Rechazado', 'description' => 'El RIPS ha sido rechazado y no es válido.'],
            ['name' => 'Aceptado', 'description' => 'El RIPS ha sido aceptado y está listo para su uso en el proceso de cobro.'],
        ]);
    }
}
