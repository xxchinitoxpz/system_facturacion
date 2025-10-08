<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Buscar productos por nombre o código de barras
     */
    public function buscar(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $productos = Product::with(['category', 'brand'])
            ->where(function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('barcode', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
        
        return response()->json($productos);
    }
    
    /**
     * Obtener presentaciones de un producto
     */
    public function presentaciones(Product $producto): JsonResponse
    {
        $presentaciones = Presentation::where('producto_id', $producto->id)
            ->orderBy('precio_venta')
            ->get();
        
        return response()->json($presentaciones);
    }

    /**
     * Obtener stock disponible de un producto
     */
    public function stockDisponible(Request $request): JsonResponse
    {
        $productoId = $request->get('producto_id');
        $sucursalId = $request->get('sucursal_id');
        
        if (!$productoId || !$sucursalId) {
            return response()->json(['error' => 'Se requiere producto_id y sucursal_id'], 400);
        }

        try {
            // Obtener stock disponible (solo productos no vencidos)
            $stockDisponible = DB::table('product_warehouse')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->where('product_warehouse.producto_id', $productoId)
                ->where('warehouses.sucursal_id', $sucursalId)
                ->where(function($query) {
                    $query->whereNull('product_warehouse.fecha_vencimiento')
                          ->orWhere('product_warehouse.fecha_vencimiento', '>=', now()->toDateString());
                })
                ->sum('product_warehouse.stock');

            // Obtener presentaciones del producto con stock calculado
            $presentaciones = Presentation::where('producto_id', $productoId)
                ->orderBy('precio_venta')
                ->get()
                ->map(function($presentacion) use ($stockDisponible) {
                    $maximoPresentaciones = $stockDisponible > 0 ? floor($stockDisponible / $presentacion->unidades) : 0;
                    
                    return [
                        'id' => $presentacion->id,
                        'nombre' => $presentacion->nombre,
                        'precio_venta' => $presentacion->precio_venta,
                        'unidades' => $presentacion->unidades,
                        'stock_disponible' => $stockDisponible,
                        'maximo_presentaciones' => $maximoPresentaciones,
                        'puede_vender' => $maximoPresentaciones > 0
                    ];
                });

            // Obtener precio unitario del producto (precio de la presentación más barata dividido por unidades)
            $precioUnitario = 0;
            if ($presentaciones->count() > 0) {
                $presentacionMasBarata = $presentaciones->first(); // Ya está ordenado por precio_venta
                $precioUnitario = $presentacionMasBarata['precio_venta'] / $presentacionMasBarata['unidades'];
            }

            return response()->json([
                'producto_id' => $productoId,
                'stock_disponible' => $stockDisponible,
                'precio_unitario' => $precioUnitario,
                'presentaciones' => $presentaciones
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener stock: ' . $e->getMessage()], 500);
        }
    }
}
