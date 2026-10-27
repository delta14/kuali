<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Etiqueta libre para pedidos de mostrador (no están ligados a
            // una fila de "tables"), y etiqueta de estado legible que se
            // guarda junto al pedido en vez de derivarse siempre de "status".
            $table->string('table_name')->nullable()->after('table_id');
            $table->string('status_label')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['table_name', 'status_label']);
        });
    }
};
