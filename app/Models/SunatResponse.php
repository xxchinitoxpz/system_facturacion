<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SunatResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'numero_documento',
        'tipo_documento',
        'xml_path',
        'cdr_path',
        'hash_documento',
        'codigo_respuesta',
        'descripcion_respuesta',
        'exitoso',
        'respuesta_completa',
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'respuesta_completa' => 'array',
    ];

    /**
     * Get the sale associated with the SUNAT response.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'venta_id');
    }
}
