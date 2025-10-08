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
        Schema::create('cash_box_movements', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['apertura', 'cierre', 'ingreso', 'salida']);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'billetera_virtual', 'tarjeta'])->nullable();
            $table->decimal('monto', 10, 2);
            $table->text('descripcion');
            $table->foreignId('sesion_caja_id')->constrained('cash_box_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_box_movements');
    }
};
