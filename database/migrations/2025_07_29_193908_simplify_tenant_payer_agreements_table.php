<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration {
    public function up(): void
    {
        // Verifica si la FK existe antes de intentar eliminarla
        $foreignKeyExists = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'rips_tenant_payer_agreements'
            AND CONSTRAINT_NAME = 'rips_tenant_payer_agreements_payer_id_foreign'
            AND TABLE_SCHEMA = DATABASE()
        ");


        if ($foreignKeyExists) {
            DB::statement('ALTER TABLE rips_tenant_payer_agreements DROP FOREIGN KEY rips_tenant_payer_agreements_payer_id_foreign');
        }

        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            if (Schema::hasColumn('rips_tenant_payer_agreements', 'payer_id')) {
                $table->dropColumn('payer_id');
            }

            $table->string('code', 50)->nullable()->change();

            // Verifica si el índice 'name' ya existe antes de crearlo
            $indexes = DB::select("
                SHOW INDEXES FROM rips_tenant_payer_agreements WHERE Key_name = 'rips_tenant_payer_agreements_name_unique'
            ");

            if (empty($indexes)) {
                $table->unique('name', 'rips_tenant_payer_agreements_name_unique');
            }
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
