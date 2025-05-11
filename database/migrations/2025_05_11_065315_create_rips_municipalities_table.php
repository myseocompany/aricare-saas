<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_municipalities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code')->unique(); // Ej: 5001
            $table->string('name'); // Ej: Medellín
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_municipalities');
    }
};
