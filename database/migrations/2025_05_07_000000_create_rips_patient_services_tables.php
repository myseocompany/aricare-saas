<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rips_patient_services', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');


            $table->string('tenant_id', 255);
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->string('location_code', 12)->nullable();
            $table->boolean('has_incapacity')->default(false);
            $table->dateTime('service_datetime');
            $table->string('service_group_code', 5)->nullable();
            $table->integer('service_code')->nullable();
            $table->string('technology_purpose_code', 10)->nullable();
            $table->string('collection_concept_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('rips_patient_service_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_service_id')->constrained('rips_patient_services')->onDelete('cascade');
            $table->foreignId('cie10_id')->constrained('cie10');
            $table->unsignedSmallInteger('sequence');
        });

        Schema::create('rips_patient_service_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_service_id')->constrained('rips_patient_services')->onDelete('cascade');
            $table->string('mipres_id', 30)->nullable();
            $table->string('authorization_number', 30)->nullable();
            $table->foreignId('cups_id')->constrained('cups');
            $table->foreignId('cie10_id')->nullable()->constrained('cie10');
            $table->foreignId('surgery_cie10_id')->nullable()->constrained('cie10');
            $table->float('service_value')->nullable();
            $table->float('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('rips_patient_service_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_service_id')->unique()->constrained('rips_patient_services')->onDelete('cascade');
            $table->foreignId('consultation_cups_id')->constrained('cups');
            $table->float('service_value')->nullable();
            $table->float('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rips_patient_service_consultations');
        Schema::dropIfExists('rips_patient_service_procedures');
        Schema::dropIfExists('rips_patient_service_diagnoses');
        Schema::dropIfExists('rips_patient_services');
    }
};
