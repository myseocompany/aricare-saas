<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('cie10')) {
            Schema::create('cie10', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('description', 255);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cie10');
    }
};
