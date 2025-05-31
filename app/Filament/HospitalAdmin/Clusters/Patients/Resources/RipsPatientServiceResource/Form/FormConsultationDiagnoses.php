<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class FormConsultationDiagnoses
{
    public static function schema(): array
    {
        return [
            Forms\Components\Repeater::make('diagnoses')
                ->relationship('diagnoses') // relación en el modelo
                ->label('Diagnósticos')
                ->schema([
                    Forms\Components\Select::make('cie10_id')
                        ->label('Diagnóstico CIE10')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
                                ->limit(20)
                                ->pluck('description', 'id');
                        })
                        ->required(),

                    Forms\Components\Select::make('sequence')
                        ->label('Secuencia')
                        ->options([
                            1 => 'Principal',
                            2 => 'Relacionado 1',
                            3 => 'Relacionado 2',
                            4 => 'Relacionado 3',
                        ])
                        ->required(),
                ])
                ->columns(2)
                ->createItemButtonLabel('Añadir diagnóstico'),
        ];
    }
}
