<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class FormPatientDoctor
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('patient_id')
                ->label('Paciente')
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                ->options(function (string $search = null) {
                    $tenantId = Auth::user()->tenant_id;
                    return \App\Models\Patient::query()
                        ->where('tenant_id', $tenantId)
                        ->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->with('user')
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($patient) => [$patient->id => $patient->user?->first_name . ' ' . $patient->user?->last_name]);
                })
                ->required(),

            Forms\Components\Select::make('doctor_id')
                ->label('Doctor')
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                ->options(function (string $search = null) {
                    $tenantId = Auth::user()->tenant_id;
                    return \App\Models\Doctor::query()
                        ->where('tenant_id', $tenantId)
                        ->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->with('user')
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($doctor) => [$doctor->id => $doctor->user?->first_name . ' ' . $doctor->user?->last_name]);
                })
                ->preload()
                ->required(),

            Forms\Components\Hidden::make('tenant_id')
                ->default(Auth::user()->tenant_id)
                ->required(),

            Forms\Components\Toggle::make('has_incapacity')
                ->label('Has incapacity'),

            Forms\Components\DateTimePicker::make('service_datetime')
                ->default(now()) // Fecha por defecto de hoy
                ->required(),
        ]);
    }
}
