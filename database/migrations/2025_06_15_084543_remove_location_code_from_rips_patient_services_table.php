<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->dropColumn('location_code');
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->string('location_code', 12)->nullable()->after('billing_document_id');
        });
    }
};
