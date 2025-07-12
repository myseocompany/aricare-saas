<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            // Eliminar los campos antiguos
            //$table->dropColumn(['service_group_code', 'service_code', 'technology_purpose_code', 'collection_concept_code']);

            // Agregar nuevas claves foráneas
            $table->foreignId('rips_service_group_id')->nullable()->constrained('rips_service_groups');
            $table->foreignId('rips_service_id')->nullable()->constrained('rips_services');
            $table->foreignId('rips_technology_purpose_id')->nullable()->constrained('rips_technology_purposes');
            $table->foreignId('rips_collection_concept_id')->nullable()->constrained('rips_collection_concepts');
        });
    }

    public function down(): void {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->dropForeign(['rips_service_group_id']);
            $table->dropForeign(['rips_service_id']);
            $table->dropForeign(['rips_technology_purpose_id']);
            $table->dropForeign(['rips_collection_concept_id']);

            $table->dropColumn([
                'rips_service_group_id',
                'rips_service_id',
                'rips_technology_purpose_id',
                'rips_collection_concept_id',
            ]);

            $table->string('service_group_code', 5)->nullable();
            $table->integer('service_code')->nullable();
            $table->string('technology_purpose_code', 10)->nullable();
            $table->string('collection_concept_code', 10)->nullable();
        });
    }
};
