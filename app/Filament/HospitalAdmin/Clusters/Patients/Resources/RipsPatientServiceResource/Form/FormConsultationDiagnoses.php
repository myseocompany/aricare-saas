<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use App\Models\Rips\RipsDiagnosisType;

class FormConsultationDiagnoses
{
    public static function schema(): array
    {
        return [
            Forms\Components\Repeater::make('diagnoses')
                ->relationship('diagnoses')
                ->label('Diagnósticos')
                ->minItems(1)
                ->maxItems(4)
                ->schema([
                    Forms\Components\Select::make('cie10_id')
                        ->label('Diagnóstico')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
                                ->limit(20)
                                ->pluck('description', 'id');
                        })
                        ->required(),

                    Forms\Components\Hidden::make('sequence')
                        ->default(fn (Get $get) => 
                            count($get('diagnoses') ?? []) === 0 ? 1 : (count($get('diagnoses')) + 1)
                        ),

                    Forms\Components\Placeholder::make('sequence_label')
                        ->content(fn (Get $get) => 
                            $get('sequence') == 1 ? 'Principal' : 'Relacionado ' . ($get('sequence') - 1)
                        ),

                    Forms\Components\Select::make('rips_diagnosis_type_id')
                        ->label('Tipo de Diagnóstico')
                        ->options(RipsDiagnosisType::all()->pluck('name', 'id'))
                        ->visible(fn (Get $get) => $get('sequence') === 1) // Solo visible para Principal
                        ->required(fn (Get $get) => $get('sequence') === 1), // Solo requerido si es Principal
                ])
                ->columns(2)
                ->createItemButtonLabel('Añadir diagnóstico'),
        ];
    }
}
