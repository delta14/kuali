<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->unsignedInteger('last_order_number')->default(0)->after('is_active');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('order_number')->nullable()->after('id');
        });

        // Backfill: a cada pedido existente se le asigna un folio secuencial
        // propio de su negocio, en el orden en que fue creado.
        $businessIds = DB::table('orders')->select('business_id')->distinct()->pluck('business_id');

        foreach ($businessIds as $businessId) {
            $orders = DB::table('orders')
                ->where('business_id', $businessId)
                ->orderBy('id')
                ->pluck('id');

            $number = 0;
            foreach ($orders as $orderId) {
                $number++;
                DB::table('orders')->where('id', $orderId)->update(['order_number' => $number]);
            }

            DB::table('businesses')->where('id', $businessId)->update(['last_order_number' => $number]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->unique(['business_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'order_number']);
            $table->dropColumn('order_number');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('last_order_number');
        });
    }
};
