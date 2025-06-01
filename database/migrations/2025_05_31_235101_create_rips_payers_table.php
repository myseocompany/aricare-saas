<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_payers', function (Blueprint $table) {
            $table->id();
            $table->char('tenant_id', 36);
            $table->foreignId('type_id')->constrained('rips_payer_types'); // Asegúrate que existe tabla types o creamos una
            $table->string('name');
            $table->string('identification', 20);
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_payers');
    }
};
