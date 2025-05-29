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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del país
            $table->string('code', 3)->nullable(); // Código de país (ej: COL, USA)
            $table->boolean('is_active')->default(true); // Para habilitar/deshabilitar países
            $table->timestamps();
            $table->softDeletes();
             // Agregar índice a la columna 'name' para mejorar la búsqueda
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};