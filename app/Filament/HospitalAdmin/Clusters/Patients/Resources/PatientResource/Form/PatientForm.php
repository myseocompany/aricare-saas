<?php

namespace App\Filament\HospitalAdmin\Clusters\Patients\Resources\PatientResource\Form;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Rips\RipsIdentificationType;
use App\Models\Rips\RipsUserType;
use App\Models\Rips\RipsCountry;
use App\Models\Rips\RipsDepartment;
use App\Models\Rips\RipsMunicipality;
use App\Models\Rips\RipsTerritorialZoneType;
use App\Models\Rips\RipsGenderType;

class PatientForm
{
    public static function schema(): array
    {
        $genderOptions = RipsGenderType::pluck('name', 'id')->toArray();
        $territorialZoneOptions = RipsTerritorialZoneType::pluck('name', 'code')->toArray();

        return [
            Section::make()->schema([
                Select::make('rips_identification_type_id')
                    ->label('Tipo de documento (RIPS)')
                    ->options(RipsIdentificationType::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione tipo de documento'),
                TextInput::make('rips_identification_number')
                    ->label(__('messages.patient.document_number') . ':')
                    ->required()
                    ->maxLength(15),
                TextInput::make('first_name')
                    ->required()
                    ->label(__('messages.user.first_name') . ':')
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->label(__('messages.user.last_name') . ':')
                    ->maxLength(255),
                Select::make('type_id')
                    ->label(__('messages.patient.patient_type') . ':')
                    ->options(RipsUserType::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),
                Select::make('country_of_origin_id')
                    ->label(__('messages.patient.origin_country') . ':')
                    ->options(RipsCountry::pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->default(fn () => RipsCountry::where('name', 'Colombia')->value('id'))
                    ->preload()
                    ->placeholder('Seleccione país de origen'),
                Radio::make('gender')
                    ->label(__('messages.user.gender') . ':')
                    ->required()
                    ->options($genderOptions)
                    ->columns(count($genderOptions)),
                DatePicker::make('dob')
                    ->native(true)
                    ->maxDate(now())
                    ->label(__('messages.user.dob') . ':'),
            ])->columns(2),
            Fieldset::make('Detalles de residencia')->schema([
                Group::make()->schema([
                    Select::make('rips_country_id')
                        ->label(__('messages.patient.residence_country') . ':')
                        ->options(RipsCountry::pluck('name', 'id'))
                        ->default(fn () => RipsCountry::where('name', 'Colombia')->value('id'))
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_department_id', null)),
                    Select::make('rips_department_id')
                        ->label(__('messages.patient.residence_department') . ':')
                        ->options(function (callable $get) {
                            if (! $get('rips_country_id')) return [];
                            return RipsDepartment::where('rips_country_id', $get('rips_country_id'))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable()
                        //->default(fn () => RipsDepartment::where('name', 'Antioquia')->value('id'))
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('rips_municipality_id', null)),
                ])->columns(2),
                Group::make()->schema([
                    Select::make('rips_municipality_id')
                        ->label(__('messages.patient.residence_city') . ':')
                        ->options(function (callable $get) {
                            if (! $get('rips_department_id')) return [];
                            return RipsMunicipality::where('rips_department_id', $get('rips_department_id'))
                                ->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        //->default(fn () => RipsMunicipality::where('name', 'Medellín')->value('id'))
                        ->required()
                        ->searchable(),
                    Select::make('zone_code')
                        ->label(__('messages.patient.residence_zone') . ':')
                        ->options(RipsTerritorialZoneType::pluck('name', 'id')) // <--- CAMBIO CLAVE
                        ->required()
                        ->default(1)
                        ->placeholder('Seleccione zona territorial')
                        ->native(false)
                        ->searchable(),


                ])->columns(2),
            ]),
        ];
    }
}
