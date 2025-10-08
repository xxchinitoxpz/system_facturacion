<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Purchase extends Model
{
    protected $fillable = [
        'fecha_compra',
        'total',
        'observaciones',
        'comprobante_path',
        'sucursal_id',
        'proveedor_id',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_compra' => 'datetime',
        'total' => 'decimal:2',
    ];

    /**
     * Obtiene la sucursal de la compra
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Obtiene el proveedor de la compra
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'proveedor_id');
    }

    /**
     * Obtiene el usuario que realizó la compra
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtiene los productos de la compra (relación muchos a muchos)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_products', 'compra_id', 'producto_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'fecha_vencimiento')
                    ->withTimestamps();
    }

    /**
     * Scope para buscar compras por proveedor
     */
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    /**
     * Scope para buscar compras por sucursal
     */
    public function scopePorSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    /**
     * Scope para buscar compras por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para buscar compras por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_compra', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para compras con comprobante
     */
    public function scopeConComprobante($query)
    {
        return $query->whereNotNull('comprobante_path');
    }

    /**
     * Scope para compras sin comprobante
     */
    public function scopeSinComprobante($query)
    {
        return $query->whereNull('comprobante_path');
    }
}
