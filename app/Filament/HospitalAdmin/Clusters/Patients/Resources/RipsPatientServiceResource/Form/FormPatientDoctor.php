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
            Forms\Components\TextInput::make('invoice_number')
                ->label('Número de Factura')
                ->required()
                ->maxLength(30)
                ->rule(function () {
                    return function (string $attribute, $value, $fail) {
                        $tenantId = auth()->user()->tenant_id;
                        $typeIdFactura = 1; // Ajusta según tu catálogo, tipo factura por ejemplo es 1.

                        $exists = \App\Models\Rips\RipsBillingDocument::where('tenant_id', $tenantId)
                            ->where('type_id', $typeIdFactura)
                            ->where('document_number', $value)
                            ->exists();

                        if ($exists) {
                            $fail('El número de factura ya existe para este tipo de documento.');
                        }
                    };
                }),


// 🔥 Nuevo campo: Select de convenio/acuerdo
            Forms\Components\Select::make('agreement_id')
                ->label('Convenio / Contrato')
                ->searchable()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->code . ')')
                ->options(function (string $search = null) {
                    $tenantId = Auth::user()->tenant_id;
                    return \App\Models\Rips\RipsTenantPayerAgreement::query()
                        ->whereHas('payer', function ($query) use ($tenantId) {
                            $query->where('tenant_id', $tenantId);
                        })
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn ($agreement) => [$agreement->id => $agreement->name . ' (' . $agreement->code . ')']);
                })

                ->required()
                ->helperText('Seleccione el convenio o contrato bajo el cual se factura este servicio.'),

            Forms\Components\Toggle::make('has_incapacity')
                ->label('Has incapacity'),

            Forms\Components\DateTimePicker::make('service_datetime')
                ->default(now()) // Fecha por defecto de hoy
                ->required(),
        ]);
    }
}
