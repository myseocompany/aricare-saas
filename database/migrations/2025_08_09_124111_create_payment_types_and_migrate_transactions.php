<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear catálogo
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2) Seed
        $now = now();
        DB::table('payment_types')->insert([
            ['id'=>1,'code'=>'stripe','name'=>'Stripe','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>2,'code'=>'paypal','name'=>'PayPal','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>3,'code'=>'razorpay','name'=>'RazorPay','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>4,'code'=>'manual','name'=>'Manual','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>5,'code'=>'paytm','name'=>'Paytm','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>6,'code'=>'paystack','name'=>'Paystack','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>7,'code'=>'phonepe','name'=>'PhonePe','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>8,'code'=>'flutterwave','name'=>'FlutterWave','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['id'=>9,'code'=>'wompi','name'=>'Wompi','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        // 3) Asegurar que el tipo coincida (BIGINT UNSIGNED) ANTES de la FK
        // Opción A: con SQL crudo (no requiere doctrine/dbal)
        DB::statement('ALTER TABLE `transactions` MODIFY `payment_type` BIGINT UNSIGNED NOT NULL');

        // (Si prefieres Schema::change(), instala doctrine/dbal y usa:
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->unsignedBigInteger('payment_type')->change();
        // });)

        // 4) Agregar la FK
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('payment_type', 'transactions_payment_type_fk')
                ->references('id')->on('payment_types')
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // Quitar FK
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_payment_type_fk');
        });

        // (Opcional) Devolver el tipo original si lo necesitas
        DB::statement('ALTER TABLE `transactions` MODIFY `payment_type` INT UNSIGNED NOT NULL');

        // Borrar catálogo
        Schema::dropIfExists('payment_types');
    }
};
