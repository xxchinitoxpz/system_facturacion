<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('ver-combos')) {
            abort(403, 'No tienes permisos para ver combos.');
        }

        try {
            $search = request('search');
            
            $combos = Combo::query()
                ->with(['products'])
                ->when($search, function ($query, $search) {
                    $query->where('nombre', 'like', "%{$search}%");
                })
                ->orderBy('nombre')
                ->paginate(10);

            return view('web.combos.index', compact('combos', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los combos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-combos')) {
            abort(403, 'No tienes permisos para crear combos.');
        }

        try {
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();

            return view('web.combos.create', compact('products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crear-combos')) {
            abort(403, 'No tienes permisos para crear combos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'estado' => 'boolean',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre del combo es obligatorio.',
            'precio.required' => 'El precio del combo es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'productos.required' => 'Debe agregar al menos un producto al combo.',
            'productos.min' => 'Debe agregar al menos un producto al combo.',
            'productos.*.producto_id.required' => 'El producto es obligatorio.',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ]);

        try {
            DB::beginTransaction();

            $combo = Combo::create([
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'estado' => $request->has('estado'),
            ]);

            // Agregar productos al combo
            foreach ($request->productos as $producto) {
                if (!empty($producto['producto_id']) && !empty($producto['cantidad'])) {
                    $combo->products()->attach($producto['producto_id'], [
                        'cantidad' => $producto['cantidad']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('combos.index')->with('success', 'Combo creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el combo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Combo $combo)
    {
        if (!auth()->user()->can('ver-combos')) {
            abort(403, 'No tienes permisos para ver combos.');
        }

        try {
            $combo->load(['products.category', 'products.brand']);

            return view('web.combos.show', compact('combo'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los detalles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Combo $combo)
    {
        if (!auth()->user()->can('editar-combos')) {
            abort(403, 'No tienes permisos para editar combos.');
        }

        try {
            $combo->load(['products']);
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();

            return view('web.combos.edit', compact('combo', 'products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Combo $combo)
    {
        if (!auth()->user()->can('editar-combos')) {
            abort(403, 'No tienes permisos para editar combos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'estado' => 'boolean',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre del combo es obligatorio.',
            'precio.required' => 'El precio del combo es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'productos.required' => 'Debe agregar al menos un producto al combo.',
            'productos.min' => 'Debe agregar al menos un producto al combo.',
            'productos.*.producto_id.required' => 'El producto es obligatorio.',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ]);

        try {
            DB::beginTransaction();

            $combo->update([
                'nombre' => $request->nombre,
                'precio' => $request->precio,
                'estado' => $request->has('estado'),
            ]);

            // Sincronizar productos del combo
            $productosData = [];
            foreach ($request->productos as $producto) {
                if (!empty($producto['producto_id']) && !empty($producto['cantidad'])) {
                    $productosData[$producto['producto_id']] = ['cantidad' => $producto['cantidad']];
                }
            }
            $combo->products()->sync($productosData);

            DB::commit();

            return redirect()->route('combos.index')->with('success', 'Combo actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el combo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Combo $combo)
    {
        if (!auth()->user()->can('eliminar-combos')) {
            abort(403, 'No tienes permisos para eliminar combos.');
        }

        try {
            DB::beginTransaction();

            // Eliminar relaciones con productos
            $combo->products()->detach();
            
            // Eliminar el combo
            $combo->delete();

            DB::commit();

            return redirect()->route('combos.index')->with('success', 'Combo eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el combo: ' . $e->getMessage()]);
        }
    }
}
