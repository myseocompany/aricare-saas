<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_payer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // EPS, EAPB, Empresa, ARL, etc.
            $table->string('description')->nullable(); // Opcional, para más detalle
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_payer_types');
    }
};
