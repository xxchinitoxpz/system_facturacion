<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'nro_documento',
        'telefono',
        'email',
        'direccion',
        'cargo',
        'fecha_ingreso',
        'activo',
        'sucursal_id'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'activo' => 'boolean'
    ];

    /**
     * Obtiene la sucursal a la que pertenece el empleado.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Relación con el usuario asociado
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'empleado_id');
    }

    /**
     * Scope para filtrar empleados activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar empleados inactivos.
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }
}
