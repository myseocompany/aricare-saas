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
            // Relación con tipos de documento
            $table->foreignId('rips_identification_type_id')
                ->nullable()
                ->constrained('rips_identification_types')
                ->nullOnDelete()
                ->after('hospital_name');

            // Número de documento
            $table->string('rips_identification_number', 20)
                ->nullable()
                ->after('rips_identification_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['rips_identification_type_id']);
            $table->dropColumn(['rips_identification_type_id', 'rips_identification_number']);
        });
    }

};
