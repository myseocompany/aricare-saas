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
        Schema::create('rips_billing_documents', function (Blueprint $table) {
            $table->id();

            // UUID del tenant (hospital, IPS)
            $table->uuid('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();

            $table->unsignedBigInteger('type_id'); // Tipo de documento (Factura, Recibo de Caja)

            $table->string('document_number', 30);   // Numero interno del documento
            $table->dateTime('issued_at');            // Fecha y hora de emisión
            $table->string('cufe', 100)->nullable();   // Código Único de Factura Electrónica DIAN
            $table->string('uuid_dian', 100)->nullable(); // UUID generado por la DIAN

            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('copay_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rips_billing_documents');
    }
};
