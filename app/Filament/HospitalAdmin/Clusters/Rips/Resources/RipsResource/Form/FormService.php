<?php

namespace App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsResource\Form;

use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;

use App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Form\PatientForm;
use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Form\DoctorForm;
use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Form\DoctorMinimalForm;
use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsTenantPayerAgreement\RipsTenantPayerAgreementResource\Form\AgreementMinimalForm;


use App\Filament\HospitalAdmin\Clusters\Rips\Resources\RipsPayers\RipsPayerResource\Form\RipsPayerMinimalForm;

use Illuminate\Support\Carbon;
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\DoctorDepartment;

use App\Actions\Rips\LoadTemplateToForm;


class FormService
{
    public static function make(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3) 
                ->schema([
                    
                    Select::make('patient_id')
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
                                ->limit(100)
                                ->get()
                                ->sortBy(fn($patient) => strtolower($patient->user?->first_name . ' ' . $patient->user?->last_name)) // 👈 Ordenar alfabéticamente
        
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
                                ->sortBy(fn($doctor) => strtolower($doctor->user?->first_name . ' ' . $doctor->user?->last_name)) // 👈 Ordenar alfabéticamente
        
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
                    Forms\Components\Toggle::make('has_incapacity')
                        ->inlineLabel()
                        ->label(__('messages.rips.patientservice.has_incapacity')),
                    Forms\Components\DatePicker::make('service_date')
                        ->label('Fecha del Servicio')
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->native(true)
                        ->inlineLabel()
                        ->default(now()->toDateString())
                        ->required(),

                    Forms\Components\TimePicker::make('service_time')
                        ->label('Hora del Servicio')
                        ->native(true)
                        ->inlineLabel()
                        ->default(now()->format('H:i'))
                        ->required(),
                    Forms\Components\Select::make('billing_document_id')
                        ->label('Documento Soporte')
                        ->searchable()
                        ->inlineLabel()
                        ->nullable()
                        ->options(function () {
                            $tenantId = auth()->user()->tenant_id;
                            return \App\Models\Rips\RipsBillingDocument::where('tenant_id', $tenantId)
                                ->orderBy('document_number')
                                ->pluck('document_number', 'id');
                        })
                        ->afterStateUpdated(function ($state, callable $set) {
                            $agreement = \App\Models\Rips\RipsBillingDocument::find($state)?->agreement_id;
                            $set('agreement_id', $agreement);
                        })
                        ->createOptionForm(function (Forms\Get $get){
        $requiresFev = $get('requires_fev'); // ← leer del formulario principal
        return [
            Forms\Components\Select::make('type_id')
                ->label('Tipo de Documento Soporte')
                ->options(\App\Models\Rips\RipsBillingDocumentType::pluck('name', 'id'))
                ->default($requiresFev ? 1 : 2)
                ->disabled()
                ->required(),

            Forms\Components\TextInput::make('document_number')
                ->label('Número de Documento')
                ->required()
                ->maxLength(30),

            Forms\Components\Select::make('agreement_id')
                ->label('Convenio')
                ->searchable()
                ->options(\App\Models\Rips\RipsTenantPayerAgreement::pluck('name', 'id'))
                ->required(),
                    // 📂 Campo para subir XML (opcional)
        Forms\Components\FileUpload::make('xml_path')
            ->label('Archivo XML (opcional)')
            ->disk('public')
            ->directory(fn ($get) => 
                auth()->user()->tenant_id . '/' . ($get('agreement_id') ?? 'sin_convenio')
            )
            ->visibility('public')
            ->preserveFilenames()
            ->acceptedFileTypes(['text/xml','application/xml'])
            ->downloadable(),
        ];
                            })
                        ->createOptionUsing(function (array $data) {
                            return \App\Models\Rips\RipsBillingDocument::create([
                                'tenant_id' => auth()->user()->tenant_id,
                                'type_id' => $data['type_id'], // Puedes hacerlo dinámico si manejas otros tipos
                                'document_number' => $data['document_number'],
                                'agreement_id' => $data['agreement_id'],
                                'issued_at' => now(),
                            ])->id;
                        }),

                    Forms\Components\Toggle::make('requires_fev')
                        ->label('Requiere FEV')
                        ->inlineLabel()
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
                                ->where('tenant_id', $tenantId)
                                ->where(function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%")
                                        ->orWhere('code', 'like', "%{$search}%");
                                })
                                ->limit(100)
                                ->get()
                                ->mapWithKeys(fn ($agreement) => [$agreement->id => $agreement->name . ' (' . $agreement->code . ')']);
                        }),





                    
                    Forms\Components\Select::make('template_id')
                        ->label('Usar Plantilla')
                        ->searchable()
                        ->inlineLabel()
                        ->options(function () {
                            $tenantId = auth()->user()->tenant_id;
                            return \App\Models\Rips\RipsPatientServiceTemplate::query()
                                ->where('tenant_id', $tenantId)
                                ->orWhere('is_public', true)
                                ->pluck('name', 'id');
                        })
                        ->live()
                        ->afterStateUpdated(function (  $state, Set $set) {
                            if ($state) {
                                $data = app(\App\Actions\Rips\LoadTemplateToForm::class)($state);
                                
                                $set('consultations', $data['consultations'] ?? []);
                                $set('procedures', $data['procedures'] ?? []);
                            }
                        })
                        ->columnSpan(1),


                    Forms\Components\Toggle::make('save_as_template')
                        ->label('¿Guardar como plantilla?')
                        ->reactive()
                        ->inlineLabel(),

                    Forms\Components\TextInput::make('template_name')
                        ->label('Nombre de la plantilla')
                        ->inlineLabel()
                        ->required(fn ($get) => $get('save_as_template') === true)
                        ->visible(fn ($get) => $get('save_as_template') === true)
                        ->maxLength(255),
                    

                ]),
        ]);
    }
}
