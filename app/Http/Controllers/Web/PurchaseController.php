<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('ver-compras')) {
            abort(403, 'No tienes permisos para ver compras.');
        }

        try {
            $user = auth()->user();
            $search = request('search');
            $proveedor_id = request('proveedor_id');
            $sucursal_id = request('sucursal_id');
            $fecha_inicio = request('fecha_inicio');
            $fecha_fin = request('fecha_fin');
            
            $purchases = Purchase::query()
                ->with(['supplier', 'branch', 'user'])
                // Si el usuario es empleado, filtrar solo compras de su sucursal
                ->when($user->sucursal_id !== null, function ($query) use ($user) {
                    $query->where('sucursal_id', $user->sucursal_id);
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('observaciones', 'like', "%{$search}%")
                          ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                              $supplierQuery->where('nombre_completo', 'like', "%{$search}%");
                          });
                    });
                })
                ->when($proveedor_id, function ($query, $proveedor_id) {
                    $query->where('proveedor_id', $proveedor_id);
                })
                ->when($sucursal_id, function ($query, $sucursal_id) {
                    $query->where('sucursal_id', $sucursal_id);
                })
                ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('fecha_compra', [$fecha_inicio, $fecha_fin]);
                })
                ->orderBy('fecha_compra', 'desc')
                ->paginate(10);

            $suppliers = Supplier::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();

            return view('web.purchases.index', compact('purchases', 'search', 'suppliers', 'branches', 'proveedor_id', 'sucursal_id', 'fecha_inicio', 'fecha_fin'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar las compras: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-compras')) {
            abort(403, 'No tienes permisos para crear compras.');
        }

        try {
            $user = auth()->user();
            
            $suppliers = Supplier::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();
            $products = Product::with(['category', 'brand', 'presentations'])->orderBy('nombre')->get();

            return view('web.purchases.create', compact('suppliers', 'branches', 'products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crear-compras')) {
            abort(403, 'No tienes permisos para crear compras.');
        }

        $request->validate([
            'total' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
            'proveedor_id' => 'required',
            'proveedor_documento' => 'required|string',
            'proveedor_nombre' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.tipo' => 'required|in:producto,presentacion',
            'productos.*.item_id' => 'required|integer',
            'productos.*.nombre' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'productos.*.fecha_vencimiento' => 'nullable|date',
            'comprobante_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser mayor o igual a 0.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_documento.required' => 'Debe ingresar el documento del proveedor.',
            'proveedor_nombre.required' => 'Debe ingresar el nombre del proveedor.',
            'productos.required' => 'Debe agregar al menos un producto a la compra.',
            'productos.min' => 'Debe agregar al menos un producto a la compra.',
            'comprobante_path.file' => 'El comprobante debe ser un archivo.',
            'comprobante_path.mimes' => 'El comprobante debe ser un archivo PDF, JPG, JPEG o PNG.',
            'comprobante_path.max' => 'El comprobante no debe superar los 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // Validar que el empleado solo pueda crear compras en su sucursal asignada
            $user = auth()->user();
            if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para crear compras en esta sucursal. Solo puedes crear compras en tu sucursal asignada.');
            }

            // Manejar proveedor (crear si es necesario)
            $proveedorId = $request->proveedor_id;
            if (str_starts_with($proveedorId, 'temp_')) {
                // Es un proveedor temporal, crear uno nuevo
                $documento = str_replace('temp_', '', $proveedorId);
                $tipoDocumento = strlen($documento) === 8 ? 'DNI' : 'RUC';
                
                $proveedor = Supplier::create([
                    'nombre_completo' => $request->proveedor_nombre,
                    'tipo_documento' => $tipoDocumento,
                    'nro_documento' => $documento,
                    'telefono' => null,
                    'email' => null,
                    'direccion' => null,
                    'activo' => true,
                ]);
                
                $proveedorId = $proveedor->id;
            }

            // Manejar la subida del comprobante
            $comprobantePath = null;
            if ($request->hasFile('comprobante_path')) {
                $comprobantePath = $request->file('comprobante_path')->store('comprobantes-compras', 'public');
            }

            $purchase = Purchase::create([
                'fecha_compra' => now(),
                'total' => $request->total,
                'observaciones' => $request->observaciones,
                'comprobante_path' => $comprobantePath,
                'sucursal_id' => $request->sucursal_id,
                'proveedor_id' => $proveedorId,
                'usuario_id' => auth()->id(),
            ]);

            // Agregar productos a la compra y aumentar stock
            foreach ($request->productos as $producto) {
                $purchaseProductData = [
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['subtotal'],
                    'compra_id' => $purchase->id,
                    'fecha_vencimiento' => $producto['fecha_vencimiento'] ?? null,
                ];

                if ($producto['tipo'] === 'producto') {
                    $purchaseProductData['producto_id'] = $producto['item_id'];
                    
                    // Aumentar stock del producto
                    $this->aumentarStockProducto($producto['item_id'], $request->sucursal_id, $producto['cantidad'], $producto['fecha_vencimiento'] ?? null);
                } else {
                    // Es una presentación
                    $presentacion = Presentation::find($producto['item_id']);
                    if ($presentacion) {
                        $purchaseProductData['producto_id'] = $presentacion->producto_id;
                        
                        // Aumentar stock del producto padre (multiplicar por unidades de la presentación)
                        $unidadesAAumentar = $producto['cantidad'] * $presentacion->unidades;
                        $this->aumentarStockProducto($presentacion->producto_id, $request->sucursal_id, $unidadesAAumentar, $producto['fecha_vencimiento'] ?? null);
                    }
                }

                DB::table('purchase_products')->insert($purchaseProductData);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Compra creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la compra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        if (!auth()->user()->can('ver-compras')) {
            abort(403, 'No tienes permisos para ver compras.');
        }

        // Validar que el empleado solo pueda ver compras de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para ver compras de esta sucursal.');
        }

        try {
            $purchase->load(['supplier', 'branch.company', 'user', 'products']);
            return view('web.purchases.show', compact('purchase'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        if (!auth()->user()->can('editar-compras')) {
            abort(403, 'No tienes permisos para editar compras.');
        }

        // Validar que el empleado solo pueda editar compras de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para editar compras de esta sucursal.');
        }

        try {
            $purchase->load(['supplier', 'branch.company', 'user', 'products']);
            $suppliers = Supplier::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();
            $products = Product::with(['category', 'brand', 'presentations'])->orderBy('nombre')->get();

            return view('web.purchases.edit', compact('purchase', 'suppliers', 'branches', 'products'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        if (!auth()->user()->can('editar-compras')) {
            abort(403, 'No tienes permisos para editar compras.');
        }

        $request->validate([
            'total' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
            'proveedor_id' => 'required|exists:suppliers,id',
            'comprobante_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser mayor o igual a 0.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'comprobante_path.file' => 'El comprobante debe ser un archivo.',
            'comprobante_path.mimes' => 'El comprobante debe ser un archivo PDF, JPG, JPEG o PNG.',
            'comprobante_path.max' => 'El comprobante no debe superar los 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // Validar que el empleado solo pueda editar compras de su sucursal
            $user = auth()->user();
            if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para editar compras de esta sucursal.');
            }

            // Validar que el empleado solo pueda cambiar a su sucursal asignada
            if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para cambiar la sucursal de la compra.');
            }

            // Manejar la subida del comprobante
            $comprobantePath = $purchase->comprobante_path;
            if ($request->hasFile('comprobante_path')) {
                // Eliminar el archivo anterior si existe
                if ($comprobantePath && Storage::disk('public')->exists($comprobantePath)) {
                    Storage::disk('public')->delete($comprobantePath);
                }
                $comprobantePath = $request->file('comprobante_path')->store('comprobantes-compras', 'public');
            }

            $purchase->update([
                'total' => $request->total,
                'observaciones' => $request->observaciones,
                'comprobante_path' => $comprobantePath,
                'sucursal_id' => $request->sucursal_id,
                'proveedor_id' => $request->proveedor_id,
            ]);

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        if (!auth()->user()->can('eliminar-compras')) {
            abort(403, 'No tienes permisos para eliminar compras.');
        }

        // Validar que el empleado solo pueda eliminar compras de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para eliminar compras de esta sucursal.');
        }

        try {
            DB::beginTransaction();

            // Restaurar stock original antes de eliminar
            $this->restaurarStockCompra($purchase);

            // Eliminar el comprobante si existe
            if ($purchase->comprobante_path && Storage::disk('public')->exists($purchase->comprobante_path)) {
                Storage::disk('public')->delete($purchase->comprobante_path);
            }

            // Eliminar las relaciones con productos
            $purchase->products()->detach();
            
            // Eliminar la compra
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Anular una compra
     */
    public function anular(Purchase $purchase)
    {
        if (!auth()->user()->can('anular-compras')) {
            abort(403, 'No tienes permisos para anular compras.');
        }

        // Validar que el empleado solo pueda anular compras de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para anular compras de esta sucursal.');
        }

        try {
            DB::beginTransaction();

            // Cargar las relaciones necesarias para restaurar el stock
            $purchase->load(['products']);

            // Restaurar el stock de todos los productos de la compra
            $this->restaurarStockCompra($purchase);

            // Cambiar el estado a anulada (asumiendo que agregamos un campo estado)
            // $purchase->update(['estado' => 'anulada']);

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra anulada exitosamente. El stock ha sido restaurado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular la compra: ' . $e->getMessage());
        }
    }

    /**
     * Descargar comprobante de compra
     */
    public function descargarComprobante(Purchase $purchase)
    {
        if (!auth()->user()->can('ver-compras')) {
            abort(403, 'No tienes permisos para ver compras.');
        }

        // Validar que el empleado solo pueda ver compras de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $purchase->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para ver compras de esta sucursal.');
        }

        if (!$purchase->comprobante_path) {
            abort(404, 'No hay comprobante disponible para esta compra.');
        }

        if (!Storage::disk('public')->exists($purchase->comprobante_path)) {
            abort(404, 'El archivo del comprobante no existe.');
        }

        return Storage::disk('public')->download($purchase->comprobante_path);
    }

    /**
     * Aumentar stock de un producto
     */
    private function aumentarStockProducto($productoId, $sucursalId, $cantidad, $fechaVencimiento = null)
    {
        // Obtener los almacenes de la sucursal
        $almacenes = \App\Models\Warehouse::where('sucursal_id', $sucursalId)->get();
        
        foreach ($almacenes as $almacen) {
            // Buscar si ya existe un lote con la misma fecha de vencimiento
            $loteExistente = DB::table('product_warehouse')
                ->where('producto_id', $productoId)
                ->where('almacen_id', $almacen->id)
                ->where('fecha_vencimiento', $fechaVencimiento)
                ->first();

            if ($loteExistente) {
                // Actualizar el lote existente
                DB::table('product_warehouse')
                    ->where('id', $loteExistente->id)
                    ->update([
                        'stock' => $loteExistente->stock + $cantidad,
                        'updated_at' => now()
                    ]);
            } else {
                // Crear un nuevo lote
                DB::table('product_warehouse')->insert([
                    'producto_id' => $productoId,
                    'almacen_id' => $almacen->id,
                    'stock' => $cantidad,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Restaurar stock de una compra
     */
    private function restaurarStockCompra($purchase)
    {
        foreach ($purchase->products as $product) {
            $cantidadOriginal = $product->pivot->cantidad;
            $productoId = $product->id;
            $fechaVencimiento = $product->pivot->fecha_vencimiento;
            
            // Obtener los almacenes de la sucursal
            $almacenes = \App\Models\Warehouse::where('sucursal_id', $purchase->sucursal_id)->get();
            
            foreach ($almacenes as $almacen) {
                // Buscar el lote con la misma fecha de vencimiento
                $lote = DB::table('product_warehouse')
                    ->where('producto_id', $productoId)
                    ->where('almacen_id', $almacen->id)
                    ->where('fecha_vencimiento', $fechaVencimiento)
                    ->first();

                if ($lote) {
                    // Restar del stock existente
                    $nuevoStock = max(0, $lote->stock - $cantidadOriginal);
                    DB::table('product_warehouse')
                        ->where('id', $lote->id)
                        ->update([
                            'stock' => $nuevoStock,
                            'updated_at' => now()
                        ]);
                }
            }
        }
    }
}
