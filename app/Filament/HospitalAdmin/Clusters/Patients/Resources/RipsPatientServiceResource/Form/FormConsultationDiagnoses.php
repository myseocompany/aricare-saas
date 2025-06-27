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
            ->getSearchResultsUsing(function (string $search) {
                return \App\Models\Rips\Cie10::query()
                    ->where('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($d) => [$d->id => "{$d->code} - {$d->description}"]);
            })
            ->getOptionLabelUsing(function ($value): ?string {
                $d = \App\Models\Rips\Cie10::find($value);
                return $d ? "{$d->code} - {$d->description}" : null;
            })
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
