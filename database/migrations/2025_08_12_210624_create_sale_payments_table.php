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
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('tipo_pago');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_recibido', 10, 2);
            $table->decimal('vuelto', 10, 2);
            $table->timestamp('fecha_pago');
            $table->foreignId('venta_id')->constrained('sales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
