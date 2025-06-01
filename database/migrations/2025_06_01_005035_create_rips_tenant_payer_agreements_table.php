<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_tenant_payer_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payer_id')->constrained('rips_payers')->onDelete('cascade');
            $table->string('name'); // Nombre del convenio
            $table->string('code', 50); // Código único
            $table->text('description')->nullable(); // Opcional
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['payer_id', 'code']); // Asegurar unicidad por pagador
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_tenant_payer_agreements');
    }
};
