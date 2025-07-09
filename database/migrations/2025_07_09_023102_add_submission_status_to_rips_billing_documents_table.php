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
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->string('submission_status')
                  ->nullable()
                  ->after('net_amount'); // Ajusta si quieres que esté después de otro campo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->dropColumn('submission_status');
        });
    }
};
