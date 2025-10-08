<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'fecha_venta',
        'total',
        'estado',
        'observaciones',
        'sucursal_id',
        'cliente_id',
        'usuario_id',
        'tipo_comprobante',
        'serie',
        'correlativo',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
        'correlativo' => 'integer',
    ];

    /**
     * Obtiene la sucursal de la venta
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Obtiene el cliente de la venta
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    /**
     * Obtiene el usuario que realizó la venta
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtiene los productos de la venta (relación muchos a muchos)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_products', 'venta_id', 'producto_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'nombre_producto', 'presentacion_id', 'fecha_vencimiento')
                    ->withTimestamps();
    }

    /**
     * Obtiene los combos de la venta (relación muchos a muchos)
     */
    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'sale_products', 'venta_id', 'combo_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Obtiene los pagos de la venta
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'venta_id');
    }

    /**
     * Obtiene los movimientos de caja de la venta
     */
    public function cashBoxMovements(): HasMany
    {
        return $this->hasMany(CashBoxMovement::class, 'venta_id');
    }

    /**
     * Obtiene las respuestas de SUNAT de la venta
     */
    public function sunatResponses(): HasMany
    {
        return $this->hasMany(SunatResponse::class, 'venta_id');
    }

    /**
     * Scope para buscar ventas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para buscar ventas por cliente
     */
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para buscar ventas por sucursal
     */
    public function scopePorSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope para buscar ventas por tipo de comprobante
     */
    public function scopePorTipoComprobante($query, $tipoComprobante)
    {
        return $query->where('tipo_comprobante', $tipoComprobante);
    }

    /**
     * Scope para buscar ventas por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para ventas activas (no anuladas)
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', '!=', 'anulado');
    }
}
