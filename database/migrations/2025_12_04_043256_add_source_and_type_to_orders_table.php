<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // origen del pedido: qr | counter | phone | etc.
            $table->string('source', 20)->default('qr')->after('status');

            // tipo de servicio: consumir aquí o para llevar (opcional)
            $table->string('order_type', 20)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'order_type']);
        });
    }
};

