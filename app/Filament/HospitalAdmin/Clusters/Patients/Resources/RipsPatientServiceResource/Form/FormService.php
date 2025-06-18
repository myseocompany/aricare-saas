<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\RipsPatientServiceResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

class FormService
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3) // <-- DOS columnas
                ->schema([
                    Forms\Components\Select::make('patient_id')
                        ->label('Paciente')
                        ->searchable()
                        ->inlineLabel()
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
                        ->inlineLabel()
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

                    Forms\Components\DateTimePicker::make('service_datetime')
                        ->default(now())
                        ->inlineLabel()
                        ->required(),
                        
                    Forms\Components\Select::make('billing_document_id')
    ->label('Factura')
    ->searchable()
    ->inlineLabel()
    ->nullable()
    ->options(\App\Models\Rips\RipsBillingDocument::pluck('document_number', 'id'))
    ->afterStateUpdated(function ($state, callable $set) {
        $agreement = null;
        if ($state) {
            $agreement = \App\Models\Rips\RipsBillingDocument::find($state)?->agreement_id;
        }
        $set('agreement_id', $agreement);
    })
    ->createOptionForm([
        Forms\Components\TextInput::make('document_number')
            ->label('Número de Factura')
            ->maxLength(30)
            ->required(),

        Forms\Components\Select::make('agreement_id')
            ->label('Convenio')
            ->options(\App\Models\Rips\RipsTenantPayerAgreement::pluck('name', 'id'))
            ->searchable()
            ->required(),
    ])
    ->createOptionUsing(function (array $data) {
        return \App\Models\Rips\RipsBillingDocument::create([
            'tenant_id' => auth()->user()->tenant_id,
            'type_id' => 1, // Tipo factura
            'document_number' => $data['document_number'],
            'agreement_id' => $data['agreement_id'],
            'issued_at' => now(),
        ])->id;
    }),

                    Forms\Components\Select::make('agreement_id')
                        ->label('Convenio / Contrato')
                        ->searchable()
                        ->inlineLabel()
                        ->disabled()
                        ->dehydrated(false)
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->code . ')')
                        ->afterStateHydrated(function ($component, $state) {
                            $record = $component->getRecord();
                            if ($record) {
                                $component->state($record->billingDocument?->agreement_id);
                            }
                        })
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

                        ,



                    Forms\Components\Toggle::make('has_incapacity')
                        ->inlineLabel()
                        ->label('Has incapacity'),
                    
                    Forms\Components\Hidden::make('tenant_id')
                        ->default(Auth::user()->tenant_id)
                        ->required(),
                ]),
        ]);
    }
}
