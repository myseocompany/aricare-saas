<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRipsPatientServiceConsultationsAddGroupModeAndReason extends Migration
{
    public function up()
    {
        Schema::table('rips_patient_service_consultations', function (Blueprint $table) {
            $$table->foreignId('rips_service_group_mode_id')->nullable();
            $table->foreignId('rips_service_reason_id')->nullable();
            $table->foreignId('rips_consultation_cups_id')->nullable();


            // Definir llaves foráneas con nombres cortos
            $table->foreign('rips_service_group_mode_id', 'fk_rps_group_mode')
                ->references('id')
                ->on('rips_service_group_modes')
                ->nullOnDelete();

            $table->foreign('rips_service_reason_id', 'fk_rps_reason')
                ->references('id')
                ->on('rips_service_reasons')
                ->nullOnDelete();

            $table->foreign('rips_consultation_cups_id', 'fk_rps_consult_cups')
                ->references('id')
                ->on('rips_cups')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rips_patient_service_consultations', function (Blueprint $table) {
            $table->dropForeign('fk_rps_group_mode');
            $table->dropForeign('fk_rps_reason');
            $table->dropForeign('fk_rps_consult_cups');

            $table->dropColumn([
                'rips_service_group_mode_id',
                'rips_service_reason_id',
                'rips_consultation_cups_id',
            ]);
        });
    }
}
