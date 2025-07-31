<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            // Eliminar restricción de clave foránea y columna de payer_id
            $table->dropForeign(['payer_id']);
            $table->dropUnique('rips_tenant_payer_agreements_payer_id_code_unique');
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
