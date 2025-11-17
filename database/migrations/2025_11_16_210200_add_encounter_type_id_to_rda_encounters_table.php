<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rda_encounters', function (Blueprint $table) {
            if (!Schema::hasColumn('rda_encounters', 'encounter_type_id')) {
                $table->foreignId('encounter_type_id')
                    ->nullable()
                    ->after('doctor_id')
                    ->constrained('rda_encounter_types')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('rda_encounters', 'encounter_type')) {
                $table->dropColumn('encounter_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rda_encounters', function (Blueprint $table) {
            if (!Schema::hasColumn('rda_encounters', 'encounter_type')) {
                $table->string('encounter_type')->after('doctor_id');
            }

            if (Schema::hasColumn('rda_encounters', 'encounter_type_id')) {
                $table->dropConstrainedForeignId('encounter_type_id');
            }
        });
    }
};
