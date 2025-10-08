<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefectiveProduct extends Model
{
    protected $fillable = [
        'cantidad',
        'fecha_registro',
        'estado',
        'observaciones',
        'producto_id'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'cantidad' => 'integer'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }
}
