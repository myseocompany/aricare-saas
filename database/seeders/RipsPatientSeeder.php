<?php

namespace Database\Seeders;

use App\Models\MultiTenant;
use App\Models\Patient;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RipsPatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('es_ES');
        $patientDepartmentId = 3; // Departamento 'Patient'

        foreach (MultiTenant::all() as $tenant) {
            for ($i = 0; $i < 50; $i++) {
                $user = User::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('123456'),
                    'designation' => 'patient',
                    'status' => 1,
                    'email_verified_at' => now(),
                    'department_id' => $patientDepartmentId,
                    'tenant_id' => $tenant->id,
                ]);

                $patient = Patient::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'guardian_name' => $faker->name,
                    'gender' => $faker->randomElement(['male', 'female']),
                    'dob' => $faker->date(),
                    'blood_group' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                ]);

                $user->update([
                    'owner_id' => $patient->id,
                    'owner_type' => Patient::class,
                ]);

                $user->assignRole($patientDepartmentId);
            }

            $this->command->info("✅ 50 pacientes creados para el tenant: {$tenant->hospital_name} ({$tenant->tenant_username})");
        }
    }
}
