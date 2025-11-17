<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rda_encounters', function (Blueprint $table) {
            if (!Schema::hasColumn('rda_encounters', 'status_id')) {
                $table->foreignId('status_id')
                    ->nullable()
                    ->after('encounter_type_id')
                    ->constrained('rda_encounter_statuses')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('rda_encounters', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rda_encounters', function (Blueprint $table) {
            if (!Schema::hasColumn('rda_encounters', 'status')) {
                $table->enum('status', ['planned', 'in-progress', 'finished', 'cancelled'])->default('finished')->after('encounter_type_id');
            }

            if (Schema::hasColumn('rda_encounters', 'status_id')) {
                $table->dropConstrainedForeignId('status_id');
            }
        });
    }
};
