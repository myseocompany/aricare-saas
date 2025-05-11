<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('code')->unique(); // <- Añadido
            $table->string('name');
            $table->string('alpha2', 2)->unique();
            $table->string('alpha3', 3)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_countries');
    }
};
