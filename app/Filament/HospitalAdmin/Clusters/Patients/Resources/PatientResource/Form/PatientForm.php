<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Form;

use App\Enums\MaritalStatus;
use App\Models\Rips\RipsCountry;
use App\Models\Rips\RipsDepartment;
use App\Models\Rips\RipsGenderType;
use App\Models\Rips\RipsIdentificationType;
use App\Models\Rips\RipsMunicipality;
use App\Models\Rips\RipsTerritorialZoneType;
use App\Models\Rips\RipsUserType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PatientForm
{
    public static function schema(): array
    {
        $genderOptions = RipsGenderType::pluck('name', 'id')->toArray();
        $defaultIdentificationType = RipsIdentificationType::value('id');
        $defaultGender = RipsGenderType::value('id');
        $defaultUserType = RipsUserType::value('id');
        $defaultCountry = RipsCountry::where('name', 'Colombia')->value('id') ?? RipsCountry::value('id');

        return [
            Section::make()->schema([
                // Campos que están en users.*
                Group::make()
                    ->relationship('user')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->label(__('messages.user.first_name') . ':')
                            ->maxLength(255)
                            ->default('Paciente Demo'),

                        TextInput::make('last_name')
                            ->required()
                            ->label(__('messages.user.last_name') . ':')
                            ->maxLength(255)
                            ->default('Prueba'),

                        TextInput::make('phone')
                            ->label(__('messages.user.phone') . ':')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('phone_secondary')
                            ->label(__('messages.patient.phone_secondary') . ':')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('contact_email')
                            ->label(__('messages.user.email') . ':')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('correo@paciente.com'),
                                                        
                        Radio::make('gender')
                            ->label(__('messages.user.gender') . ':')
                            ->required()
                            ->options($genderOptions)
                            ->columns(count($genderOptions))
                            ->default($defaultGender),

                        Select::make('rips_identification_type_id')
                            ->label('Tipo de documento')
                            ->options(RipsIdentificationType::pluck('name', 'id'))
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione tipo de documento')
                            ->default($defaultIdentificationType),

                        TextInput::make('rips_identification_number')
                            ->label(__('messages.patient.document_number') . ':')
                            ->required()
                            ->maxLength(15)
                            ->default(fn () => '900' . random_int(100000, 999999)),

                        DatePicker::make('dob')
                            ->label(__('messages.user.dob') . ':')
                            ->native(true)
                            ->maxDate(now())
                            ->closeOnDateSelection()
                            ->default(now()->subYears(30)),

                                        TextInput::make('birth_place')
                    ->label(__('messages.patient.birth_place') . ':')
                    ->maxLength(255),
                                        Select::make('type_id')
                    ->label(__('messages.patient.patient_type') . ':')
                    ->options(RipsUserType::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->default($defaultUserType)
                    ->preload(),

                Select::make('country_of_origin_id')
                    ->label(__('messages.patient.origin_country') . ':')
                    ->options(RipsCountry::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->default($defaultCountry)
                    ->preload()
                    ->placeholder('Seleccione país de origen'),
                    ])
                    ->columns(3),

                // Campos que están en patients.*

            ])->columns(1),

            Section::make(__('messages.patient.additional_information'))->schema([
                Select::make('marital_status_id')
                    ->label(__('messages.patient.marital_status.label') . ':')
                    ->options(MaritalStatus::options())
                    ->native(false)
                    ->placeholder(__('messages.common.select')),



                TextInput::make('residence_address')
                    ->label(__('messages.patient.residence_address') . ':')
                    ->maxLength(255),

                TextInput::make('occupation')
                    ->label(__('messages.patient.occupation') . ':')
                    ->maxLength(255),

                TextInput::make('ethnicity')
                    ->label(__('messages.patient.ethnicity') . ':')
                    ->maxLength(255),

                TextInput::make('education_level')
                    ->label(__('messages.patient.education_level') . ':')
                    ->maxLength(255),


            ])->columns(3),

            Section::make('Detalles de residencia')->schema([
                Group::make()->schema([
                    Select::make('rips_country_id')
                        ->label(__('messages.patient.residence_country') . ':')
                        ->options(RipsCountry::pluck('name', 'id'))
                        ->default($defaultCountry)
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_department_id', null)),

                    Select::make('rips_department_id')
                        ->label(__('messages.patient.residence_department') . ':')
                        ->options(function (callable $get) {
                            if (! $get('rips_country_id')) {
                                return [];
                            }

                            return RipsDepartment::where('rips_country_id', $get('rips_country_id'))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->default(function () use ($defaultCountry) {
                            return RipsDepartment::where('rips_country_id', $defaultCountry)
                                ->orderBy('name')
                                ->value('id');
                        })
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_municipality_id', null)),
                ])->columns(2),

                Group::make()->schema([
                    Select::make('rips_municipality_id')
                        ->label(__('messages.patient.residence_city') . ':')
                        ->options(function (callable $get) {
                            if (! $get('rips_department_id')) {
                                return [];
                            }

                            return RipsMunicipality::where('rips_department_id', $get('rips_department_id'))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->default(function () use ($defaultCountry) {
                            $departmentId = RipsDepartment::where('rips_country_id', $defaultCountry)
                                ->orderBy('name')
                                ->value('id');

                            if (! $departmentId) {
                                return null;
                            }

                            return RipsMunicipality::where('rips_department_id', $departmentId)
                                ->orderBy('name')
                                ->value('id');
                        })
                        ->required()
                        ->searchable(),

                    Select::make('zone_code')
                        ->label(__('messages.patient.residence_zone') . ':')
                        ->options(RipsTerritorialZoneType::pluck('name', 'id'))
                        ->required()
                        ->default(1)
                        ->placeholder('Seleccione zona territorial')
                        ->native(false)
                        ->searchable(),
                ])->columns(2),
            ]),

            Section::make(__('messages.patient.guardian_details'))->schema([
                TextInput::make('responsible_name')
                    ->label(__('messages.patient.responsible_name') . ':')
                    ->maxLength(255),

                TextInput::make('responsible_phone')
                    ->label(__('messages.patient.responsible_phone') . ':')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('responsible_relationship')
                    ->label(__('messages.patient.responsible_relationship') . ':')
                    ->maxLength(255),
            ])->columns(3),

            Section::make(__('messages.patient.emergency_contact_details'))->schema([
                TextInput::make('emergency_contact_name')
                    ->label(__('messages.patient.emergency_contact_name') . ':')
                    ->maxLength(255),

                TextInput::make('emergency_contact_phone')
                    ->label(__('messages.patient.emergency_contact_phone') . ':')
                    ->tel()
                    ->maxLength(50),
            ])->columns(2),
        ];
    }
}
