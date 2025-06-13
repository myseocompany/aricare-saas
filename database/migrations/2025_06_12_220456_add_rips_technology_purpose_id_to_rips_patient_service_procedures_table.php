<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->unsignedBigInteger('rips_technology_purpose_id')->nullable()->after('rips_collection_concept_id');

            $table->foreign('rips_technology_purpose_id', 'fk_psp_technology_purpose')
                ->references('id')
                ->on('rips_technology_purposes')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->dropForeign('fk_psp_technology_purpose');
            $table->dropColumn('rips_technology_purpose_id');
        });
    }
};
