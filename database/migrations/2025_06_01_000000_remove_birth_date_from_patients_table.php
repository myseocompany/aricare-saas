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
        if (Schema::hasColumn('patients', 'birth_date')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('birth_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('patients', 'birth_date')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->date('birth_date')->nullable()->after('type_id');
            });
        }
    }
};
