<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtiene los productos de esta categoría.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'categoria_id');
    }

    /**
     * Scope para filtrar categorías activas.
     */
    public function scopeActivas($query)
    {
        return $query;
    }
}
