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
         // En users: agregar campos y foreign key
        Schema::table('users', function (Blueprint $table) {
            //$table->string('rips_identification_number', 15)->nullable(); // Cambia 'some_column' por columna existente donde quieres agregar
            //$table->unsignedBigInteger('rips_identification_type_id')->nullable();
            $table->foreign('rips_identification_type_id')
                ->references('id')
                ->on('rips_identification_types')
                ->onDelete('set null');
        });

        // En patients: eliminar foreign key y columnas
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['rips_identification_type_id']);
            $table->dropColumn('rips_identification_type_id');
            $table->dropColumn('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir en users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rips_identification_type_id']);
            $table->dropColumn('rips_identification_type_id');
            $table->dropColumn('rips_identification_number');
        });

        // Revertir en patients
        Schema::table('patients', function (Blueprint $table) {
            $table->unsignedBigInteger('rips_identification_type_id')->nullable()->after('document_number');
            $table->string('document_number', 15)->nullable()->after('document_type');
            $table->foreign('rips_identification_type_id')
                ->references('id')
                ->on('rips_identification_types')
                ->onDelete('set null');
        });
    }
};
