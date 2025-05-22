<?php
namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;

use Filament\Forms\Form;

use App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections\{
    GeneralInfo,
    Diagnoses,
    Consultations,
    Procedures
};

class FormSchema
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            ...GeneralInfo::make(),
            ...Consultations::make(),
            ...Procedures::make(),
        ]);
    }
}
