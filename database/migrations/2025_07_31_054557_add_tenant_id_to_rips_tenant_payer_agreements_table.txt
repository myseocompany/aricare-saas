<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            $table->string('tenant_id')->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('rips_tenant_payer_agreements', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
