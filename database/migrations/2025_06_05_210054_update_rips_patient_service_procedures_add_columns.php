<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRipsPatientServiceProceduresAddColumns extends Migration
{
    public function up()
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->foreignId('rips_admission_route_id')->nullable()->after('rips_patient_service_id');
            $table->foreignId('rips_service_group_mode_id')->nullable()->after('rips_admission_route_id');
            $table->foreignId('rips_service_group_id')->nullable()->after('rips_service_group_mode_id');
            $table->foreignId('rips_collection_concept_id')->nullable()->after('rips_service_group_id');
            $table->foreignId('rips_complication_cie10_id')->nullable()->after('surgery_cie10_id');

            // Constraints con nombres más cortos
            $table->foreign('rips_admission_route_id', 'fk_psp_admission_route')
                ->references('id')
                ->on('rips_admission_routes')
                ->nullOnDelete();

            $table->foreign('rips_service_group_mode_id', 'fk_psp_service_group_mode')
                ->references('id')
                ->on('rips_service_group_modes')
                ->nullOnDelete();

            $table->foreign('rips_service_group_id', 'fk_psp_service_group')
                ->references('id')
                ->on('rips_service_groups')
                ->nullOnDelete();

            $table->foreign('rips_collection_concept_id', 'fk_psp_collection_concept')
                ->references('id')
                ->on('rips_collection_concepts')
                ->nullOnDelete();

            $table->foreign('rips_complication_cie10_id', 'fk_psp_complication_cie10')
                ->references('id')
                ->on('cie10')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->dropForeign('fk_psp_admission_route');
            $table->dropForeign('fk_psp_service_group_mode');
            $table->dropForeign('fk_psp_service_group');
            $table->dropForeign('fk_psp_collection_concept');
            $table->dropForeign('fk_psp_complication_cie10');

            $table->dropColumn([
                'rips_admission_route_id',
                'rips_service_group_mode_id',
                'rips_service_group_id',
                'rips_collection_concept_id',
                'rips_complication_cie10_id',
            ]);
        });
    }
}
