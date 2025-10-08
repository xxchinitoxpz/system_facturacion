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
        Schema::create('sunat_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('sales')->onDelete('cascade');
            $table->string('numero_documento');
            $table->string('tipo_documento'); // '01' para factura, '03' para boleta
            $table->string('xml_path'); // Path del archivo XML guardado
            $table->string('cdr_path')->nullable(); // Path del archivo CDR guardado
            $table->string('hash_documento');
            $table->string('codigo_respuesta');
            $table->text('descripcion_respuesta');
            $table->boolean('exitoso');
            $table->json('respuesta_completa')->nullable(); // Para guardar toda la respuesta JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_responses');
    }
};
