<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class Diagnoses
{
    public static function make(): Repeater
    {
        return Repeater::make('diagnoses')
            ->label('Diagnósticos')
            ->relationship('diagnoses') // 👈 MUY IMPORTANTE
            ->schema([
                Select::make('cie10_id')
                    ->label('Diagnóstico CIE10')
                    ->options(\App\Models\Cie10::all()->pluck('description', 'id'))
                    ->searchable()
                    ->placeholder('Seleccione un diagnóstico')
                    ->required(),

                Select::make('rips_diagnosis_type_id')
                    ->label('Tipo de diagnóstico')
                    ->options(\App\Models\RipsDiagnosisType::all()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Seleccione tipo')
                    ->required(),
            ])
            ->defaultItems(1)
            ->minItems(1)
            ->maxItems(4)
            ->columns(2)
            ->columnSpanFull();
    }
}
