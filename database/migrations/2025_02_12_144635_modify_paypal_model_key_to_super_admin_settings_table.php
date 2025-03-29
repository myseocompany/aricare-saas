<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('super_admin_settings')
            ->where('key', 'paypal_mode')
            ->update(['value' => 'sandbox']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('super_admin_settings')
            ->where('key', 'paypal_mode')
            ->update(['value' => 'sandbox']);
    }
};
