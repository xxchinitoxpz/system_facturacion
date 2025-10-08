<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    protected $fillable = [
        'nombre',
        'precio',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'precio' => 'decimal:2'
    ];

    /**
     * Relación muchos a muchos con Product
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_combos', 'combo_id', 'producto_id')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    /**
     * Obtiene las ventas donde está incluido el combo
     */
    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'sale_products', 'combo_id', 'venta_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal')
                    ->withTimestamps();
    }

}
