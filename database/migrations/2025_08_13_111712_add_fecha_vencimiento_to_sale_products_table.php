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
        Schema::table('sale_products', function (Blueprint $table) {
            // Agregar campo JSON para almacenar información de lotes vendidos
            $table->json('fecha_vencimiento')->nullable()->after('presentacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_products', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
};
