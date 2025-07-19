<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->boolean('requires_fev')->default(false)->after('has_incapacity');
        });
    }

    public function down(): void
    {
        Schema::table('rips_patient_services', function (Blueprint $table) {
            $table->dropColumn('requires_fev');
        });
    }
};
