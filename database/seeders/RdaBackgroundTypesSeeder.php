<?php

namespace Database\Seeders;

use App\Models\Rda\BackgroundType;
use Illuminate\Database\Seeder;

class RdaBackgroundTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'personal_patologico', 'name' => 'Personal patológico'],
            ['code' => 'no_patologico', 'name' => 'No patológico'],
            ['code' => 'quirurgico', 'name' => 'Quirúrgico'],
            ['code' => 'farmacologico', 'name' => 'Farmacológico'],
            ['code' => 'alergico', 'name' => 'Alérgico'],
            ['code' => 'familiar', 'name' => 'Familiar'],
            ['code' => 'gineco_obstetrico', 'name' => 'Gineco-obstétrico'],
            ['code' => 'ocupacional', 'name' => 'Ocupacional'],
            ['code' => 'epidemiologico', 'name' => 'Epidemiológico'],
            ['code' => 'psicologico', 'name' => 'Psicológico'],
        ];

        foreach ($types as $type) {
            BackgroundType::updateOrCreate(
                ['code' => $type['code']],
                ['name' => $type['name'], 'is_active' => true]
            );
        }
    }
}
