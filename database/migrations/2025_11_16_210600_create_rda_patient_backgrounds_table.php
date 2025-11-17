<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rda_patient_backgrounds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_id')->nullable();
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();
            $table->foreignId('background_type_id')
                ->constrained('rda_background_types')
                ->restrictOnDelete();
            $table->text('description');
            $table->foreignId('cie10_id')
                ->nullable()
                ->constrained('cie10')
                ->restrictOnDelete();
            $table->foreignId('rips_cups_id')
                ->nullable()
                ->constrained('rips_cups')
                ->restrictOnDelete();
            $table->string('medication_name', 150)->nullable();
            $table->string('procedure_name', 150)->nullable();
            $table->string('related_person', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('patient_id');
            $table->index('background_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rda_patient_backgrounds');
    }
};
