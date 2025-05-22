<?php

namespace App\Filament\HospitalAdmin\Resources\RipsPatientServiceResource\Sections;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class GeneralInfo
{
    public static function make(): array
    {
        return [
            Select::make('patient_id')
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

            Select::make('doctor_id')
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

            Toggle::make('has_incapacity')
                ->label('¿Tiene incapacidad?'),

            DateTimePicker::make('service_datetime')
                ->label('Fecha y hora de atención')
                ->default(now())
                ->required(),

          
        ];
    }
}
