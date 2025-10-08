<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class Sale2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-ventas')) {
            abort(403, 'No tienes permisos para ver ventas.');
        }

        try {
            $user = auth()->user();
            
            // Obtener parámetro de búsqueda
            $search = $request->get('search');
            
            // Construir query base
            $query = Sale::with(['client', 'branch', 'user', 'products', 'payments'])
                ->orderBy('fecha_venta', 'desc');

            // Si el usuario tiene sucursal asignada, filtrar por esa sucursal
            if ($user->sucursal_id !== null) {
                $query->where('sucursal_id', $user->sucursal_id);
            }

            // Aplicar búsqueda si existe
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('tipo_comprobante', 'like', "%{$search}%")
                      ->orWhere('serie', 'like', "%{$search}%")
                      ->orWhere('correlativo', 'like', "%{$search}%")
                      ->orWhereHas('client', function($clientQuery) use ($search) {
                          $clientQuery->where('nombre_completo', 'like', "%{$search}%")
                                     ->orWhere('nro_documento', 'like', "%{$search}%");
                      });
                });
            }

            // Obtener ventas paginadas
            $sales = $query->paginate(10);

            return view('web.sales2.index', compact('sales', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar las ventas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        if (!auth()->user()->can('ver-ventas')) {
            abort(403, 'No tienes permisos para ver ventas.');
        }

        // Validar que el empleado solo pueda ver ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para ver ventas de esta sucursal.');
        }

        try {
            // Cargar las relaciones necesarias
            $sale->load(['client', 'branch', 'user', 'products', 'payments']);
            
            return view('web.sales2.show', compact('sale'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-ventas')) {
            abort(403, 'No tienes permisos para crear ventas.');
        }

        try {
                // Cargar productos con su inventario, presentaciones y categoría
                $products = \App\Models\Product::with([
                    'warehouses' => function($query) {
                        $query->where('stock', '>', 0);
                    },
                    'presentations',
                    'category'
                ])
                ->whereHas('warehouses', function($query) {
                    $query->where('stock', '>', 0);
                })
                ->get();

            // Cargar todas las categorías para el carrusel
            $categories = \App\Models\Category::orderBy('nombre')->get();

            return view('web.sales2.create', compact('products', 'categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los productos: ' . $e->getMessage());
        }
    }
}
        