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
        Schema::create('department_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del departamento/estado
            $table->foreignId('country_id')->constrained()->onDelete('cascade'); // Relación con el país
            $table->boolean('is_active')->default(true); // Habilitar/deshabilitar departamento
            $table->timestamps();
            $table->softDeletes(); // Eliminación suave
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_countries');
    }
};