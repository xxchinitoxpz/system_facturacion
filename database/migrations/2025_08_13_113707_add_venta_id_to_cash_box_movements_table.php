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
        Schema::table('cash_box_movements', function (Blueprint $table) {
            // Agregar campo para vincular con venta
            $table->foreignId('venta_id')->nullable()->constrained('sales')->onDelete('set null')->after('sesion_caja_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_box_movements', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');
        });
    }
};
