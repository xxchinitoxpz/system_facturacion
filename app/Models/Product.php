<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'barcode',
        'categoria_id',
        'marca_id',
    ];

    /**
     * Obtiene la categoría del producto
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    /**
     * Obtiene la marca del producto
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'marca_id');
    }

    /**
     * Obtiene las presentaciones del producto
     */
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class, 'producto_id');
    }

    /**
     * Obtiene los almacenes donde está disponible el producto (con información de inventario)
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse', 'producto_id', 'almacen_id')
                    ->withPivot('stock', 'fecha_vencimiento')
                    ->withTimestamps();
    }

    /**
     * Obtiene los combos donde está incluido el producto
     */
    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'product_combos', 'producto_id', 'combo_id')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    /**
     * Scope para buscar productos por código de barras
     */
    public function scopePorBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope para buscar productos por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope para buscar productos por marca
     */
    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    /**
     * Obtiene los productos defectuosos asociados
     */
    public function defectiveProducts(): HasMany
    {
        return $this->hasMany(DefectiveProduct::class, 'producto_id');
    }

    /**
     * Obtiene las ventas donde está incluido el producto
     */
    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'sale_products', 'producto_id', 'venta_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'nombre_producto')
                    ->withTimestamps();
    }

    /**
     * Obtiene las compras donde está incluido el producto
     */
    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_products', 'producto_id', 'compra_id')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'fecha_vencimiento')
                    ->withTimestamps();
    }

}
