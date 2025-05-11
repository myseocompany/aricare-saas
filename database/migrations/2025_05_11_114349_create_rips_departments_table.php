<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('rips_country_id')
                ->constrained('rips_countries')
                ->onDelete('cascade');

            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_departments');
    }
};
