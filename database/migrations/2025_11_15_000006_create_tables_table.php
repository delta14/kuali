<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();

            // MULTITENANT
            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');             // Mesa 1, Terraza 3, etc.
            $table->string('code')->unique();   // Código interno
            $table->string('qr_token')->unique(); // Token para QR
            $table->unsignedTinyInteger('capacity')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
