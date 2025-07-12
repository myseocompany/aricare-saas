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
           Schema::table('patients', function (Blueprint $table) {
               $table->string('invoice_number', 50)->nullable()->after('country_of_origin_id');
               $table->enum('note_type', ['credit', 'debit'])->nullable()->after('invoice_number');
               $table->string('note_number', 50)->nullable()->after('note_type');
           });
       }
   
       public function down(): void
       {
           Schema::table('patients', function (Blueprint $table) {
               $table->dropColumn(['invoice_number', 'note_type', 'note_number']);
           });
       }
};
