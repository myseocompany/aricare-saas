<?php

namespace App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource\Pages;

use App\Filament\HospitalAdmin\Clusters\DoctorsCluster\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\Doctor;


class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extrae campos del User desde el $data
        $userData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt('defaultpassword'),
            'tenant_id' => getLoggedInUser()->tenant_id,
            'region_code' => '+57',
            'phone' => '0000000000',
            'gender' => 0,
            'dob' => '1980-01-01',
            'status' => 1,
            'designation' => 'Doctor',
            'qualification' => 'N/A',
            'language' => 'es',
            'department_id' => 1,
            'hospital_name' => '',
            'rips_identification_type_id' => $data['rips_identification_type_id'],
            'rips_identification_number' => $data['rips_identification_number'],
        ];

        // Crea el usuario
        $user = User::create($userData);

        // Asigna el ID del usuario al doctor
        $data['user_id'] = $user->id;

        // Elimina campos de user del array $data si no están en la tabla doctors
        unset(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['rips_identification_type_id'],
            $data['rips_identification_number'],
            $data['region_code'],
            $data['phone'],
            $data['gender'],
            $data['dob'],
            $data['department_id'],
            $data['designation'],
            $data['qualification'],
            $data['language'],
            $data['hospital_name'],
            $data['status']
        );

        return $data;
    }

}
