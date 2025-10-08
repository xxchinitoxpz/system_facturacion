<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'ultimo_correlativo',
        'sucursal_id'
    ];

    /**
     * Relación con la sucursal
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }
}
