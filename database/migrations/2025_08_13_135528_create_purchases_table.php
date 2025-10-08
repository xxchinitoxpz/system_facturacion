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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_compra');
            $table->decimal('total', 10, 2);
            $table->text('observaciones')->nullable();
            $table->string('comprobante_path')->nullable();
            $table->foreignId('sucursal_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
