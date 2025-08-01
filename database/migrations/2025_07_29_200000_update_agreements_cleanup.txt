<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration {
    public function up(): void
    {
        $foreignKeyExists = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'rips_tenant_payer_agreements'
            AND CONSTRAINT_NAME = 'rips_tenant_payer_agreements_payer_id_foreign'
            AND TABLE_SCHEMA = DATABASE()
        ");
        if($foreignKeyExists)
            Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
                // Eliminar restricción de clave foránea y columna de payer_id
                $table->dropForeign(['payer_id']);
                $table->dropUnique('rips_tenant_payer_agreements_payer_id_code_unique');
            });

        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            // Eliminar restricción de clave foránea y columna de payer_id
            $table->dropColumn('payer_id');

            // Hacer que el campo 'code' (número de identificación) sea opcional
            $table->string('code', 50)->nullable()->change();

            // Nueva unicidad solo por nombre
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            $table->dropUnique(['name']);

            $table->foreignId('payer_id')->constrained('rips_payers')->onDelete('cascade');
            $table->unique(['payer_id', 'code']);
            $table->string('code', 50)->nullable(false)->change();
        });
    }
};
