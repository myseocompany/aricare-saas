<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use App\Models\Rips\RipsDiagnosisType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


class FormConsultationDiagnoses{

    public static function schema(bool $isPrincipal = false, int $startSequence = 2): array
    {
    return [


        
        Select::make('cie10_id')
            ->label('Diagnóstico')
            ->searchable()
            ->options(fn (string $search = null) =>
                \App\Models\Rips\Cie10::query()
                    ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
                    ->limit(20)
                    ->pluck('description', 'id')
            )
            ->required()
            ->columnSpan(1),
        
        Select::make('rips_diagnosis_type_id')
            ->label('Tipo de Diagnóstico')
            ->options(RipsDiagnosisType::pluck('name', 'id'))
            ->visible($isPrincipal)
            ->required($isPrincipal)
            ->columnSpan(1),
    ];
}

}
