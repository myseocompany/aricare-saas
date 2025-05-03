<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('document_type', 3)->nullable()->after('patient_unique_id');
            $table->string('document_number', 15)->nullable()->after('document_type');
            $table->smallInteger('user_type')->nullable()->after('document_number');
            $table->date('birth_date')->nullable()->after('user_type');
            $table->string('sex_code', 2)->nullable()->after('birth_date');
            $table->string('country_code', 5)->nullable()->after('sex_code');
            $table->string('municipality_code', 6)->nullable()->after('country_code');
            $table->integer('zone_code')->nullable()->after('municipality_code');
            $table->integer('country_of_origin')->nullable()->after('zone_code');
        });
    }
    
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_number',
                'user_type',
                'birth_date',
                'sex_code',
                'country_code',
                'municipality_code',
                'zone_code',
                'country_of_origin',
            ]);
        });
    }
    
};


php artisan migrate --path=database/migrations/2025_05_03_135043_add_rips_fields_to_patients_table.php