<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('rips_patient_service_consultation_diagnoses', 'rips_diagnosis_type_id')) {
            Schema::table('rips_patient_service_consultation_diagnoses', function (Blueprint $table) {
                $table->unsignedBigInteger('rips_diagnosis_type_id')
                    ->nullable()
                    ->after('rips_patient_service_consultation_id');

                $table->foreign('rips_diagnosis_type_id', 'rpscd_diagnosis_type_fk')
                    ->references('id')
                    ->on('rips_diagnosis_types')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rips_patient_service_consultation_diagnoses', function (Blueprint $table) {
            $table->dropForeign('rpscd_diagnosis_type_fk');
            $table->dropColumn('rips_diagnosis_type_id');
        });

    }
};
