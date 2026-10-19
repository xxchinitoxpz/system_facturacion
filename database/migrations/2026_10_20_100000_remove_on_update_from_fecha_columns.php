<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Columnas de fecha que MySQL creó con ON UPDATE CURRENT_TIMESTAMP:
     * cualquier UPDATE sobre la fila reemplazaba la fecha por la hora actual.
     */
    private array $columnas = [
        'sales' => 'fecha_venta',
        'sale_payments' => 'fecha_pago',
        'purchases' => 'fecha_compra',
        'cash_box_sessions' => 'fecha_hora_apertura',
    ];

    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            return;
        }

        foreach ($this->columnas as $tabla => $columna) {
            DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$columna}` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }

        // Restaurar la fecha original de boletas/facturas: al registrar la venta,
        // fecha_venta y created_at se guardan con la misma hora
        DB::table('sales')
            ->whereNotNull('estado_sunat')
            ->whereNotNull('created_at')
            ->whereColumn('fecha_venta', '!=', 'created_at')
            ->update(['fecha_venta' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // No se vuelve a agregar ON UPDATE CURRENT_TIMESTAMP: provocaba la pérdida de fechas
    }
};
