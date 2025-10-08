<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBoxMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'metodo_pago',
        'monto',
        'descripcion',
        'sesion_caja_id',
        'venta_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    /**
     * Relación con la sesión de caja
     */
    public function session()
    {
        return $this->belongsTo(CashBoxSession::class, 'sesion_caja_id');
    }

    /**
     * Relación con la venta
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'venta_id');
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('tipo', $type);
    }

    /**
     * Scope para filtrar por método de pago
     */
    public function scopeOfPaymentMethod($query, $method)
    {
        return $query->where('metodo_pago', $method);
    }

    /**
     * Obtener el total de movimientos por tipo y método de pago
     */
    public static function getTotalByTypeAndMethod($sessionId, $type, $method = null)
    {
        $query = static::where('sesion_caja_id', $sessionId)
            ->where('tipo', $type);
        
        if ($method) {
            $query->where('metodo_pago', $method);
        }
        
        return $query->sum('monto');
    }
}
