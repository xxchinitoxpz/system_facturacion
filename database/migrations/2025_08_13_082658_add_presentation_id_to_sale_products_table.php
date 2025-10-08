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
            $table->foreignId('presentacion_id')->nullable()->constrained('presentations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_products', function (Blueprint $table) {
            $table->dropForeign(['presentacion_id']);
            $table->dropColumn('presentacion_id');
        });
    }
};
