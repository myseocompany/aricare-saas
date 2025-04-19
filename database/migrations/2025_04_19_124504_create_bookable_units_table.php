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
        Schema::create('bookable_units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('tenant_id')->nullable(); // multitenant
            $table->timestamps();
    
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }
        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookable_units');
    }
};
