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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('rips_idsispro', 50)->nullable()->after('sispro_username');
            $table->string('rips_passispro', 50)->nullable()->after('rips_idsispro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['rips_provider_code', 'rips_idsispro', 'passispro']);
        });
    }
};
