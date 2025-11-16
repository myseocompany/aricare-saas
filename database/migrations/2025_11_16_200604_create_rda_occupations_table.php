<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rda_occupations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique()->index();
            $table->string('name');
            $table->string('major_group_code')->nullable();
            $table->string('major_group_name')->nullable();
            $table->string('subgroup_code')->nullable();
            $table->string('subgroup_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rda_occupations');
    }
};
