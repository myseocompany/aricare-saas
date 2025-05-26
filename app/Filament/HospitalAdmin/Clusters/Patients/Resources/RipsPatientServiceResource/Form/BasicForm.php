<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class BasicForm
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            // Formulario del paciente
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

            // **Formulario de consultas (por encima de los procedimientos)**
            Forms\Components\Repeater::make('consultations')  // Repetidor para agregar múltiples consultas
                ->relationship() // Vincula la relación de consultas
                ->schema([
                    Forms\Components\TextInput::make('consultation_id')
                        ->label('ID de consulta'),

                    Forms\Components\TextInput::make('consultation_description')
                        ->label('Descripción de la consulta'),

                    Forms\Components\Select::make('rips_cups_id')
                        ->label('CUPS')
                        ->searchable() // Habilita la búsqueda en vivo
                        ->options(function (string $search = null) {
                            // Cargar solo los CUPS que coincidan con la búsqueda
                            return \App\Models\Rips\RipsCups::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('name', 'like', "%{$search}%");
                                })
                                ->limit(20) // Limita la cantidad de registros devueltos
                                ->pluck('name', 'id'); // Devuelve solo los campos necesarios
                        })
                        ->required(),

                    Forms\Components\TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_receipt_number')
                        ->label('Número de recibo del copago'),
                ])
                ->columns(2) // Número de columnas para los campos de la consulta
                ->createItemButtonLabel('Añadir consulta'), // Botón para agregar consulta

            // **Formulario de procedimientos (debajo de las consultas)**
            Forms\Components\Repeater::make('procedures')  // Usamos Repeater para agregar múltiples procedimientos
                ->relationship() // Vincula la relación de procedimientos
                ->schema([
                    Forms\Components\TextInput::make('mipres_id')
                        ->label('Mipres ID'),

                    Forms\Components\TextInput::make('authorization_number')
                        ->label('Número de autorización'),

                    Forms\Components\Select::make('rips_cups_id')
                        ->label('CUPS')
                        ->searchable() // Habilita la búsqueda en vivo
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\RipsCups::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('name', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('name', 'id'); // Devuelve solo los campos necesarios
                        })
                        ->required(),

                    Forms\Components\Select::make('cie10_id')
                        ->label('CIE10')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('description', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('description', 'id');
                        }),

                    Forms\Components\Select::make('surgery_cie10_id')
                        ->label('CIE10 Cirugía')
                        ->searchable()
                        ->options(function (string $search = null) {
                            return \App\Models\Rips\Cie10::query()
                                ->when($search, function ($query, $search) {
                                    return $query->where('description', 'like', "%{$search}%");
                                })
                                ->limit(20)
                                ->pluck('description', 'id');
                        }),

                    Forms\Components\TextInput::make('service_value')
                        ->label('Valor del servicio')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_value')
                        ->label('Valor del copago')
                        ->numeric(),

                    Forms\Components\TextInput::make('copayment_receipt_number')
                        ->label('Número de recibo del copago'),
                ])
                ->columns(2) // Número de columnas para los campos del procedimiento
                ->createItemButtonLabel('Añadir procedimiento'), // Botón para agregar procedimiento
        ]);
    }
}
