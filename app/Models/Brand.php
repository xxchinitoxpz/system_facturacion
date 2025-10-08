<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtiene los productos de esta marca.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'marca_id');
    }

    /**
     * Scope para filtrar marcas activas.
     */
    public function scopeActivas($query)
    {
        return $query;
    }
}
