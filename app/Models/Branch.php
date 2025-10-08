<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'empresa_id'
    ];

    /**
     * Relación con la empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    /**
     * Relación con las series de comprobantes
     */
    public function documentSeries()
    {
        return $this->hasMany(DocumentSeries::class, 'sucursal_id');
    }

    /**
     * Relación con los empleados
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'sucursal_id');
    }

    /**
     * Relación con los usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }

    /**
     * Relación con los almacenes
     */
    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'sucursal_id');
    }

    /**
     * Relación con las cajas
     */
    public function cashBoxes(): HasMany
    {
        return $this->hasMany(CashBox::class, 'sucursal_id');
    }
}