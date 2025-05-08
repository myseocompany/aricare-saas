<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Models\HospitalSchedule;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\MultiTenant as Tenant;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RipsDoctorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('es_ES'); // Usamos ES para nombres hispanos
        $departmentId = Department::whereName('Doctor')->value('id');

        foreach (Tenant::all() as $tenant) {
            $doctorDepartmentId = DoctorDepartment::where('tenant_id', $tenant->id)->value('id');

            if (!$doctorDepartmentId) {
                $this->command->warn("Saltando tenant {$tenant->id} (sin DoctorDepartment)");
                continue;
            }

            for ($i = 0; $i < 3; $i++) {
                $user = User::create([
                    'department_id' => $departmentId,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('123456'),
                    'designation' => 'doctor',
                    'qualification' => 'MD',
                    'status' => 1,
                    'email_verified_at' => now(),
                    'tenant_id' => $tenant->id,
                ]);

                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'doctor_department_id' => $doctorDepartmentId,
                    'specialist' => $faker->randomElement(['medicina general', 'pediatría', 'ginecología']),
                    'tenant_id' => $tenant->id,
                ]);

                $user->update([
                    'owner_id' => $doctor->id,
                    'owner_type' => Doctor::class,
                ]);

                $user->assignRole($departmentId);

                $schedule = Schedule::create([
                    'doctor_id' => $doctor->id,
                    'per_patient_time' => '00:15:00',
                    'tenant_id' => $tenant->id,
                ]);

                foreach (HospitalSchedule::WEEKDAY_FULL_NAME as $index => $day) {
                    ScheduleDay::create([
                        'doctor_id' => $doctor->id,
                        'schedule_id' => $schedule->id,
                        'available_on' => $index, // ← aquí usamos el índice numérico correcto
                        'available_from' => '08:00:00',
                        'available_to' => '12:00:00',
                    ]);
                }
                
            }

            $this->command->info("✅ 3 doctores creados para el tenant: {$tenant->hospital_name}");
        }
    }
}
