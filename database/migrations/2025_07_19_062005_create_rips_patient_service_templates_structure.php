<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla principal
        Schema::create('rips_patient_service_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Consultas
        Schema::create('rips_patient_service_template_consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('rips_cups_id')->nullable();
            $table->unsignedBigInteger('rips_service_group_id')->nullable();
            $table->unsignedBigInteger('rips_service_group_mode_id')->nullable();
            $table->unsignedBigInteger('rips_service_reason_id')->nullable();
            $table->unsignedBigInteger('rips_consultation_cups_id')->nullable();
            $table->unsignedBigInteger('rips_service_id')->nullable();
            $table->unsignedBigInteger('rips_technology_purpose_id')->nullable();
            $table->double('service_value')->nullable();
            $table->unsignedBigInteger('rips_collection_concept_id')->nullable();
            $table->double('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();

            $table->foreign('template_id', 'rpst_cons_template_fk')
                  ->references('id')->on('rips_patient_service_templates')->onDelete('cascade');
        });

        // Diagnósticos de consulta
        Schema::create('rips_patient_service_template_consultation_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cie10_id');
            $table->unsignedBigInteger('rips_diagnosis_type_id')->nullable();
            $table->smallInteger('sequence');

            $table->timestamps();

            $table->foreignId('consultation_id')
                ->constrained('rips_patient_service_template_consultations')->onDelete('cascade');
        });

        // Procedimientos
        Schema::create('rips_patient_service_template_procedures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('rips_admission_route_id')->nullable();
            $table->unsignedBigInteger('rips_service_group_mode_id')->nullable();
            $table->unsignedBigInteger('rips_service_group_id')->nullable();
            $table->unsignedBigInteger('rips_service_id')->nullable();
            $table->unsignedBigInteger('rips_collection_concept_id')->nullable();
            $table->unsignedBigInteger('rips_technology_purpose_id')->nullable();
            $table->string('mipres_id', 30)->nullable();
            $table->string('authorization_number', 30)->nullable();
            $table->unsignedBigInteger('rips_cups_id');
            $table->unsignedBigInteger('cie10_id')->nullable();
            $table->unsignedBigInteger('surgery_cie10_id')->nullable();
            $table->unsignedBigInteger('rips_complication_cie10_id')->nullable();
            $table->double('service_value')->nullable();
            $table->double('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();

            $table->foreign('template_id', 'rpst_proc_template_fk')
                  ->references('id')->on('rips_patient_service_templates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_patient_service_template_procedures');
        Schema::dropIfExists('rips_patient_service_template_consultation_diagnoses');
        Schema::dropIfExists('rips_patient_service_template_consultations');
        Schema::dropIfExists('rips_patient_service_templates');
    }
};
