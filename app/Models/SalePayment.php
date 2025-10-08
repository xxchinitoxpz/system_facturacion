<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    protected $fillable = [
        'tipo_pago',
        'monto',
        'monto_recibido',
        'vuelto',
        'fecha_pago',
        'venta_id'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto' => 'decimal:2'
    ];

    /**
     * Obtiene la venta asociada al pago
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'venta_id');
    }

    /**
     * Scope para buscar pagos por tipo de pago
     */
    public function scopePorTipoPago($query, $tipoPago)
    {
        return $query->where('tipo_pago', $tipoPago);
    }

    /**
     * Scope para buscar pagos por venta
     */
    public function scopePorVenta($query, $ventaId)
    {
        return $query->where('venta_id', $ventaId);
    }

    /**
     * Scope para buscar pagos por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
    }
}
