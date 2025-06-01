<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->foreignId('billing_document_id')
                ->nullable()
                ->after('doctor_id')
                ->constrained('rips_billing_documents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->dropForeign(['billing_document_id']);
            $table->dropColumn('billing_document_id');
        });
    }
};
