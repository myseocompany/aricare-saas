<?php

    namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Form;

    use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\User;
use App\Models\Rips\RipsIdentificationType;
use App\Models\DoctorDepartment;

class DoctorMinimalForm
{


    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->label(__('messages.user.first_name')),

                    TextInput::make('last_name')
                        ->required()
                        ->label(__('messages.user.last_name')),

                   Select::make('rips_identification_type_id')
                        ->required()
                        ->label(__('messages.user.identification_type'))
                        ->options(function () {
                            return RipsIdentificationType::whereIn('id', function ($query) {
                                $query->select('identification_type_id')
                                    ->from('rips_model_identification_types')
                                    ->where('model_type', 'App\\Models\\Doctor');
                            })->pluck('name', 'id');
                        }),


                    TextInput::make('rips_identification_number')
                        ->required()
                        ->label(__('messages.user.identification_number')),

                    // Campos ocultos requeridos
                    Hidden::make('status')->default(User::ACTIVE),
                    Hidden::make('gender')->default(0),
                    Hidden::make('dob')->default('1980-01-01'),
                    Hidden::make('phone')->default('0000000000'),
                    Hidden::make('region_code')->default('+57'),
                    Hidden::make('email')->default(fn () => 'doc_' . uniqid() . '@fake.local'),
                    Hidden::make('password')->default(fn () => bcrypt('defaultpassword')),
                    Hidden::make('department_id')->default(1),
                    Hidden::make('designation')->default('Doctor'),
                    Hidden::make('qualification')->default('N/A'),
                    Hidden::make('language')->default('es'),
                    Hidden::make('hospital_name')->default(''),
                    Hidden::make('tenant_id')->default(fn () => getLoggedInUser()->tenant_id),
                    Hidden::make('appointment_charge')->default(0),
                    Hidden::make('doctor_department_id')
                        ->default(function () {
                            $tenantId = getLoggedInUser()->tenant_id;

                            $department = DoctorDepartment::where('tenant_id', $tenantId)->first();

                            if (!$department) {
                                $department = DoctorDepartment::create([
                                    'tenant_id' => $tenantId,
                                    'title' => 'General',
                                    'description' => 'Departamento por defecto creado automáticamente.',
                                ]);
                            }

                            return $department->id;
                        }),
                    Hidden::make('specialist')->default('General'),
                    Hidden::make('description')->default(''),



                ])
                ->columns(2),
        ];
    }
}
