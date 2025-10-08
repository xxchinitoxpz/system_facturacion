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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_venta');
            $table->decimal('total', 10, 2);
            $table->string('estado');
            $table->text('observaciones')->nullable();
            $table->foreignId('sucursal_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_comprobante');
            $table->string('serie')->nullable();
            $table->integer('correlativo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
