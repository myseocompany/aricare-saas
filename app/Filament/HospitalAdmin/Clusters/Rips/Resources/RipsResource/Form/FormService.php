<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Form\PatientForm;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource\Form\DoctorForm;
use App\Filament\HospitalAdmin\Clusters\Doctors\Resources\DoctorResource\Form\DoctorMinimalForm;

use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\DoctorDepartment;

class FormService
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3) // <-- DOS columnas
                ->schema([
                    Forms\Components\Select::make('patient_id')
                        ->label(__('messages.ipd_patient.patient_id'))
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

                        ->createOptionForm(PatientForm::schema())
                        ->createOptionUsing(function (array $data) {
                            $data['tenant_id'] = auth()->user()->tenant_id;
                            $user = app(\App\Repositories\PatientRepository::class)->store($data, false);
                            return $user->owner_id;

                        })
                        ->required(),

                    Forms\Components\Select::make('doctor_id')
                        ->label(__('messages.ipd_patient.doctor_id'))
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

                        ->createOptionForm(DoctorMinimalForm::schema())
                        ->createOptionUsing(function (array $data) {
                            $data['tenant_id'] = auth()->user()->tenant_id;
                            $user = app(\App\Repositories\DoctorRepository::class)->store($data, false);
                            return $user->owner_id;

                        })
                        ->preload()
                        ->required(),

                    Forms\Components\DateTimePicker::make('service_datetime')
                        ->label(__('messages.rips.patientservice.service_datetime'))
                        ->default(now())
                        ->inlineLabel()
                        ->required(),
                        
                    Forms\Components\Select::make('billing_document_id')
                        ->label('Factura')
                        ->searchable()
                        ->inlineLabel()
                        ->nullable()
                        ->options(function () {
                            $tenantId = auth()->user()->tenant_id;

                            return \App\Models\Rips\RipsBillingDocument::query()
                                ->where('tenant_id', $tenantId)
                                ->orderByDesc('created_at') // Opcional: prioriza recientes
                                ->limit(100) // o más si deseas
                                ->get()
                                ->mapWithKeys(fn ($doc) => [$doc->id => $doc->document_number]);
                        })
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
                    Forms\Components\Select::make('electronic_invoice')
                                        ->label('Factura Electrónica')
                                        ->options([
                                            'yes' => 'Sí',
                                            'no' => 'No',
                                        ])
                                        ->required(),    

                    Forms\Components\Select::make('agreement_id')
                        ->label('Convenio / Contrato')
                        ->searchable()
                        ->inlineLabel()
                        ->disabled()
                        ->dehydrated(false)

                        ->visible(fn ($get) => filled($get('billing_document_id')))

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
                        ->label(__('messages.rips.patientservice.has_incapacity')),
                    
                    Forms\Components\Hidden::make('tenant_id')
                        ->default(Auth::user()->tenant_id)
                        ->required(),
                ]),
        ]);
    }
}
