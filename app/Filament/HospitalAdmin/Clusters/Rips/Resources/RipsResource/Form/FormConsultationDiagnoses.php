<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form;

use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use App\Models\Rips\RipsDiagnosisType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\Selects\Cie10Options;


class FormConsultationDiagnoses{

    public static function schema(bool $isPrincipal = false, int $startSequence = 2): array
    {
    return [


        

    Select::make('cie10_id')
        ->label('Diagnóstico')
        ->searchable()
        ->getSearchResultsUsing(fn ($search) => Cie10Options::getOptions($search))
        ->getOptionLabelUsing(fn ($value) => Cie10Options::getLabel($value))
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
