<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'nro_documento',
        'telefono',
        'email',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Obtiene las compras realizadas a este proveedor
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'proveedor_id');
    }
}
