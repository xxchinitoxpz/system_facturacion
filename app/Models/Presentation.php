<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presentation extends Model
{
    protected $fillable = [
        'nombre',
        'precio_venta',
        'unidades',
        'producto_id',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'unidades' => 'integer',
    ];

    /**
     * Obtiene el producto al que pertenece esta presentación
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    /**
     * Scope para obtener presentaciones por precio mínimo
     */
    public function scopePrecioMinimo($query, $precio)
    {
        return $query->where('precio_venta', '>=', $precio);
    }

    /**
     * Scope para obtener presentaciones por precio máximo
     */
    public function scopePrecioMaximo($query, $precio)
    {
        return $query->where('precio_venta', '<=', $precio);
    }

    /**
     * Scope para obtener presentaciones por producto
     */
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }
}
