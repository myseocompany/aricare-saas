<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form;

use Filament\Forms\Components\Select;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form\Selects\Cie10Options;


class FormConsultationSimpleDiagnoses
{
    public static function schema(bool $isPrincipal = false): Select
    {
        return 

Select::make('cie10_id')
    ->label('Diagnóstico')
    ->searchable()
    ->getSearchResultsUsing(fn ($search) => Cie10Options::getOptions($search))
    ->getOptionLabelUsing(fn ($value) => Cie10Options::getLabel($value))
    ->required();

    }
}
