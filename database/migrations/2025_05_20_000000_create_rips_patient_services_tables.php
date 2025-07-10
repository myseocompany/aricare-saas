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

            //$table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');


            $table->string('tenant_id', 255);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->string('location_code', 12)->nullable();
            $table->boolean('has_incapacity')->default(false);
            $table->dateTime('service_datetime');
            $table->timestamps();
        });

        Schema::create('rips_patient_service_consultations', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('rips_patient_service_id');
            $table->foreign('rips_patient_service_id', 'fk_rps_consultations_service')
                ->references('id')
                ->on('rips_patient_services')
                ->onDelete('cascade');

            
            
            $table->foreignId('rips_cups_id')->nullable()->constrained('rips_cups');
            $table->foreignId('service_group_id')->nullable()->constrained('rips_service_groups');
            $table->foreignId('service_id')->nullable()->constrained('rips_services');

            $table->foreignId('technology_purpose_id')->nullable()->constrained('rips_technology_purposes');
            
            $table->float('service_value')->nullable();
            $table->foreignId('collection_concept_id')->nullable()->constrained('rips_collection_concepts');
            
            $table->float('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('rips_patient_service_consultation_diagnoses', function (Blueprint $table) {
            $table->id();
        

            $table->unsignedBigInteger('rips_patient_service_consultation_id');
            $table->foreign('rips_patient_service_consultation_id', 'fk_rps_consultation_diagnoses')
                ->references('id')
                ->on('rips_patient_service_consultations')
                ->onDelete('cascade');
                  
        
            $table->foreignId('cie10_id')->constrained('cie10');
        
            $table->unsignedSmallInteger('sequence')->comment('1 = principal, 2+ = relacionados');
        
            $table->timestamps();
        });
        
        

        Schema::create('rips_patient_service_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rips_patient_service_id')->constrained('rips_patient_services')->onDelete('cascade');
            $table->string('mipres_id', 30)->nullable();
            $table->string('authorization_number', 30)->nullable();
            $table->foreignId('rips_cups_id')->constrained('rips_cups');
            $table->foreignId('cie10_id')->nullable()->constrained('cie10');
            $table->foreignId('surgery_cie10_id')->nullable()->constrained('cie10');
            $table->float('service_value')->nullable();
            $table->float('copayment_value')->nullable();
            $table->string('copayment_receipt_number', 30)->nullable();
            $table->timestamps();
        });


    }

    public function down(): void {
        Schema::dropIfExists('rips_patient_service_consultations');
        Schema::dropIfExists('rips_patient_service_procedures');
        Schema::dropIfExists('rips_patient_service_consultation_diagnoses');
        Schema::dropIfExists('rips_patient_services');
    }
};


