<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Código de habilitación del prestador (REPS) — 12 dígitos
            $table->string('rips_provider_code', 12)
                ->nullable()
                ->unique()
                ->after('rips_identification_number');
        });

        // (Opcional) Backfill si ya usabas `provider_code`
        DB::statement('
            UPDATE tenants
            SET rips_provider_code = provider_code
            WHERE rips_provider_code IS NULL AND provider_code IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['rips_provider_code']);
            $table->dropColumn('rips_provider_code');
        });
    }
};
