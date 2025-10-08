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
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->id();
            $table->integer('stock')->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('almacen_id')->constrained('warehouses')->onDelete('cascade');
            $table->timestamps();
            
            // Índice compuesto que incluye fecha de vencimiento para permitir el mismo producto
            // en el mismo almacén con diferentes fechas de vencimiento
            $table->unique(['producto_id', 'almacen_id', 'fecha_vencimiento'], 'product_warehouse_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse');
    }
};
