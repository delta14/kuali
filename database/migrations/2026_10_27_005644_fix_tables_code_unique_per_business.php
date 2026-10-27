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
        Schema::table('tables', function (Blueprint $table) {
            // "code" sólo debe ser único dentro de un mismo negocio, no
            // globalmente: dos negocios distintos pueden tener ambos una
            // "Mesa 1" (código mesa-1) sin chocar entre sí.
            $table->dropUnique('tables_code_unique');
            $table->unique(['business_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'code']);
            $table->unique('code');
        });
    }
};
