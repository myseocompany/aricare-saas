<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InsuranceTableSeeder extends Seeder
{
    public function run()
    {
        $json = File::get(database_path('data/insurances.json'));
        $insurances = json_decode($json, true);

        foreach ($insurances as $insurance) {
            DB::table('insurances')->insert([
                'name' => $insurance['name'],
                'insurance_code' => $insurance['insurance_code'],
                'insurance_no' => $insurance['insurance_no'],
                'remark' => $insurance['remark'],
                'service_tax' => 0,
                'hospital_rate' => 0,
                'total' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
