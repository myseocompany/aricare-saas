<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->foreignId('rips_invoice_id')
                ->nullable()
                ->constrained('rips_tenant_invoices')
                ->nullOnDelete()
                ->after('id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rips_patient_service_procedures', function (Blueprint $table) {
            $table->dropForeign(['rips_invoice_id']);
            $table->dropColumn('rips_invoice_id');
        });
    }
};
