<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->unsignedBigInteger('rips_service_id')->nullable()->after('rips_service_group_id');

            $table->foreign('rips_service_id')
                ->references('id')
                ->on('rips_services')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->dropForeign(['rips_service_id']);
            $table->dropColumn('rips_service_id');
        });
    }
};
