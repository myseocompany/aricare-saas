<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form\BasicForm;
use Filament\Forms\Form;

class RipsPatientServiceForm
{
    public static function form(Form $form): Form
    {
        return BasicForm::make($form);
    }
}
