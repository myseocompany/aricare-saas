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
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('rips_payer_id')
                ->nullable()
                ->constrained('rips_payers')
                ->nullOnDelete();

            $table->foreignId('rips_tenant_payer_agreement_id')
                ->nullable()
                ->constrained('rips_tenant_payer_agreements')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['rips_payer_id']);
            $table->dropColumn('rips_payer_id');

            $table->dropForeign(['rips_tenant_payer_agreement_id']);
            $table->dropColumn('rips_tenant_payer_agreement_id');
        });
    }
};
