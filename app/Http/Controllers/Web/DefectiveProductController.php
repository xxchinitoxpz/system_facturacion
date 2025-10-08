<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DefectiveProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefectiveProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-productos-defectuosos')) {
            abort(403, 'No tienes permisos para ver productos defectuosos.');
        }

        try {
            $search = $request->get('search');
            $estado = $request->get('estado');

            $query = DB::table('defective_products')
                ->join('products', 'defective_products.producto_id', '=', 'products.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->select([
                    'defective_products.*',
                    'products.nombre as producto_nombre',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre'
                ]);

            // Filtros de búsqueda
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('products.nombre', 'like', "%{$search}%")
                      ->orWhere('products.barcode', 'like', "%{$search}%")
                      ->orWhere('defective_products.observaciones', 'like', "%{$search}%");
                });
            }



            if ($estado) {
                $query->where('defective_products.estado', $estado);
            }

            $defectiveProducts = $query->orderBy('defective_products.created_at', 'desc')
                                     ->paginate(10);

            return view('web.defective-products.index', compact('defectiveProducts', 'search', 'estado'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los productos defectuosos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-productos-defectuosos')) {
            abort(403, 'No tienes permisos para crear productos defectuosos.');
        }

        try {
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();
            return view('web.defective-products.create', compact('products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crear-productos-defectuosos')) {
            abort(403, 'No tienes permisos para crear productos defectuosos.');
        }

        $request->validate([
            'producto_id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|in:cambiado,almacenado,deshechado',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $defectiveProduct = DefectiveProduct::create([
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
                'fecha_registro' => now()->toDateString(),
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('defective-products.index')->with('success', 'Producto defectuoso registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar el producto defectuoso: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!auth()->user()->can('ver-productos-defectuosos')) {
            abort(403, 'No tienes permisos para ver productos defectuosos.');
        }

        try {
            $defectiveProduct = DB::table('defective_products')
                ->join('products', 'defective_products.producto_id', '=', 'products.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->select([
                    'defective_products.*',
                    'products.nombre as producto_nombre',
                    'products.descripcion as producto_descripcion',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre'
                ])
                ->where('defective_products.id', $id)
                ->first();

            if (!$defectiveProduct) {
                abort(404);
            }

            return view('web.defective-products.show', compact('defectiveProduct'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los detalles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->can('editar-productos-defectuosos')) {
            abort(403, 'No tienes permisos para editar productos defectuosos.');
        }

        try {
            $defectiveProduct = DefectiveProduct::findOrFail($id);
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();

            return view('web.defective-products.edit', compact('defectiveProduct', 'products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->can('editar-productos-defectuosos')) {
            abort(403, 'No tienes permisos para editar productos defectuosos.');
        }

        $request->validate([
            'producto_id' => 'required|exists:products,id',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|in:cambiado,almacenado,deshechado',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $defectiveProduct = DefectiveProduct::findOrFail($id);
            $defectiveProduct->update([
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('defective-products.index')->with('success', 'Producto defectuoso actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el producto defectuoso: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->can('eliminar-productos-defectuosos')) {
            abort(403, 'No tienes permisos para eliminar productos defectuosos.');
        }

        try {
            DB::beginTransaction();

            $defectiveProduct = DefectiveProduct::findOrFail($id);
            $defectiveProduct->delete();

            DB::commit();

            return redirect()->route('defective-products.index')->with('success', 'Producto defectuoso eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el producto defectuoso: ' . $e->getMessage());
        }
    }
}
