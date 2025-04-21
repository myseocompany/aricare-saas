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
        Schema::create('cups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index();          // Codigo
            $table->text('name');                   // Nombre
            $table->text('description')->nullable();  // Descripcion
            $table->string('group')->nullable();      // Extra_I:Cobertura (SUBCATEGORIA)
            $table->string('subgroup_code')->nullable(); // Extra_II
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cups');
    }
};