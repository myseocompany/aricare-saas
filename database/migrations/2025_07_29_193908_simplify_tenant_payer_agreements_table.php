<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Agrega tenant_id si no existe
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('rips_tenant_payer_agreements', 'tenant_id')) {
                $table->string('tenant_id')->after('id')->index();
            }
        });

        // Elimina restricciones existentes
        $foreignKeyExists = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'rips_tenant_payer_agreements'
            AND CONSTRAINT_NAME = 'rips_tenant_payer_agreements_payer_id_foreign'
            AND TABLE_SCHEMA = DATABASE()
        ");

        if ($foreignKeyExists) {
            Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
                $table->dropForeign(['payer_id']);
                $table->dropUnique('rips_tenant_payer_agreements_payer_id_code_unique');
            });
        }

        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('rips_tenant_payer_agreements', 'payer_id')) {
                $table->dropColumn('payer_id');
            }

            $table->string('code', 50)->nullable()->change();

            // Eliminar posibles índices anteriores
            DB::statement("DROP INDEX IF EXISTS rips_tenant_payer_agreements_name_unique ON rips_tenant_payer_agreements");

            // Crear índice compuesto correcto
            $table->unique(['tenant_id', 'name'], 'tenant_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            $table->dropUnique('tenant_name_unique');

            $table->foreignId('payer_id')->constrained('rips_payers')->onDelete('cascade');
            $table->unique(['payer_id', 'code']);
            $table->string('code', 50)->nullable(false)->change();
            $table->dropColumn('tenant_id');
        });
    }
};
