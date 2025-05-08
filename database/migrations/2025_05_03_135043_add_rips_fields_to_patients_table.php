<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Verificar si la columna 'document_type' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'document_type')) {
                $table->string('document_type', 3)->nullable()->after('patient_unique_id');
            }

            // Verificar si la columna 'document_number' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'document_number')) {
                $table->string('document_number', 15)->nullable()->after('document_type');
            }

            // Verificar si la columna 'birth_date' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('user_type');
            }

            // Verificar si la columna 'type_id' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'type_id')) {
                $table->smallInteger('type_id')->nullable()->after('document_number');
            }

            // Verificar si la columna 'sex_code' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'sex_code')) {
                $table->string('sex_code', 2)->nullable()->after('birth_date');
            }

            // Verificar si la columna 'country_code' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'country_code')) {
                $table->string('country_code', 5)->nullable()->after('sex_code');
            }

            // Verificar si la columna 'municipality_code' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'municipality_code')) {
                $table->string('municipality_code', 6)->nullable()->after('country_code');
            }

            // Verificar si la columna 'zone_code' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'zone_code')) {
                $table->integer('zone_code')->nullable()->after('municipality_code');
            }

            // Verificar si la columna 'country_of_origin' no existe antes de agregarla
            if (!Schema::hasColumn('patients', 'country_of_origin')) {
                $table->integer('country_of_origin')->nullable()->after('zone_code');
            }
        });
    }
    
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_number',
                'type_id',
                'birth_date',
                'sex_code',
                'country_code',
                'municipality_code',
                'zone_code',
                'country_of_origin',
            ]);
        });
    }
    
};

