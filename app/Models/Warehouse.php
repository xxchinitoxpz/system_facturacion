<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
    ];

    /**
     * Obtiene la sucursal a la que pertenece el almacén
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Obtiene los productos en este almacén (con información de inventario)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_warehouse', 'almacen_id', 'producto_id')
                    ->withPivot('stock', 'fecha_vencimiento')
                    ->withTimestamps();
    }

    /**
     * Scope para obtener almacenes activos
     */
    public function scopeActivos($query)
    {
        return $query;
    }
}