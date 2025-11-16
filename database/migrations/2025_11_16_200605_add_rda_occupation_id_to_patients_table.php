<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (! Schema::hasColumn('patients', 'rda_occupation_id')) {
                $table->foreignId('rda_occupation_id')
                    ->nullable()
                    ->constrained('rda_occupations')
                    ->nullOnDelete()
                    ->after('occupation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'rda_occupation_id')) {
                $table->dropForeign(['rda_occupation_id']);
                $table->dropColumn('rda_occupation_id');
            }
        });
    }
};
