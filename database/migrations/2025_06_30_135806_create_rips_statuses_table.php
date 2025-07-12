<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRipsStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rips_statuses')) {
            Schema::create('rips_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');  // Nombre del estado (e.g. 'Creado', 'Validado')
                $table->text('description')->nullable();  // Descripción opcional
                $table->timestamps();
                $table->unique('name');  // Asegura que el nombre del estado sea único
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_statuses');
    }
}
