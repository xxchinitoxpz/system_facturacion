<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Imports\InventoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-inventario')) {
            abort(403, 'No tienes permisos para ver inventario.');
        }

        try {
            $search = $request->get('search', '');
            $almacen_id = $request->get('almacen_id', '');
            $mostrar_stock_cero = $request->get('mostrar_stock_cero', false);

            $query = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->select([
                    'product_warehouse.id',
                    'product_warehouse.stock',
                    'product_warehouse.fecha_vencimiento',
                    'products.nombre as producto_nombre',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre',
                    'warehouses.nombre as almacen_nombre',
                    'product_warehouse.created_at',
                    'product_warehouse.updated_at'
                ]);

            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('products.nombre', 'like', "%{$search}%")
                      ->orWhere('products.barcode', 'like', "%{$search}%")
                      ->orWhere('warehouses.nombre', 'like', "%{$search}%");
                });
            }

            if ($almacen_id) {
                $query->where('product_warehouse.almacen_id', $almacen_id);
            }

            // Filtrar productos con stock 0 (por defecto no se muestran)
            if (!$mostrar_stock_cero) {
                $query->where('product_warehouse.stock', '>', 0);
            }

            $inventory = $query->orderBy('product_warehouse.updated_at', 'desc')
                              ->paginate(15);

            $products = Product::orderBy('nombre')->get();
            $warehouses = Warehouse::orderBy('nombre')->get();

            return view('web.inventory.index', compact('inventory', 'products', 'warehouses', 'search', 'almacen_id', 'mostrar_stock_cero'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el inventario: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('crear-inventario')) {
            abort(403, 'No tienes permisos para crear inventario.');
        }

        try {
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();
            $warehouses = Warehouse::orderBy('nombre')->get();

            return view('web.inventory.create', compact('products', 'warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crear-inventario')) {
            abort(403, 'No tienes permisos para crear inventario.');
        }

        $request->validate([
            'almacen_id' => 'required|exists:warehouses,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:products,id',
            'productos.*.stock' => 'required|integer|min:0',
            'productos.*.fecha_vencimiento' => 'nullable|date|after:today',
        ], [
            'almacen_id.required' => 'El almacén es obligatorio.',
            'almacen_id.exists' => 'El almacén seleccionado no existe.',
            'productos.required' => 'Debe agregar al menos un producto.',
            'productos.min' => 'Debe agregar al menos un producto.',
            'productos.*.producto_id.required' => 'El producto es obligatorio.',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.stock.required' => 'El stock es obligatorio.',
            'productos.*.stock.integer' => 'El stock debe ser un número entero.',
            'productos.*.stock.min' => 'El stock no puede ser negativo.',
            'productos.*.fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'productos.*.fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a hoy.',
        ]);

        try {
            DB::beginTransaction();

            $almacen_id = $request->almacen_id;
            $productos = $request->productos;
            $creados = 0;
            $actualizados = 0;
            $errores = [];

            foreach ($productos as $index => $producto) {
                if (empty($producto['producto_id']) || empty($producto['stock'])) {
                    continue; // Saltar productos vacíos
                }

                $producto_id = $producto['producto_id'];
                $stock = $producto['stock'];
                $fecha_vencimiento = !empty($producto['fecha_vencimiento']) ? $producto['fecha_vencimiento'] : null;

                // Buscar si ya existe un registro para este producto en este almacén con la misma fecha de vencimiento
                $existingRecord = DB::table('product_warehouse')
                    ->where('producto_id', $producto_id)
                    ->where('almacen_id', $almacen_id)
                    ->where('fecha_vencimiento', $fecha_vencimiento)
                    ->first();

                if ($existingRecord) {
                    // Actualizar el stock sumando al existente
                    $nuevoStock = $existingRecord->stock + $stock;
                    DB::table('product_warehouse')
                        ->where('id', $existingRecord->id)
                        ->update([
                            'stock' => $nuevoStock,
                            'updated_at' => now(),
                        ]);
                    $actualizados++;
                } else {
                    // Crear nuevo registro
                    DB::table('product_warehouse')->insert([
                        'producto_id' => $producto_id,
                        'almacen_id' => $almacen_id,
                        'stock' => $stock,
                        'fecha_vencimiento' => $fecha_vencimiento,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $creados++;
                }
            }

            DB::commit();

            $mensaje = '';
            if ($creados > 0) {
                $mensaje .= "Se crearon {$creados} lotes nuevos. ";
            }
            if ($actualizados > 0) {
                $mensaje .= "Se actualizaron {$actualizados} lotes existentes. ";
            }

            return redirect()->route('inventory.index')->with('success', trim($mensaje));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar el inventario: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!auth()->user()->can('ver-inventario')) {
            abort(403, 'No tienes permisos para ver inventario.');
        }

        try {
            $inventory = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->select([
                    'product_warehouse.*',
                    'products.nombre as producto_nombre',
                    'products.descripcion as producto_descripcion',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre',
                    'warehouses.nombre as almacen_nombre',
                    'warehouses.descripcion as almacen_descripcion'
                ])
                ->where('product_warehouse.id', $id)
                ->first();

            if (!$inventory) {
                abort(404);
            }

            return view('web.inventory.show', compact('inventory'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los detalles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->can('editar-inventario')) {
            abort(403, 'No tienes permisos para editar inventario.');
        }

        try {
            $inventory = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->select([
                    'product_warehouse.*',
                    'products.nombre as producto_nombre',
                    'warehouses.nombre as almacen_nombre'
                ])
                ->where('product_warehouse.id', $id)
                ->first();

            if (!$inventory) {
                abort(404);
            }

            $products = Product::orderBy('nombre')->get();
            $warehouses = Warehouse::orderBy('nombre')->get();

            return view('web.inventory.edit', compact('inventory', 'products', 'warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->can('editar-inventario')) {
            abort(403, 'No tienes permisos para editar inventario.');
        }

        $request->validate([
            'stock' => 'required|integer|min:0',
            'fecha_vencimiento' => 'nullable|date|after:today',
        ], [
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a hoy.',
        ]);

        // Obtener el registro actual
        $currentRecord = DB::table('product_warehouse')->where('id', $id)->first();
        if (!$currentRecord) {
            return back()->withErrors(['error' => 'Registro de inventario no encontrado.'])->withInput();
        }

        // Verificar que no exista otro registro para este producto en este almacén con la misma fecha de vencimiento
        // Solo si se está cambiando la fecha de vencimiento
        if ($request->fecha_vencimiento != $currentRecord->fecha_vencimiento) {
            $existingRecord = DB::table('product_warehouse')
                ->where('producto_id', $currentRecord->producto_id)
                ->where('almacen_id', $currentRecord->almacen_id)
                ->where('fecha_vencimiento', $request->fecha_vencimiento)
                ->where('id', '!=', $id)
                ->first();

            if ($existingRecord) {
                return back()->withErrors(['fecha_vencimiento' => 'Ya existe un lote de inventario para este producto en el almacén seleccionado con la misma fecha de vencimiento.'])->withInput();
            }
        }

        try {
            DB::table('product_warehouse')
                ->where('id', $id)
                ->update([
                    'stock' => $request->stock,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'updated_at' => now(),
                ]);

            return redirect()->route('inventory.index')->with('success', 'Lote de inventario actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el lote de inventario.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->can('eliminar-inventario')) {
            abort(403, 'No tienes permisos para eliminar inventario.');
        }

        try {
            DB::table('product_warehouse')->where('id', $id)->delete();
            return redirect()->route('inventory.index')->with('success', 'Lote de inventario eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el lote de inventario.']);
        }
    }

    /**
     * Show the form for importing inventory from Excel.
     */
    public function importForm()
    {
        if (!auth()->user()->can('crear-inventario')) {
            abort(403, 'No tienes permisos para crear inventario.');
        }

        try {
            $warehouses = Warehouse::orderBy('nombre')->get();
            return view('web.inventory.import', compact('warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario de importación: ' . $e->getMessage());
        }
    }

    /**
     * Import inventory from Excel file.
     */
    public function import(Request $request)
    {
        if (!auth()->user()->can('crear-inventario')) {
            abort(403, 'No tienes permisos para crear inventario.');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Máximo 10MB
            'warehouse_id' => 'required|exists:warehouses,id',
        ], [
            'excel_file.required' => 'El archivo Excel es obligatorio.',
            'excel_file.file' => 'El archivo debe ser válido.',
            'excel_file.mimes' => 'El archivo debe ser un Excel (.xlsx, .xls) o CSV.',
            'excel_file.max' => 'El archivo no puede ser mayor a 10MB.',
            'warehouse_id.required' => 'El almacén es obligatorio.',
            'warehouse_id.exists' => 'El almacén seleccionado no existe.',
        ]);

        try {
            $import = new InventoryImport($request->warehouse_id);
            
            Excel::import($import, $request->file('excel_file'));
            
            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();
            
            if (empty($errors)) {
                return redirect()->route('inventory.index')
                    ->with('success', "Importación exitosa. Se procesaron {$successCount} registros correctamente.");
            } else {
                $errorMessage = "Importación completada con errores. Se procesaron {$successCount} registros correctamente. Errores encontrados: " . count($errors);
                return redirect()->route('inventory.index')
                    ->with('warning', $errorMessage)
                    ->with('import_errors', $errors);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template for inventory import.
     */
    public function downloadTemplate()
    {
        if (!auth()->user()->can('crear-inventario')) {
            abort(403, 'No tienes permisos para crear inventario.');
        }

        try {
            $data = [
                ['codigo_barras', 'stock', 'fecha_vencimiento'],
                ['7759109000758', '10', '20/09/2026'],
                ['1234567890123', '5', '15/12/2025'],
                ['9876543210987', '25', ''],
            ];

            return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function array(): array {
                    return $this->data;
                }
            }, 'plantilla_inventario.xlsx');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF del inventario
     */
    public function pdf(Request $request)
    {

        if (!auth()->user()->can('ver-inventario')) {
            abort(403, 'No tienes permisos para ver inventario.');
        }

        try {
            $search = $request->get('search', '');
            $almacen_id = $request->get('almacen_id', '');
            $mostrar_stock_cero = $request->get('mostrar_stock_cero', false);

            $query = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->join('branches', 'warehouses.sucursal_id', '=', 'branches.id')
                ->select([
                    'product_warehouse.id',
                    'product_warehouse.stock',
                    'product_warehouse.fecha_vencimiento',
                    'products.nombre as producto_nombre',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre',
                    'warehouses.nombre as almacen_nombre',
                    'branches.nombre as sucursal_nombre',
                    'product_warehouse.created_at',
                    'product_warehouse.updated_at'
                ]);

            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('products.nombre', 'like', "%{$search}%")
                      ->orWhere('products.barcode', 'like', "%{$search}%")
                      ->orWhere('warehouses.nombre', 'like', "%{$search}%");
                });
            }

            if ($almacen_id) {
                $query->where('product_warehouse.almacen_id', $almacen_id);
            }

            // Filtrar productos con stock 0 (por defecto no se muestran)
            if (!$mostrar_stock_cero) {
                $query->where('product_warehouse.stock', '>', 0);
            }

            $inventory = $query->orderBy('product_warehouse.updated_at', 'desc')->get();

            // Obtener información de filtros aplicados
            $filtros = [
                'search' => $search,
                'almacen_id' => $almacen_id,
                'mostrar_stock_cero' => $mostrar_stock_cero
            ];

            // Obtener nombre del almacén si se filtró por uno específico
            $almacen_nombre = null;
            if ($almacen_id) {
                $almacen = Warehouse::find($almacen_id);
                $almacen_nombre = $almacen ? $almacen->nombre : 'Almacén no encontrado';
            }

            // Calcular estadísticas
            $total_productos = $inventory->count();
            $total_stock = $inventory->sum('stock');
            $productos_vencidos = $inventory->filter(function($item) {
                if (!$item->fecha_vencimiento) return false;
                return \Carbon\Carbon::parse($item->fecha_vencimiento)->isPast();
            })->count();
            $productos_por_vencer = $inventory->filter(function($item) {
                if (!$item->fecha_vencimiento) return false;
                $fecha = \Carbon\Carbon::parse($item->fecha_vencimiento);
                $diasRestantes = now()->diffInDays($fecha, false);
                return $diasRestantes > 0 && $diasRestantes <= 30;
            })->count();

            $pdf = Pdf::loadView('web.inventory.pdf', compact(
                'inventory', 
                'filtros', 
                'almacen_nombre',
                'total_productos',
                'total_stock',
                'productos_vencidos',
                'productos_por_vencer'
            ));
            
            $filename = 'Inventario_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF del inventario agrupado por producto
     */
    public function stockReport(Request $request)
    {
        if (!auth()->user()->can('ver-inventario')) {
            abort(403, 'No tienes permisos para ver inventario.');
        }

        try {
            $search = $request->get('search', '');
            $almacen_id = $request->get('almacen_id', '');
            $mostrar_stock_cero = $request->get('mostrar_stock_cero', false);

            $query = DB::table('product_warehouse')
                ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->join('categories', 'products.categoria_id', '=', 'categories.id')
                ->join('brands', 'products.marca_id', '=', 'brands.id')
                ->join('branches', 'warehouses.sucursal_id', '=', 'branches.id')
                ->select([
                    'products.id as producto_id',
                    'products.nombre as producto_nombre',
                    'products.barcode',
                    'categories.nombre as categoria_nombre',
                    'brands.nombre as marca_nombre',
                    'warehouses.nombre as almacen_nombre',
                    'branches.nombre as sucursal_nombre',
                    DB::raw('SUM(product_warehouse.stock) as stock_total')
                ])
                ->groupBy('products.id', 'products.nombre', 'products.barcode', 'categories.nombre', 'brands.nombre', 'warehouses.nombre', 'branches.nombre');

            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('products.nombre', 'like', "%{$search}%")
                      ->orWhere('products.barcode', 'like', "%{$search}%")
                      ->orWhere('warehouses.nombre', 'like', "%{$search}%");
                });
            }

            if ($almacen_id) {
                $query->where('product_warehouse.almacen_id', $almacen_id);
            }

            // Filtrar productos con stock 0 (por defecto no se muestran)
            if (!$mostrar_stock_cero) {
                $query->having('stock_total', '>', 0);
            }

            $inventory = $query->orderBy('products.nombre', 'asc')->get();

            // Obtener información de filtros aplicados
            $filtros = [
                'search' => $search,
                'almacen_id' => $almacen_id,
                'mostrar_stock_cero' => $mostrar_stock_cero
            ];

            // Obtener nombre del almacén si se filtró por uno específico
            $almacen_nombre = null;
            if ($almacen_id) {
                $almacen = Warehouse::find($almacen_id);
                $almacen_nombre = $almacen ? $almacen->nombre : 'Almacén no encontrado';
            }

            // Calcular estadísticas
            $total_productos = $inventory->count();
            $total_stock = $inventory->sum('stock_total');

            $pdf = Pdf::loadView('web.inventory.stock-report', compact(
                'inventory', 
                'filtros', 
                'almacen_nombre',
                'total_productos',
                'total_stock'
            ));
            
            $filename = 'Reporte_Stock_Agrupado_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el reporte de stock: ' . $e->getMessage());
        }
    }
}
