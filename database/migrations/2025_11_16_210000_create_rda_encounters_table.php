<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rda_encounters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_id')->nullable();
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();
            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('doctors')
                ->nullOnDelete();
            $table->string('encounter_type');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->text('reason')->nullable();
            $table->string('vida_code', 255)->nullable();
            $table->enum('status', ['planned', 'in-progress', 'finished', 'cancelled'])->default('finished');
            $table->timestamps();

            $table->index(['tenant_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rda_encounters');
    }
};
