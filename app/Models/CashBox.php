<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
    ];

    /**
     * Relación con la sucursal
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    /**
     * Relación con las sesiones de caja
     */
    public function sessions()
    {
        return $this->hasMany(CashBoxSession::class, 'caja_id');
    }

    /**
     * Relación con la sesión activa de la caja
     */
    public function activeSession()
    {
        return $this->hasOne(CashBoxSession::class, 'caja_id')->where('estado', 'abierta');
    }
}
