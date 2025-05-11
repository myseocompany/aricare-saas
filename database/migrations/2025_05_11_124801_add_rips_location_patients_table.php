<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Remover campos antiguos si existen
            if (Schema::hasColumn('patients', 'country_code')) {
                $table->dropColumn('country_code');
            }

            if (Schema::hasColumn('patients', 'municipality_code')) {
                $table->dropColumn('municipality_code');
            }

            if (Schema::hasColumn('patients', 'country_of_origin')) {
                $table->dropColumn('country_of_origin');
            }

            // Nuevos campos relacionales
            if (!Schema::hasColumn('patients', 'rips_country_id')) {
                $table->foreignId('rips_country_id')
                    ->nullable()
                    ->after('sex_code')
                    ->constrained('rips_countries')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('patients', 'rips_department_id')) {
                $table->foreignId('rips_department_id')
                    ->nullable()
                    ->after('rips_country_id')
                    ->constrained('rips_departments')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('patients', 'rips_municipality_id')) {
                $table->foreignId('rips_municipality_id')
                    ->nullable()
                    ->after('rips_department_id')
                    ->constrained('rips_municipalities')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('patients', 'zone_code')) {
                $table->string('zone_code', 2)->nullable()->after('rips_municipality_id');
            }

            if (!Schema::hasColumn('patients', 'country_of_origin_id')) {
                $table->foreignId('country_of_origin_id')
                    ->nullable()
                    ->after('zone_code')
                    ->constrained('rips_countries')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['rips_country_id']);
            $table->dropForeign(['rips_department_id']);
            $table->dropForeign(['rips_municipality_id']);
            $table->dropForeign(['country_of_origin_id']);

            $table->dropColumn([
                'rips_country_id',
                'rips_department_id',
                'rips_municipality_id',
                'zone_code',
                'country_of_origin_id',
            ]);

            // Restaurar columnas antiguas si fuera necesario (opcional)
            $table->string('country_code', 5)->nullable();
            $table->string('municipality_code', 6)->nullable();
            $table->integer('country_of_origin')->nullable();
        });
    }
};
