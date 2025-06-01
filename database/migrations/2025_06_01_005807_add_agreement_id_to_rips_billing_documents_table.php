<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->foreignId('agreement_id')
                ->nullable()
                ->after('tenant_id')
                ->constrained('rips_tenant_payer_agreements')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->dropForeign(['agreement_id']);
            $table->dropColumn('agreement_id');
        });
    }
};
