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
        Schema::create('defective_products', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->date('fecha_registro');
            $table->enum('estado', ['cambiado', 'almacenado', 'deshechado'])->default('cambiado');
            $table->text('observaciones')->nullable();
            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defective_products');
    }
};
