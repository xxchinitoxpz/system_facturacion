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

        // Debe correr antes de cualquier migración que actualice filas de estas tablas
        foreach ($this->columnas as $tabla => $columna) {
            DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$columna}` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }
    }

    public function down(): void
    {
        // No se vuelve a agregar ON UPDATE CURRENT_TIMESTAMP: provocaba la pérdida de fechas
    }
};
