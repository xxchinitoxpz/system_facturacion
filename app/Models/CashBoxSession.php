<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBoxSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'monto_apertura',
        'fecha_hora_apertura',
        'monto_cierre',
        'fecha_hora_cierre',
        'estado',
        'caja_id',
        'sucursal_id',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_hora_apertura' => 'datetime',
        'fecha_hora_cierre' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
    ];

    /**
     * Relación con la caja
     */
    public function cashBox()
    {
        return $this->belongsTo(CashBox::class, 'caja_id');
    }

    /**
     * Relación con la sucursal
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con los movimientos
     */
    public function movements()
    {
        return $this->hasMany(CashBoxMovement::class, 'sesion_caja_id');
    }

    /**
     * Calcular el total de ingresos
     */
    public function getTotalIngresosAttribute()
    {
        return $this->movements()
            ->where('tipo', 'ingreso')
            ->sum('monto');
    }

    /**
     * Calcular el total de salidas
     */
    public function getTotalSalidasAttribute()
    {
        return $this->movements()
            ->where('tipo', 'salida')
            ->sum('monto');
    }

    /**
     * Calcular el saldo actual
     */
    public function getSaldoActualAttribute()
    {
        return $this->monto_apertura + $this->total_ingresos - $this->total_salidas;
    }
}
