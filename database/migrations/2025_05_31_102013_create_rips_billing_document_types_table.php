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
        Schema::create('rips_billing_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Nombre del tipo: Factura, Recibo de Caja, etc.
            $table->string('description', 255)->nullable(); // Descripción opcional
            $table->timestamps();
        });

        // Y de una vez insertamos los valores básicos
        DB::table('rips_billing_document_types')->insert([
            ['name' => 'Factura', 'description' => 'Documento de venta de servicios de salud'],
            ['name' => 'Recibo de Caja', 'description' => 'Documento de registro de pago recibido'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rips_billing_document_types');
    }
};
