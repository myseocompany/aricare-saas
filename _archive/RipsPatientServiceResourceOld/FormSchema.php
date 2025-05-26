<?php
namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;

class FormSchema
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Select::make('patient_id')
                ->label('Paciente')
                ->relationship('patient', 'id') // o usa ->options() si prefieres
                ->required(),

            Select::make('doctor_id')
                ->label('Doctor')
                ->relationship('doctor', 'id')
                ->required(),

            Toggle::make('has_incapacity')
                ->label('¿Tiene incapacidad?')
                ->default(false)
                ->required(),


            DateTimePicker::make('service_datetime')
                ->label('Fecha de atención')
                ->default(fn () => now())
                ->required(),

        ]);
    }
}

