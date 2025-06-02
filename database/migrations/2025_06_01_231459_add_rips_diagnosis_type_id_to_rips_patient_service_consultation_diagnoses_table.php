<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_service_consultation_diagnoses', function (Blueprint $table) {
            $table->foreignId('rips_diagnosis_type_id')
                  ->nullable()
                  ->after('cie10_id');
            
            // Definir la foreign key con nombre manual más corto
            $table->foreign('rips_diagnosis_type_id', 'fk_consult_diag_type')
                  ->references('id')
                  ->on('rips_diagnosis_types')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_service_consultation_diagnoses', function (Blueprint $table) {
            $table->dropForeign('fk_consult_diag_type');
            $table->dropColumn('rips_diagnosis_type_id');
        });
    }
};
