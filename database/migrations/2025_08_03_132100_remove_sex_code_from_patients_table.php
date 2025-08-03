<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'sex_code')) {
                $table->dropColumn('sex_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'sex_code')) {
                $table->string('sex_code', 2)->nullable()->after('birth_date');
            }
        });
    }
};
