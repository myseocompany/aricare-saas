<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rips_model_identification_types', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->foreignId('identification_type_id')->constrained('rips_identification_types')->onDelete('cascade');

            // timestamps con CURRENT_TIMESTAMP por defecto
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['model_type', 'identification_type_id'], 'modeltype_identification_unique');
        });

        // Insertar tipos permitidos para el modelo App\Models\Doctor
        DB::table('rips_model_identification_types')->insert([
            ['model_type' => 'App\\Models\\Doctor', 'identification_type_id' => 2],
            ['model_type' => 'App\\Models\\Doctor', 'identification_type_id' => 4],
            ['model_type' => 'App\\Models\\Doctor', 'identification_type_id' => 10],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rips_model_identification_types');
    }
};
