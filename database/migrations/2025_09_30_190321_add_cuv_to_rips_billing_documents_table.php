<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->string('cuv', 255)->nullable()->after('submission_status');
            $table->index('cuv');
        });
    }

    public function down(): void
    {
        Schema::table('rips_billing_documents', function (Blueprint $table) {
            $table->dropIndex(['cuv']);
            $table->dropColumn('cuv');
        });
    }
};

