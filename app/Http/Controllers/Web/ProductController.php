<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Presentation;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('ver-productos')) {
            abort(403, 'No tienes permisos para ver productos.');
        }

        try {
            $search = request('search');
            $categoria_id = request('categoria_id');
            $marca_id = request('marca_id');
            
            $products = Product::query()
                ->with(['category', 'brand', 'presentations'])
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                          ->orWhere('descripcion', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                    });
                })
                ->when($categoria_id, function ($query, $categoria_id) {
                    $query->where('categoria_id', $categoria_id);
                })
                ->when($marca_id, function ($query, $marca_id) {
                    $query->where('marca_id', $marca_id);
                })
                ->orderBy('nombre')
                ->paginate(10);

            $categories = Category::orderBy('nombre')->get();
            $brands = Brand::orderBy('nombre')->get();

            return view('web.products.index', compact('products', 'search', 'categories', 'brands', 'categoria_id', 'marca_id'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los productos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-productos')) {
            abort(403, 'No tienes permisos para crear productos.');
        }

        try {
            $categories = Category::orderBy('nombre')->get();
            $brands = Brand::orderBy('nombre')->get();

            return view('web.products.create', compact('categories', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crear-productos')) {
            abort(403, 'No tienes permisos para crear productos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'barcode' => 'required|string|max:255|unique:products,barcode',
            'categoria_id' => 'required|exists:categories,id',
            'marca_id' => 'required|exists:brands,id',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.nombre' => 'required|string|max:255',
            'presentaciones.*.precio_venta' => 'required|numeric|min:0',
            'presentaciones.*.unidades' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'descripcion.required' => 'La descripción del producto es obligatoria.',
            'barcode.required' => 'El código de barras es obligatorio.',
            'barcode.unique' => 'Ya existe un producto con ese código de barras.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'marca_id.required' => 'Debe seleccionar una marca.',
            'marca_id.exists' => 'La marca seleccionada no existe.',
            'presentaciones.required' => 'Debe agregar al menos una presentación.',
            'presentaciones.min' => 'Debe agregar al menos una presentación.',
            'presentaciones.*.nombre.required' => 'El nombre de la presentación es obligatorio.',
            'presentaciones.*.precio_venta.required' => 'El precio de venta es obligatorio.',
            'presentaciones.*.precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'presentaciones.*.precio_venta.min' => 'El precio de venta debe ser mayor a 0.',
            'presentaciones.*.unidades.required' => 'El número de unidades es obligatorio.',
            'presentaciones.*.unidades.integer' => 'El número de unidades debe ser un número entero.',
            'presentaciones.*.unidades.min' => 'El número de unidades debe ser mayor a 0.',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'barcode' => $request->barcode,
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
            ]);

            // Crear las presentaciones
            foreach ($request->presentaciones as $presentacion) {
                $product->presentations()->create([
                    'nombre' => $presentacion['nombre'],
                    'precio_venta' => $presentacion['precio_venta'],
                    'unidades' => $presentacion['unidades'],
                ]);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Producto creado exitosamente con sus presentaciones.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (!auth()->user()->can('ver-productos')) {
            abort(403, 'No tienes permisos para ver productos.');
        }

        try {
            $product->load(['category', 'brand', 'presentations']);
            return view('web.products.show', compact('product'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        if (!auth()->user()->can('editar-productos')) {
            abort(403, 'No tienes permisos para editar productos.');
        }

        try {
            $product->load('presentations');
            $categories = Category::orderBy('nombre')->get();
            $brands = Brand::orderBy('nombre')->get();

            return view('web.products.edit', compact('product', 'categories', 'brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if (!auth()->user()->can('editar-productos')) {
            abort(403, 'No tienes permisos para editar productos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'barcode' => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'categoria_id' => 'required|exists:categories,id',
            'marca_id' => 'required|exists:brands,id',
            'presentaciones' => 'required|array|min:1',
            'presentaciones.*.nombre' => 'required|string|max:255',
            'presentaciones.*.precio_venta' => 'required|numeric|min:0',
            'presentaciones.*.unidades' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'descripcion.required' => 'La descripción del producto es obligatoria.',
            'barcode.required' => 'El código de barras es obligatorio.',
            'barcode.unique' => 'Ya existe un producto con ese código de barras.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'marca_id.required' => 'Debe seleccionar una marca.',
            'marca_id.exists' => 'La marca seleccionada no existe.',
            'presentaciones.required' => 'Debe agregar al menos una presentación.',
            'presentaciones.min' => 'Debe agregar al menos una presentación.',
            'presentaciones.*.nombre.required' => 'El nombre de la presentación es obligatorio.',
            'presentaciones.*.precio_venta.required' => 'El precio de venta es obligatorio.',
            'presentaciones.*.precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'presentaciones.*.precio_venta.min' => 'El precio de venta debe ser mayor a 0.',
            'presentaciones.*.unidades.required' => 'El número de unidades es obligatorio.',
            'presentaciones.*.unidades.integer' => 'El número de unidades debe ser un número entero.',
            'presentaciones.*.unidades.min' => 'El número de unidades debe ser mayor a 0.',
        ]);

        try {
            DB::beginTransaction();

            $product->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'barcode' => $request->barcode,
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
            ]);

            // 🎯 NUEVA LÓGICA: Manejar presentaciones existentes, nuevas y eliminadas
            foreach ($request->presentaciones as $presentacion) {
                // Si tiene ID, es una presentación existente
                if (isset($presentacion['id']) && !empty($presentacion['id'])) {
                    // Verificar si está marcada para eliminar
                    if (isset($presentacion['_delete']) && $presentacion['_delete'] == '1') {
                        // Eliminar la presentación
                        Presentation::where('id', $presentacion['id'])->delete();
                    } else {
                        // Actualizar la presentación existente
                        Presentation::where('id', $presentacion['id'])->update([
                            'nombre' => $presentacion['nombre'],
                            'precio_venta' => $presentacion['precio_venta'],
                            'unidades' => $presentacion['unidades'],
                        ]);
                    }
                } else {
                    // Es una presentación nueva, crearla
                    $product->presentations()->create([
                        'nombre' => $presentacion['nombre'],
                        'precio_venta' => $presentacion['precio_venta'],
                        'unidades' => $presentacion['unidades'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!auth()->user()->can('eliminar-productos')) {
            abort(403, 'No tienes permisos para eliminar productos.');
        }

        try {
            DB::beginTransaction();

            // Eliminar las presentaciones primero
            $product->presentations()->delete();
            
            // Eliminar el producto
            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Buscar productos por código de barras (para AJAX)
     */
    public function searchByBarcode(Request $request)
    {
        $barcode = $request->get('barcode');
        
        $product = Product::with(['category', 'brand', 'presentations'])
            ->where('barcode', $barcode)
            ->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ]);
    }
}
