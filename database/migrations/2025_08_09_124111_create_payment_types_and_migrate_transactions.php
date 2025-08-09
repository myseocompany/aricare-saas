<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear catálogo de tipos de pago
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();   // ej. stripe, paypal, wompi
            $table->string('name', 100);            // etiqueta legible
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2) Sembrar valores iniciales (según constantes del modelo Transaction)
        //    1=Stripe, 2=PayPal, 3=RazorPay, 4=Manual, 5=Paytm, 6=Paystack, 7=PhonePe, 8=FlutterWave, 9=Wompi
        DB::table('payment_types')->insert([
            ['code' => 'stripe',      'name' => 'Stripe',       'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'paypal',      'name' => 'PayPal',       'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'razorpay',    'name' => 'RazorPay',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'manual',      'name' => 'Manual',       'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'paytm',       'name' => 'Paytm',        'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'paystack',    'name' => 'Paystack',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'phonepe',     'name' => 'PhonePe',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'flutterwave', 'name' => 'FlutterWave',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'wompi',       'name' => 'Wompi',        'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3) Agregar nueva columna FK a transactions (sin romper datos existentes)
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_type_id')->nullable()->after('transaction_id');
            $table->index('payment_type_id', 'transactions_payment_type_id_index');
        });

        // 4) Migrar datos legacy de transactions.payment_type (int) -> payment_type_id (FK)
        //    Mapeo según tus constantes:
        //    1=stripe, 2=paypal, 3=razorpay, 4=manual, 5=paytm, 6=paystack, 7=phonepe, 8=flutterwave, 9=wompi
        $map = [
            1 => 'stripe',
            2 => 'paypal',
            3 => 'razorpay',
            4 => 'manual',
            5 => 'paytm',
            6 => 'paystack',
            7 => 'phonepe',
            8 => 'flutterwave',
            9 => 'wompi',
        ];

        // Obtener IDs reales del catálogo
        $typeIdsByCode = DB::table('payment_types')->pluck('id', 'code');

        // Actualizar por lotes
        foreach ($map as $legacyInt => $code) {
            if (isset($typeIdsByCode[$code])) {
                DB::table('transactions')
                    ->where('payment_type', $legacyInt)
                    ->update(['payment_type_id' => $typeIdsByCode[$code]]);
            }
        }

        // 5) Volver NOT NULL y agregar FK (sin requerir doctrine/dbal usamos SQL crudo)
        //    Si usas otra BD distinta a MySQL, ajusta el ALTER.
        DB::statement('ALTER TABLE `transactions` MODIFY `payment_type_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('payment_type_id', 'transactions_payment_type_id_fk')
                  ->references('id')->on('payment_types')
                  ->cascadeOnUpdate();
        });

        // 6) (Opcional pero recomendado) Eliminar la columna legacy e índice viejo
        //    Laravel suele borrar el índice al borrar la columna, pero por si acaso:
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('transactions_payment_type_index');
            });
        } catch (\Throwable $e) {
            // índice puede no existir según el entorno; ignoramos
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });
    }

    public function down(): void
    {
        // 1) Restaurar columna legacy
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('payment_type')->nullable()->after('transaction_id');
            $table->index('payment_type', 'transactions_payment_type_index');
        });

        // 2) Reconstruir mapeo inverso desde catálogo
        $codeToLegacy = [
            'stripe'      => 1,
            'paypal'      => 2,
            'razorpay'    => 3,
            'manual'      => 4,
            'paytm'       => 5,
            'paystack'    => 6,
            'phonepe'     => 7,
            'flutterwave' => 8,
            'wompi'       => 9,
        ];

        $codesById = DB::table('payment_types')->pluck('code', 'id');

        // Poner el entero legacy según el FK actual
        $rows = DB::table('transactions')->select('id', 'payment_type_id')->get();
        foreach ($rows as $row) {
            $code = $codesById[$row->payment_type_id] ?? null;
            $legacy = $code ? ($codeToLegacy[$code] ?? null) : null;
            if ($legacy !== null) {
                DB::table('transactions')->where('id', $row->id)->update(['payment_type' => $legacy]);
            }
        }

        // 3) Quitar FK y columna nueva
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_payment_type_id_fk');
            $table->dropIndex('transactions_payment_type_id_index');
            $table->dropColumn('payment_type_id');
        });

        // 4) Borrar catálogo
        Schema::dropIfExists('payment_types');

        // 5) Dejar la columna legacy como NOT NULL (si lo necesitas)
        DB::statement('ALTER TABLE `transactions` MODIFY `payment_type` INT UNSIGNED NOT NULL');
    }
};
