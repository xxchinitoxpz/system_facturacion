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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('tipo_documento');
            $table->string('nro_documento');
            $table->string('telefono');
            $table->string('email');
            $table->text('direccion');
            $table->string('cargo');
            $table->date('fecha_ingreso');
            $table->boolean('activo')->default(true);
            $table->foreignId('sucursal_id')->constrained('branches')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
