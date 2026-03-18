<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Client;
use App\Models\Branch;
use App\Models\CashBox;
use App\Models\CashBoxMovement;
use App\Models\Product;
use App\Models\Combo;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http; // Added for HTTP requests
use Illuminate\Support\Facades\Storage; // Added for file storage
use App\Models\DocumentSeries;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('ver-ventas')) {
            abort(403, 'No tienes permisos para ver ventas.');
        }

        try {
            $user = auth()->user();
            $search = request('search');
            $estado = request('estado');
            $cliente_id = request('cliente_id');
            $sucursal_id = request('sucursal_id');
            $fecha_inicio = request('fecha_inicio');
            $fecha_fin = request('fecha_fin');
            
            $sales = Sale::query()
                ->with(['client', 'branch', 'user'])
                // Si el usuario es empleado, filtrar solo ventas de su sucursal
                ->when($user->sucursal_id !== null, function ($query) use ($user) {
                    $query->where('sucursal_id', $user->sucursal_id);
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('serie', 'like', "%{$search}%")
                          ->orWhere('correlativo', 'like', "%{$search}%")
                          ->orWhere('tipo_comprobante', 'like', "%{$search}%")
                          ->orWhereHas('client', function ($clientQuery) use ($search) {
                              $clientQuery->where('nombre_completo', 'like', "%{$search}%");
                          });
                    });
                })
                ->when($estado, function ($query, $estado) {
                    $query->where('estado', $estado);
                })
                ->when($cliente_id, function ($query, $cliente_id) {
                    $query->where('cliente_id', $cliente_id);
                })
                ->when($sucursal_id, function ($query, $sucursal_id) {
                    $query->where('sucursal_id', $sucursal_id);
                })
                ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('fecha_venta', [$fecha_inicio, $fecha_fin]);
                })
                ->orderBy('fecha_venta', 'desc')
                ->paginate(10);

            $clients = Client::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();
            $estados = ['completada', 'anulada', 'pendiente'];

            return view('web.sales.index', compact('sales', 'search', 'clients', 'branches', 'estados', 'estado', 'cliente_id', 'sucursal_id', 'fecha_inicio', 'fecha_fin'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar las ventas: ' . $e->getMessage());
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
            $user = auth()->user();
            
            // Verificar que el usuario tenga una caja abierta
            $query = CashBox::with('activeSession');
            
            // Si el usuario es empleado, solo verificar cajas de su sucursal
            if ($user->sucursal_id !== null) {
                $query->where('sucursal_id', $user->sucursal_id);
            }
            
            $cashBoxes = $query->get();
            $openCashBox = $cashBoxes->first(function($cashBox) {
                return $cashBox->activeSession;
            });
            
            if (!$openCashBox) {
                return redirect()->route('cash-boxes.index')
                    ->with('error', 'Debes tener una caja abierta para crear ventas. Por favor, abre una caja primero.');
            }
            
            $clients = Client::where('activo', true)->orderBy('nombre_completo')->get();
            
            // Obtener solo las sucursales que tengan una caja abierta
            $branches = Branch::whereHas('cashBoxes.activeSession')->orderBy('nombre')->get();
            
            $products = Product::with(['category', 'brand'])->orderBy('nombre')->get();
            $combos = Combo::where('estado', true)->orderBy('nombre')->get();

            return view('web.sales.create', compact('clients', 'branches', 'openCashBox', 'products', 'combos'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log para depuración
        Log::info('Creando venta', [
            'request_data' => $request->all(),
            'dividir_pago_raw' => $request->input('dividir_pago'),
            'dividir_pago_parsed' => filter_var($request->input('dividir_pago'), FILTER_VALIDATE_BOOLEAN)
        ]);
        
        if (!auth()->user()->can('crear-ventas')) {
            abort(403, 'No tienes permisos para crear ventas.');
        }

        // Convertir dividir_pago a boolean antes de validar
        $dividirPago = filter_var($request->input('dividir_pago'), FILTER_VALIDATE_BOOLEAN);
        
        $request->validate([
            'total' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
            'cliente_id' => 'required|string',
            'tipo_comprobante' => 'required|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.tipo' => 'required|in:producto,combo',
            'productos.*.item_id' => 'required|integer',
            'productos.*.nombre' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser mayor o igual a 0.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'productos.required' => 'Debe agregar al menos un producto a la venta.',
            'productos.min' => 'Debe agregar al menos un producto a la venta.',
            'dividir_pago.required' => 'Debe especificar si desea dividir el pago.',
        ]);
        
        // Validaciones condicionales según el tipo de pago
        if ($dividirPago) {
            $request->validate([
                'tipo_pago_1' => 'required|integer|min:1|max:7',
                'monto_pago_1' => 'required|numeric|min:0.01',
                'tipo_pago_2' => 'required|integer|min:1|max:7',
                'monto_pago_2' => 'required|numeric|min:0.01',
            ], [
                'tipo_pago_1.required' => 'El tipo de pago 1 es obligatorio.',
                'tipo_pago_1.integer' => 'El tipo de pago 1 debe ser un número entero.',
                'tipo_pago_1.min' => 'El tipo de pago 1 debe ser mayor o igual a 1.',
                'tipo_pago_1.max' => 'El tipo de pago 1 debe ser menor o igual a 7.',
                'monto_pago_1.required' => 'El monto del pago 1 es obligatorio.',
                'monto_pago_1.numeric' => 'El monto del pago 1 debe ser un número.',
                'monto_pago_1.min' => 'El monto del pago 1 debe ser mayor a 0.',
                'tipo_pago_2.required' => 'El tipo de pago 2 es obligatorio.',
                'tipo_pago_2.integer' => 'El tipo de pago 2 debe ser un número entero.',
                'tipo_pago_2.min' => 'El tipo de pago 2 debe ser mayor o igual a 1.',
                'tipo_pago_2.max' => 'El tipo de pago 2 debe ser menor o igual a 7.',
                'monto_pago_2.required' => 'El monto del pago 2 es obligatorio.',
                'monto_pago_2.numeric' => 'El monto del pago 2 debe ser un número.',
                'monto_pago_2.min' => 'El monto del pago 2 debe ser mayor a 0.',
            ]);
            
            // Validar que la suma de los pagos sea igual al total
            $sumaPagos = $request->monto_pago_1 + $request->monto_pago_2;
            if (abs($sumaPagos - $request->total) > 0.01) {
                throw new \Exception("La suma de los pagos (S/{$sumaPagos}) debe ser igual al total de la venta (S/{$request->total})");
            }
            
            // Validar montos recibidos si son efectivo
            if ($request->tipo_pago_1 == 1) {
                $request->validate([
                    'monto_recibido_1' => 'required|numeric|min:' . $request->monto_pago_1,
                ], [
                    'monto_recibido_1.required' => 'El monto recibido del pago 1 es obligatorio para pagos en efectivo.',
                    'monto_recibido_1.numeric' => 'El monto recibido del pago 1 debe ser un número.',
                    'monto_recibido_1.min' => "El monto recibido del pago 1 debe ser mayor o igual a S/{$request->monto_pago_1}.",
                ]);
            }
            
            if ($request->tipo_pago_2 == 1) {
                $request->validate([
                    'monto_recibido_2' => 'required|numeric|min:' . $request->monto_pago_2,
                ], [
                    'monto_recibido_2.required' => 'El monto recibido del pago 2 es obligatorio para pagos en efectivo.',
                    'monto_recibido_2.numeric' => 'El monto recibido del pago 2 debe ser un número.',
                    'monto_recibido_2.min' => "El monto recibido del pago 2 debe ser mayor o igual a S/{$request->monto_pago_2}.",
                ]);
            }
        } else {
            $request->validate([
                'tipo_pago' => 'required|integer|min:1|max:7',
                'monto' => 'required|numeric|min:0',
            ], [
                'tipo_pago.required' => 'El tipo de pago es obligatorio.',
                'tipo_pago.integer' => 'El tipo de pago debe ser un número entero.',
                'tipo_pago.min' => 'El tipo de pago debe ser mayor o igual a 1.',
                'tipo_pago.max' => 'El tipo de pago debe ser menor o igual a 7.',
                'monto.required' => 'El monto es obligatorio.',
                'monto.numeric' => 'El monto debe ser un número.',
                'monto.min' => 'El monto debe ser mayor o igual a 0.',
            ]);
            
            // Solo validar monto recibido si es efectivo
            if ($request->tipo_pago == 1) {
                $request->validate([
                    'monto_recibido' => 'required|numeric|min:' . $request->monto,
                ], [
                    'monto_recibido.required' => 'El monto recibido es obligatorio para pagos en efectivo.',
                    'monto_recibido.numeric' => 'El monto recibido debe ser un número.',
                    'monto_recibido.min' => "El monto recibido debe ser mayor o igual a S/{$request->monto}.",
                ]);
            }
        }

        try {
            DB::beginTransaction();

            // Validar que el empleado solo pueda crear ventas en su sucursal asignada
            $user = auth()->user();
            if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para crear ventas en esta sucursal. Solo puedes crear ventas en tu sucursal asignada.');
            }

            // Validar stock disponible en lote antes de crear la venta
            $stockValidation = $this->validarStockEnLote($request->productos, $request->sucursal_id);
            if (!$stockValidation['valido']) {
                throw new \Exception($stockValidation['error']);
            }

            // Manejar cliente temporal o existente
            $clienteId = $request->cliente_id;
            if (str_starts_with($clienteId, 'temp_')) {
                // Es un cliente temporal, crear el cliente real
                $clienteNombre = $request->cliente_nombre ?? 'Cliente Temporal';
                $clienteId = $this->crearClienteReal($clienteId, $clienteNombre);
            } else {
                // Verificar que el cliente existe
                $cliente = Client::find($clienteId);
                if (!$cliente) {
                    throw new \Exception('El cliente seleccionado no existe en la base de datos.');
                }
            }

            $sale = Sale::create([
                'fecha_venta' => now(),
                'total' => $request->total,
                'estado' => 'completada', // Estado automático al crear la venta
                'observaciones' => $request->observaciones,
                'sucursal_id' => $request->sucursal_id,
                'cliente_id' => $clienteId,
                'usuario_id' => auth()->id(),
                'tipo_comprobante' => $request->tipo_comprobante,
            ]);

            // Descontar stock en lote para todos los productos
            $lotesUsados = $this->descontarStockEnLote($request->productos, $request->sucursal_id);
            
            // Agregar productos a la venta
            foreach ($request->productos as $producto) {
                $saleProductData = [
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['subtotal'],
                    'venta_id' => $sale->id,
                ];

                if ($producto['tipo'] === 'producto') {
                    // Verificar si es una presentación o un producto directo
                    if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                        // Es una presentación, obtener el producto padre
                        $presentacion = Presentation::find($producto['item_id']);
                        if ($presentacion) {
                            $saleProductData['producto_id'] = $presentacion->producto_id;
                            $saleProductData['nombre_producto'] = $producto['nombre']; // Ya viene como "Producto - Presentación"
                            $saleProductData['presentacion_id'] = $producto['item_id']; // Guardar el ID de la presentación
                        }
                    } else {
                        // Es un producto directo
                        $saleProductData['producto_id'] = $producto['item_id'];
                        $saleProductData['nombre_producto'] = $producto['nombre'];
                        $saleProductData['presentacion_id'] = null; // No es una presentación
                    }
                    $saleProductData['combo_id'] = null;
                    
                    // Asignar información de lotes
                    $saleProductData['fecha_vencimiento'] = json_encode($lotesUsados[$producto['item_id']] ?? []);
                } else {
                    $saleProductData['combo_id'] = $producto['item_id'];
                    $saleProductData['producto_id'] = null;
                    $saleProductData['nombre_producto'] = null;
                    $saleProductData['presentacion_id'] = null; // Los combos no tienen presentación
                    
                    // Asignar información de lotes del combo
                    $saleProductData['fecha_vencimiento'] = json_encode($lotesUsados[$producto['item_id']] ?? []);
                }

                DB::table('sale_products')->insert($saleProductData);
            }

            // Registrar pagos según el tipo
            if ($dividirPago) {
                // Crear pago 1
                $pago1 = SalePayment::create([
                    'tipo_pago' => $request->tipo_pago_1,
                    'monto' => $request->monto_pago_1,
                    'monto_recibido' => $request->monto_recibido_1 ?? $request->monto_pago_1,
                    'vuelto' => $request->vuelto_1 ?? 0,
                    'fecha_pago' => now(),
                    'venta_id' => $sale->id,
                ]);
                
                // Crear pago 2
                $pago2 = SalePayment::create([
                    'tipo_pago' => $request->tipo_pago_2,
                    'monto' => $request->monto_pago_2,
                    'monto_recibido' => $request->monto_recibido_2 ?? $request->monto_pago_2,
                    'vuelto' => $request->vuelto_2 ?? 0,
                    'fecha_pago' => now(),
                    'venta_id' => $sale->id,
                ]);
                
                // Registrar movimientos de caja para ambos pagos
                $this->registrarMovimientoCaja($sale, 'ingreso', $pago1);
                $this->registrarMovimientoCaja($sale, 'ingreso', $pago2);
            } else {
                // Crear pago único
                $pago = SalePayment::create([
                    'tipo_pago' => $request->tipo_pago,
                    'monto' => $request->monto,
                    'monto_recibido' => $request->monto_recibido ?? $request->monto,
                    'vuelto' => $request->vuelto ?? 0,
                    'fecha_pago' => now(),
                    'venta_id' => $sale->id,
                ]);
                
                // Registrar movimiento de caja
                $this->registrarMovimientoCaja($sale, 'ingreso', $pago);
            }

            // Si es boleta o factura, validar con SUNAT ANTES de crear la venta
            $sunatData = null;
            if (in_array($request->tipo_comprobante, ['boleta', 'factura'])) {
                // Verificar que existan las series de documentos
                $this->verificarSeriesDocumentos($request->sucursal_id);
                
                // Validar con SUNAT antes de crear la venta
                $sunatValidation = $this->validarConSunat($request);
                if (!$sunatValidation['success']) {
                    throw new \Exception('Error en SUNAT: ' . $sunatValidation['error']);
                }
                $sunatData = $sunatValidation['data'];
            }

            DB::commit();

            // Si la validación fue exitosa y es boleta/factura, enviar a SUNAT y guardar respuesta
            if ($sunatData && in_array($request->tipo_comprobante, ['boleta', 'factura'])) {
                $this->enviarASunat($sale, $request, $sunatData);
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Venta creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la venta: ' . $e->getMessage());
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
            $sale->load(['client', 'branch.company', 'user', 'products', 'combos']);
            return view('web.sales.show', compact('sale'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        if (!auth()->user()->can('editar-ventas')) {
            abort(403, 'No tienes permisos para editar ventas.');
        }

        // Validar que solo se puedan editar ventas de tipo ticket
        if (strtolower($sale->tipo_comprobante) !== 'ticket') {
            abort(403, 'Solo se pueden editar ventas de tipo ticket.');
        }

        // Validar que no se puedan editar ventas anuladas
        if ($sale->estado === 'anulada') {
            abort(403, 'No se pueden editar ventas anuladas.');
        }

        // Validar que el empleado solo pueda editar ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para editar ventas de esta sucursal.');
        }

        try {
            $sale->load(['client', 'branch.company', 'user', 'products', 'combos', 'payments']);
            $clients = Client::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();
            $combos = Combo::where('estado', true)->orderBy('nombre')->get();

            return view('web.sales.edit', compact('sale', 'clients', 'branches', 'combos'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        if (!auth()->user()->can('editar-ventas')) {
            abort(403, 'No tienes permisos para editar ventas.');
        }

        $request->validate([
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:completada,anulada,pendiente',
            'observaciones' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
            'cliente_id' => 'required|exists:clients,id',
            'tipo_comprobante' => 'required|string|max:255',
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total debe ser mayor o igual a 0.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser completada, anulada o pendiente.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
        ]);

        try {
            DB::beginTransaction();

            // Validar que solo se puedan editar ventas de tipo ticket
            if (strtolower($sale->tipo_comprobante) !== 'ticket') {
                throw new \Exception('Solo se pueden editar ventas de tipo ticket.');
            }

            // Validar que no se puedan editar ventas anuladas
            if ($sale->estado === 'anulada') {
                throw new \Exception('No se pueden editar ventas anuladas.');
            }

            // Validar que el empleado solo pueda editar ventas de su sucursal
            $user = auth()->user();
            if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para editar ventas de esta sucursal.');
            }

            // Validar que el empleado solo pueda cambiar a su sucursal asignada
            if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
                throw new \Exception('No tienes permisos para cambiar la sucursal de la venta.');
            }

            $sale->update([
                'total' => $request->total,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
                'sucursal_id' => $request->sucursal_id,
                'cliente_id' => $request->cliente_id,
                'tipo_comprobante' => $request->tipo_comprobante,
            ]);

            // Validar stock disponible en lote antes de actualizar
            if ($request->has('productos')) {
                $stockValidation = $this->validarStockEnLote($request->productos, $request->sucursal_id);
                if (!$stockValidation['valido']) {
                    throw new \Exception($stockValidation['error']);
                }
            }

            // Restaurar stock original antes de actualizar
            $this->restaurarStockOriginal($sale);

            // Eliminar relaciones existentes
            $sale->products()->detach();
            $sale->combos()->detach();

            // Descontar stock en lote y agregar productos
            if ($request->has('productos')) {
                Log::info('Productos recibidos en update:', $request->productos);
                
                // Descontar stock en lote para todos los productos
                $lotesUsados = $this->descontarStockEnLote($request->productos, $request->sucursal_id);
                
                foreach ($request->productos as $producto) {
                    if ($producto['tipo'] === 'producto') {
                        // Verificar si es una presentación
                        if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                            $productoId = $producto['producto_id'] ?? $producto['item_id'];
                            
                            $sale->products()->attach($productoId, [
                                'cantidad' => $producto['cantidad'],
                                'precio_unitario' => $producto['precio_unitario'],
                                'subtotal' => $producto['subtotal'],
                                'nombre_producto' => $producto['nombre'],
                                'presentacion_id' => $producto['item_id'],
                                'fecha_vencimiento' => json_encode($lotesUsados[$producto['item_id']] ?? [])
                            ]);
                        } else {
                            $sale->products()->attach($producto['item_id'], [
                                'cantidad' => $producto['cantidad'],
                                'precio_unitario' => $producto['precio_unitario'],
                                'subtotal' => $producto['subtotal'],
                                'nombre_producto' => $producto['nombre'],
                                'presentacion_id' => null,
                                'fecha_vencimiento' => json_encode($lotesUsados[$producto['item_id']] ?? [])
                            ]);
                        }
                    } elseif ($producto['tipo'] === 'combo') {
                        $sale->combos()->attach($producto['item_id'], [
                            'cantidad' => $producto['cantidad'],
                            'precio_unitario' => $producto['precio_unitario'],
                            'subtotal' => $producto['subtotal'],
                            'nombre' => $producto['nombre'],
                            'fecha_vencimiento' => json_encode($lotesUsados[$producto['item_id']] ?? [])
                        ]);
                    }
                }
            }

            // Manejar actualización de pagos según si está dividido o no
            if ($request->has('dividir_pago') && $request->dividir_pago) {
                // Pago dividido: eliminar pagos existentes y crear nuevos
                $sale->payments()->delete();
                
                // Eliminar movimientos de caja existentes para esta venta
                CashBoxMovement::where('venta_id', $sale->id)->delete();
                
                // Crear pago 1
                $pago1 = SalePayment::create([
                    'tipo_pago' => $request->tipo_pago_1,
                    'monto' => $request->monto_pago_1,
                    'monto_recibido' => $request->monto_recibido_1 ?? $request->monto_pago_1,
                    'vuelto' => $request->vuelto_1 ?? 0,
                    'fecha_pago' => now(),
                    'venta_id' => $sale->id,
                ]);
                
                // Crear pago 2
                $pago2 = SalePayment::create([
                    'tipo_pago' => $request->tipo_pago_2,
                    'monto' => $request->monto_pago_2,
                    'monto_recibido' => $request->monto_recibido_2 ?? $request->monto_pago_2,
                    'vuelto' => $request->vuelto_2 ?? 0,
                    'fecha_pago' => now(),
                    'venta_id' => $sale->id,
                ]);
                
                // Registrar movimientos de caja para ambos pagos
                $this->registrarMovimientoCaja($sale, 'ingreso', $pago1);
                $this->registrarMovimientoCaja($sale, 'ingreso', $pago2);
            } else {
                // Pago único: eliminar pagos existentes si hay más de uno y crear uno nuevo
                if ($sale->payments->count() > 1) {
                    // Si había pagos divididos, eliminar todos y crear uno nuevo
                    $sale->payments()->delete();
                    
                    // Eliminar movimientos de caja existentes para esta venta
                    CashBoxMovement::where('venta_id', $sale->id)->delete();
                    
                    // Crear pago único
                    $pagoUnico = SalePayment::create([
                        'tipo_pago' => $request->tipo_pago,
                        'monto' => $request->total,
                        'monto_recibido' => $request->monto_recibido,
                        'vuelto' => $request->vuelto,
                        'fecha_pago' => now(),
                        'venta_id' => $sale->id,
                    ]);
                    
                    // Registrar movimiento de caja
                    $this->registrarMovimientoCaja($sale, 'ingreso', $pagoUnico);
                } else if ($sale->payments->count() == 1) {
                    // Si ya había un pago único, actualizarlo
                    $payment = $sale->payments->first();
                    $payment->update([
                        'tipo_pago' => $request->tipo_pago,
                        'monto' => $request->total,
                        'monto_recibido' => $request->monto_recibido,
                        'vuelto' => $request->vuelto,
                    ]);
                    
                    // Actualizar movimiento de caja
                    $this->actualizarMovimientoCaja($sale);
                }
            }

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Venta actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        if (!auth()->user()->can('eliminar-ventas')) {
            abort(403, 'No tienes permisos para eliminar ventas.');
        }

        // Validar que el empleado solo pueda eliminar ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para eliminar ventas de esta sucursal.');
        }

        try {
            DB::beginTransaction();

            // Eliminar las relaciones con productos y combos
            $sale->products()->detach();
            $sale->combos()->detach();
            
            // Eliminar la venta
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Venta eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Anular una venta
     */
    public function anular(Sale $sale)
    {
        if (!auth()->user()->can('anular-ventas')) {
            abort(403, 'No tienes permisos para anular ventas.');
        }

        // Validar que el empleado solo pueda anular ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            abort(403, 'No tienes permisos para anular ventas de esta sucursal.');
        }

        // Validar tiempo límite para anular boletas y facturas (2 días)
        if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
            $fechaVenta = $sale->fecha_venta->startOfDay();
            $fechaActual = now()->startOfDay();
            $diasTranscurridos = $fechaVenta->diffInDays($fechaActual);
            if ($diasTranscurridos >= 2) {
                abort(403, 'No se pueden anular boletas y facturas después de 2 días de la venta.');
            }
        }

        try {
            DB::beginTransaction();

            // Cargar las relaciones necesarias para restaurar el stock
            $sale->load(['products', 'combos']);

            // Restaurar el stock de todos los productos de la venta
            $this->restaurarStockOriginal($sale);

            // Registrar movimiento de salida por anulación
            $this->registrarMovimientoCaja($sale, 'salida');

            // Cambiar el estado a anulada
            $sale->update(['estado' => 'anulada']);

            DB::commit();

            // Si es boleta o factura, enviar nota de crédito a SUNAT automáticamente
            if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
                try {
                    $this->enviarNotaASunatAutomatica($sale);
                } catch (\Exception $e) {
                    // Si falla el envío a SUNAT, no afectar la anulación
                    Log::error('Error al enviar nota de crédito a SUNAT: ' . $e->getMessage());
                }
            }

            return redirect()->route('sales.index')
                ->with('success', 'Venta anulada exitosamente. El stock ha sido restaurado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    /**
     * Generar comprobante de pago (ticket, boleta o factura)
     */
    public function ticket(Sale $sale)
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
            $sale->load(['client', 'branch.company', 'user', 'products', 'combos', 'payments']);
            
            // Obtener respuesta de SUNAT si es boleta o factura
            $sunatResponse = null;
            $codigoQR = null;
            if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
                $sunatResponse = \App\Models\SunatResponse::where('venta_id', $sale->id)->first();
                if ($sunatResponse && $sunatResponse->exitoso) {
                    $codigoQR = $this->generarCodigoQR($sale, $sunatResponse);
                }
            }

            // Determinar qué vista usar según el tipo de comprobante
            $viewName = 'pdfs.ticket'; // Por defecto
            $filename = 'ticket-venta-' . $sale->id;
            
            if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
                if ($sunatResponse && $sunatResponse->exitoso) {
                    $viewName = 'pdfs.comprobante_sunat';
                    $filename = strtolower($sale->tipo_comprobante) . '-' . $sale->id;
                } else {
                    // Si no se envió exitosamente a SUNAT, mostrar ticket normal
                    $viewName = 'pdfs.ticket';
                    $filename = 'ticket-venta-' . $sale->id;
                }
            }

            // Generar el PDF
            $pdf = PDF::loadView($viewName, compact('sale', 'sunatResponse', 'codigoQR'));
            
            // Configurar el PDF para ticket (tamaño pequeño)
            $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm x 297mm (tamaño estándar de ticket)
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Courier New'
            ]);
            
            return $pdf->stream($filename . '.pdf');
        } catch (\Exception $e) {
            abort(500, 'Error al generar el comprobante: ' . $e->getMessage());
        }
    }

    /**
     * Restaurar stock original de una venta usando información de lotes guardada
     */
    private function restaurarStockOriginal($sale)
    {
        Log::info('Restaurando stock para venta ID: ' . $sale->id);
        
        // Restaurar stock de productos individuales
        foreach ($sale->products as $product) {
            $cantidadOriginal = $product->pivot->cantidad;
            $productoId = $product->id; // El producto_id es el ID del producto en la relación
            
            // Verificar si hay información de lotes guardada
            if ($product->pivot->fecha_vencimiento) {
                $lotesUsados = json_decode($product->pivot->fecha_vencimiento, true);
                if (is_array($lotesUsados)) {
                    Log::info("Restaurando producto con lotes guardados - Producto ID: {$productoId}", $lotesUsados);
                    $this->restaurarStockConLotes($productoId, $lotesUsados);
                } else {
                    // Fallback: usar método original
                    $this->restaurarStockProductoOriginal($product, $sale->sucursal_id);
                }
            } else {
                // No hay información de lotes, usar método original
                $this->restaurarStockProductoOriginal($product, $sale->sucursal_id);
            }
        }

        // Restaurar stock de combos
        foreach ($sale->combos as $combo) {
            $cantidadCombo = $combo->pivot->cantidad;
            Log::info("Restaurando combo - Combo ID: {$combo->id}, Cantidad: {$cantidadCombo}");
            
            // Verificar si hay información de lotes guardada
            if ($combo->pivot->fecha_vencimiento) {
                $lotesUsados = json_decode($combo->pivot->fecha_vencimiento, true);
                if (is_array($lotesUsados)) {
                    Log::info("Restaurando combo con lotes guardados - Combo ID: {$combo->id}", $lotesUsados);
                    // Los lotes del combo contienen información de todos los productos del combo
                    // Necesitamos agrupar por producto_id
                    $lotesPorProducto = [];
                    foreach ($lotesUsados as $lote) {
                        if (!isset($lotesPorProducto[$lote['producto_id']])) {
                            $lotesPorProducto[$lote['producto_id']] = [];
                        }
                        $lotesPorProducto[$lote['producto_id']][] = $lote;
                    }
                    
                    foreach ($lotesPorProducto as $productoId => $lotes) {
                        Log::info("Restaurando producto de combo con lotes - Producto ID: {$productoId}", $lotes);
                        $this->restaurarStockConLotes($productoId, $lotes);
                    }
                } else {
                    // Fallback: usar método original
                    $this->restaurarStockComboOriginal($combo, $sale->sucursal_id);
                }
            } else {
                // No hay información de lotes, usar método original
                $this->restaurarStockComboOriginal($combo, $sale->sucursal_id);
            }
        }
    }

    /**
     * Restaurar stock de producto usando método original (fallback)
     */
    private function restaurarStockProductoOriginal($product, $sucursalId)
    {
        $cantidadOriginal = $product->pivot->cantidad;
        $productoId = $product->id;
        
        // Si es una presentación, restaurar las unidades de la presentación
        if ($product->pivot->presentacion_id) {
            $presentacion = \App\Models\Presentation::find($product->pivot->presentacion_id);
            if ($presentacion) {
                $unidadesADescontar = $cantidadOriginal * $presentacion->unidades;
                Log::info("Restaurando presentación (método original) - Producto ID: {$productoId}, Presentación ID: {$product->pivot->presentacion_id}, Cantidad: {$cantidadOriginal}, Unidades a restaurar: {$unidadesADescontar}");
                $this->restaurarStock($productoId, $sucursalId, $unidadesADescontar);
            }
        } else {
            // Producto individual
            Log::info("Restaurando producto individual (método original) - Producto ID: {$productoId}, Cantidad: {$cantidadOriginal}");
            $this->restaurarStock($productoId, $sucursalId, $cantidadOriginal);
        }
    }

    /**
     * Restaurar stock de combo usando método original (fallback)
     */
    private function restaurarStockComboOriginal($combo, $sucursalId)
    {
        $cantidadCombo = $combo->pivot->cantidad;
        Log::info("Restaurando combo (método original) - Combo ID: {$combo->id}, Cantidad: {$cantidadCombo}");
        
        // Obtener productos del combo
        $productosCombo = $combo->products;
        foreach ($productosCombo as $productoCombo) {
            $cantidadProductoNecesaria = $cantidadCombo * $productoCombo->pivot->cantidad;
            Log::info("Restaurando producto de combo (método original) - Producto ID: {$productoCombo->id}, Cantidad necesaria: {$cantidadProductoNecesaria}");
            $this->restaurarStock($productoCombo->id, $sucursalId, $cantidadProductoNecesaria);
        }
    }

    /**
     * Restaurar stock de un producto usando información de lotes guardada
     */
    private function restaurarStockConLotes($productoId, $lotesUsados)
    {
        Log::info("Restaurando stock con lotes - Producto ID: {$productoId}", $lotesUsados);
        
        foreach ($lotesUsados as $loteInfo) {
            $cantidad = $loteInfo['cantidad'];
            $fechaVencimiento = $loteInfo['fecha_vencimiento'];
            $almacenId = $loteInfo['almacen_id'];
            
            // Buscar el lote específico con la misma fecha de vencimiento y almacén
            $lote = DB::table('product_warehouse')
                ->where('producto_id', $productoId)
                ->where('almacen_id', $almacenId)
                ->where('fecha_vencimiento', $fechaVencimiento)
                ->first();
            
            if ($lote) {
                // Restaurar en el lote existente
                DB::table('product_warehouse')
                    ->where('id', $lote->id)
                    ->update([
                        'stock' => $lote->stock + $cantidad,
                        'updated_at' => now()
                    ]);
                
                Log::info("Restaurado stock en lote existente - Lote ID: {$lote->id}, Producto ID: {$productoId}, Cantidad: {$cantidad}, Fecha: {$fechaVencimiento}");
            } else {
                // Crear nuevo lote con la fecha de vencimiento específica
                DB::table('product_warehouse')->insert([
                    'producto_id' => $productoId,
                    'almacen_id' => $almacenId,
                    'stock' => $cantidad,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                Log::info("Creado nuevo lote para restaurar stock - Producto ID: {$productoId}, Almacén ID: {$almacenId}, Cantidad: {$cantidad}, Fecha: {$fechaVencimiento}");
            }
        }
    }

    /**
     * Restaurar stock de un producto (método original para compatibilidad)
     */
    private function restaurarStock($productoId, $sucursalId, $cantidadARestaurar)
    {
        Log::info("Restaurando stock - Producto ID: {$productoId}, Sucursal ID: {$sucursalId}, Cantidad: {$cantidadARestaurar}");
        
        // Obtener los almacenes de la sucursal
        $almacenes = \App\Models\Warehouse::where('sucursal_id', $sucursalId)->get();
        
        $cantidadRestante = $cantidadARestaurar;
        
        foreach ($almacenes as $almacen) {
            if ($cantidadRestante <= 0) break;
            
            // Obtener lotes de stock ordenados por fecha de vencimiento (más antiguos primero)
            $lotes = DB::table('product_warehouse')
                ->where('producto_id', $productoId)
                ->where('almacen_id', $almacen->id)
                ->orderBy('fecha_vencimiento', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Si no hay lotes existentes, crear uno nuevo sin fecha de vencimiento
            if ($lotes->isEmpty()) {
                DB::table('product_warehouse')->insert([
                    'producto_id' => $productoId,
                    'almacen_id' => $almacen->id,
                    'stock' => $cantidadRestante,
                    'fecha_vencimiento' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Creado nuevo lote para restaurar stock - Producto ID: {$productoId}, Almacén ID: {$almacen->id}, Cantidad: {$cantidadRestante}");
                $cantidadRestante = 0;
                break;
            }
            
            // Restaurar stock en los lotes existentes, priorizando lotes sin fecha de vencimiento
            foreach ($lotes as $lote) {
                if ($cantidadRestante <= 0) break;
                
                // Restaurar stock al lote
                DB::table('product_warehouse')
                    ->where('id', $lote->id)
                    ->update([
                        'stock' => $lote->stock + $cantidadRestante,
                        'updated_at' => now()
                    ]);
                
                Log::info("Restaurado stock en lote - Lote ID: {$lote->id}, Producto ID: {$productoId}, Cantidad restaurada: {$cantidadRestante}");
                $cantidadRestante = 0; // Restaurar todo en el primer lote disponible
            }
        }
        
        if ($cantidadRestante > 0) {
            Log::warning("No se pudo restaurar completamente el stock - Producto ID: {$productoId}, Cantidad restante: {$cantidadRestante}");
        }
    }

    /**
     * Descontar stock de los nuevos productos
     */
    private function descontarStockNuevosProductos($productos, $sucursalId)
    {
        Log::info('Descontando stock para productos:', $productos);
        
        foreach ($productos as $producto) {
            if ($producto['tipo'] === 'producto') {
                // Verificar si es una presentación
                if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                    $presentacion = \App\Models\Presentation::find($producto['item_id']);
                    if ($presentacion) {
                        $unidadesADescontar = $producto['cantidad'] * $presentacion->unidades;
                        $productoId = $producto['producto_id'] ?? $producto['item_id'];
                        Log::info("Descontando presentación - Producto ID: {$productoId}, Presentación ID: {$producto['item_id']}, Cantidad: {$producto['cantidad']}, Unidades a descontar: {$unidadesADescontar}");
                        $this->descontarStock($productoId, $sucursalId, $unidadesADescontar);
                    }
                } else {
                    // Producto individual
                    Log::info("Descontando producto individual - Producto ID: {$producto['item_id']}, Cantidad: {$producto['cantidad']}");
                    $this->descontarStock($producto['item_id'], $sucursalId, $producto['cantidad']);
                }
            } elseif ($producto['tipo'] === 'combo') {
                $combo = \App\Models\Combo::find($producto['item_id']);
                if ($combo) {
                    $cantidadCombo = $producto['cantidad'];
                    
                    // Obtener productos del combo
                    $productosCombo = $combo->products;
                    foreach ($productosCombo as $productoCombo) {
                        $cantidadProductoNecesaria = $cantidadCombo * $productoCombo->pivot->cantidad;
                        $this->descontarStock($productoCombo->id, $sucursalId, $cantidadProductoNecesaria);
                    }
                }
            }
        }
    }

    /**
     * Descontar stock de un producto y retornar información de lotes usados
     */
    private function descontarStockConLotes($productoId, $sucursalId, $cantidadADescontar)
    {
        // Obtener los almacenes de la sucursal
        $almacenes = \App\Models\Warehouse::where('sucursal_id', $sucursalId)->get();
        
        $cantidadRestante = $cantidadADescontar;
        $lotesUsados = [];
        
        foreach ($almacenes as $almacen) {
            if ($cantidadRestante <= 0) break;
            
            // Obtener lotes de stock ordenados por fecha de vencimiento (más antiguos primero)
            $lotes = DB::table('product_warehouse')
                ->where('producto_id', $productoId)
                ->where('almacen_id', $almacen->id)
                ->where(function($query) {
                    $query->whereNull('fecha_vencimiento')
                          ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
                })
                ->orderBy('fecha_vencimiento', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            foreach ($lotes as $lote) {
                if ($cantidadRestante <= 0) break;
                
                $cantidadDisponible = $lote->stock;
                $cantidadADescontarDelLote = min($cantidadRestante, $cantidadDisponible);
                
                // Actualizar el stock del lote
                DB::table('product_warehouse')
                    ->where('id', $lote->id)
                    ->update([
                        'stock' => $cantidadDisponible - $cantidadADescontarDelLote,
                        'updated_at' => now()
                    ]);
                
                // Guardar información del lote usado
                $lotesUsados[] = [
                    'cantidad' => $cantidadADescontarDelLote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento,
                    'almacen_id' => $almacen->id
                ];
                
                $cantidadRestante -= $cantidadADescontarDelLote;
            }
        }
        
        if ($cantidadRestante > 0) {
            throw new \Exception("No hay suficiente stock disponible para descontar {$cantidadADescontar} unidades del producto ID {$productoId}");
        }
        
        return $lotesUsados;
    }

    /**
     * Descontar stock de un producto (método original para compatibilidad)
     */
    private function descontarStock($productoId, $sucursalId, $cantidadADescontar)
    {
        $this->descontarStockConLotes($productoId, $sucursalId, $cantidadADescontar);
    }

    /**
     * Registrar movimiento de caja para una venta
     */
    private function registrarMovimientoCaja($sale, $tipo = 'ingreso', $payment = null)
    {
        try {
            // Obtener la sesión de caja activa de la sucursal
            $sesionCaja = \App\Models\CashBoxSession::where('sucursal_id', $sale->sucursal_id)
                ->where('estado', 'abierta')
                ->first();

            if (!$sesionCaja) {
                Log::warning("No hay sesión de caja abierta para la sucursal ID: {$sale->sucursal_id}");
                return;
            }

            // Si se pasa un pago específico, usarlo; si no, usar todos los pagos de la venta
            if ($payment) {
                $payments = collect([$payment]);
            } else {
                $payments = $sale->payments;
            }

            if ($payments->isEmpty()) {
                Log::warning("No se encontraron pagos para la venta ID: {$sale->id}");
                return;
            }

            // Crear movimientos para cada pago
            foreach ($payments as $payment) {
                // Mapear método de pago
                $metodoPago = $this->mapearMetodoPago($payment->tipo_pago);

                // Crear el movimiento
                \App\Models\CashBoxMovement::create([
                    'tipo' => $tipo,
                    'metodo_pago' => $metodoPago,
                    'monto' => $payment->monto,
                    'descripcion' => $tipo === 'ingreso' 
                        ? "Venta #{$sale->id} - {$sale->client->nombre_completo} (Pago: S/{$payment->monto})"
                        : "Anulación de venta #{$sale->id} - {$sale->client->nombre_completo} (Pago: S/{$payment->monto})",
                    'sesion_caja_id' => $sesionCaja->id,
                    'venta_id' => $sale->id,
                ]);

                Log::info("Movimiento de caja registrado - Venta ID: {$sale->id}, Tipo: {$tipo}, Monto: {$payment->monto}");
            }
        } catch (\Exception $e) {
            Log::error("Error al registrar movimiento de caja - Venta ID: {$sale->id}, Error: " . $e->getMessage());
        }
    }

    /**
     * Actualizar movimiento de caja existente
     */
    private function actualizarMovimientoCaja($sale)
    {
        try {
            // Buscar el movimiento de ingreso existente
            $movimiento = \App\Models\CashBoxMovement::where('venta_id', $sale->id)
                ->where('tipo', 'ingreso')
                ->first();

            if (!$movimiento) {
                Log::warning("No se encontró movimiento de caja para la venta ID: {$sale->id}");
                return;
            }

            // Obtener el pago actualizado
            $payment = $sale->payments->first();
            if (!$payment) {
                Log::warning("No se encontró pago para la venta ID: {$sale->id}");
                return;
            }

            // Mapear método de pago
            $metodoPago = $this->mapearMetodoPago($payment->tipo_pago);

            // Actualizar el movimiento
            $movimiento->update([
                'metodo_pago' => $metodoPago,
                'monto' => $payment->monto,
                'descripcion' => "Venta #{$sale->id} - {$sale->client->nombre_completo}",
            ]);

            Log::info("Movimiento de caja actualizado - Venta ID: {$sale->id}, Monto: {$payment->monto}");
        } catch (\Exception $e) {
            Log::error("Error al actualizar movimiento de caja - Venta ID: {$sale->id}, Error: " . $e->getMessage());
        }
    }

    /**
     * Mapear tipo de pago a método de pago de caja
     */
    private function mapearMetodoPago($tipoPago)
    {
        switch ($tipoPago) {
            case 1: // Efectivo
                return 'efectivo';
            case 2: // Tarjeta (Crédito/Débito)
            case 3: // Tarjeta (Crédito/Débito) - Mantener compatibilidad
                return 'tarjeta';
            case 4: // Transferencia - Mantener compatibilidad
                return 'transferencia';
            case 5: // Billetera Virtual (Yape/Plin)
            case 6: // Billetera Virtual (Yape/Plin) - Mantener compatibilidad
                return 'billetera_virtual';
            case 7: // Otros - Mantener compatibilidad
            default:
                return 'efectivo';
        }
    }

    /**
     * Generar JSON para SUNAT
     */
    private function generateSunatJson(Request $request)
    {
        // Obtener datos de la empresa
        $branch = Branch::find($request->sucursal_id);
        $company = $branch->company;

        // Obtener datos del cliente
        $clienteId = $request->cliente_id;
        if (str_starts_with($clienteId, 'temp_')) {
            // Para clientes temporales, extraer el documento del ID temporal
            $documento = str_replace('temp_', '', $clienteId);
            
            // Verificar si ya existe un cliente con ese documento
            $cliente = Client::where('nro_documento', $documento)->first();
            
            if (!$cliente) {
                // Si no existe, buscar el cliente general (para tickets)
                $cliente = Client::where('nro_documento', '00000000')->first();
                
                // Si es factura y no hay cliente válido, lanzar error
                if ($request->tipo_comprobante === 'factura' && (!$cliente || $cliente->nro_documento === '00000000')) {
                    throw new \Exception('Para facturas se requiere un cliente con RUC válido. No se puede usar cliente temporal.');
                }
            }
        } else {
            $cliente = Client::find($clienteId);
        }
        
        // Validar que el cliente existe
        if (!$cliente) {
            throw new \Exception('No se encontró el cliente especificado.');
        }
        
        // Validar que para facturas, el cliente tenga un RUC válido (11 dígitos)
        if ($request->tipo_comprobante === 'factura') {
            if (strlen($cliente->nro_documento) !== 11) {
                throw new \Exception('Para facturas se requiere un cliente con RUC válido (11 dígitos). El cliente actual tiene documento de ' . strlen($cliente->nro_documento) . ' dígitos.');
            }
        }

        // Obtener serie y correlativo
        $documentSeries = \App\Models\DocumentSeries::where('sucursal_id', $request->sucursal_id)
            ->where('tipo_comprobante', $request->tipo_comprobante)
            ->first();

        if (!$documentSeries) {
            throw new \Exception('No se encontró serie de documento para este tipo de comprobante.');
        }

        // Determinar tipo de documento SUNAT
        $tipoDoc = $request->tipo_comprobante === 'factura' ? '01' : '03';
        
        // Determinar tipo de documento del cliente basado en la longitud del documento
        $tipoDocCliente = strlen($cliente->nro_documento) === 11 ? '6' : '1';

        // Generar detalles de productos
        $details = [];
        foreach ($request->productos as $producto) {
            $mtoValorUnitario = $producto['precio_unitario'] / 1.18; // Precio sin IGV
            $mtoValorVenta = $mtoValorUnitario * $producto['cantidad'];
            $mtoBaseIgv = $mtoValorVenta;
            $igv = $mtoBaseIgv * 0.18;
            $mtoPrecioUnitario = $producto['precio_unitario'];

            $details[] = [
                'tipAfeIgv' => 10,
                'codProducto' => $this->getProductBarcode($producto),
                'unidad' => 'NIU',
                'descripcion' => $producto['nombre'],
                'cantidad' => $producto['cantidad'],
                'mtoValorUnitario' => round($mtoValorUnitario, 2),
                'mtoValorVenta' => round($mtoValorVenta, 2),
                'mtoBaseIgv' => round($mtoBaseIgv, 2),
                'porcentajeIgv' => 18,
                'igv' => round($igv, 2),
                'totalImpuestos' => round($igv, 2),
                'mtoPrecioUnitario' => round($mtoPrecioUnitario, 2)
            ];
        }

        // Construir JSON
        $json = [
            'ublVersion' => '2.1',
            'tipoDoc' => $tipoDoc,
            'tipoOperacion' => '0101',
            'serie' => $documentSeries->serie,
            'correlativo' => (string)($documentSeries->ultimo_correlativo + 1),
            'fechaEmision' => now()->format('Y-m-d\TH:i:s-05:00'),
            'formaPago' => [
                'moneda' => 'PEN',
                'tipo' => 'Contado'
            ],
            'tipoMoneda' => 'PEN',
            'company' => [
                'ruc' => (int)$company->ruc,
                'razonSocial' => $company->razon_social,
                'nombreComercial' => $company->razon_social,
                'address' => [
                    'ubigueo' => '140101',
                    'departamento' => 'LAMBAYEQUE',
                    'provincia' => 'CHICLAYO',
                    'distrito' => 'CHICLAYO',
                    'urbanizacion' => '-',
                    'direccion' => $company->direccion,
                    'codLocal' => '0000'
                ]
            ],
            'client' => [
                'tipoDoc' => $tipoDocCliente,
                'numDoc' => (int)$cliente->nro_documento,
                'rznSocial' => $cliente->nombre_completo
            ],
            'details' => $details
        ];

        return $json;
    }

    /**
     * Validar con SUNAT antes de crear la venta
     */
    private function validarConSunat($request)
    {
        try {
            // Generar JSON para SUNAT
            $sunatJson = $this->generateSunatJson($request);
            
            // Obtener token JWT de la sesión
            $jwtToken = session('jwt_token');

            Log::info('validarConSunat - request', [
                'tipo_comprobante' => $request->tipo_comprobante,
                'sucursal_id' => $request->sucursal_id,
                'cliente_id' => $request->cliente_id,
                'cliente_documento' => $request->cliente_documento ?? null,
                'productos_count' => is_array($request->productos) ? count($request->productos) : null,
                'total' => $request->total ?? null,
            ]);
            
            // Enviar a SUNAT para validación
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jwtToken
            ])->post('http://greenter.test/api/invoices/send', $sunatJson);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Verificar si SUNAT respondió con éxito
                if (isset($data['sunatResponse']['success']) && $data['sunatResponse']['success']) {
                    return [
                        'success' => true,
                        'data' => $data
                    ];
                } else {
                    Log::error('validarConSunat - success=false payload', [
                        'http_status' => $response->status(),
                        'has_cdrResponse' => isset($data['sunatResponse']['cdrResponse']),
                        'cdr_code' => $data['sunatResponse']['cdrResponse']['code'] ?? null,
                        'cdr_description' => $data['sunatResponse']['cdrResponse']['description'] ?? null,
                        'payload_preview' => substr(json_encode($data), 0, 4000),
                    ]);
                    // SUNAT respondió pero con error
                    $errorMessage = $data['sunatResponse']['cdrResponse']['description'] ?? 'Error desconocido en SUNAT';
                    return [
                        'success' => false,
                        'error' => $errorMessage
                    ];
                }
            } else {
                // Error en la comunicación con SUNAT
                $errorData = $response->json();
                Log::error('validarConSunat - http error', [
                    'http_status' => $response->status(),
                    'error_preview' => is_array($errorData) ? substr(json_encode($errorData), 0, 4000) : (string)$response->body(),
                ]);
                $errorMessage = $errorData['message'] ?? 'Error de comunicación con SUNAT';
                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Log::error('validarConSunat - excepción', [
                'message' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Excepción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Guardar respuesta de SUNAT (después de crear la venta exitosamente)
     */
    private function enviarASunat($sale, $request, $sunatData)
    {
        try {
            // Extraer número de documento del XML
            $numeroDocumento = $this->extractDocumentNumberFromXml($sunatData['xml']);
            
            // Guardar archivos XML y CDR
            $xmlPath = $this->saveXmlFile($sunatData['xml'], $sale->id, $numeroDocumento);
            $cdrPath = null;
            
            if (isset($sunatData['sunatResponse']['cdrZip'])) {
                $cdrPath = $this->saveCdrFile($sunatData['sunatResponse']['cdrZip'], $sale->id, $numeroDocumento);
            }
            
            // Guardar respuesta en BD
            \App\Models\SunatResponse::create([
                'venta_id' => $sale->id,
                'numero_documento' => $numeroDocumento,
                'tipo_documento' => $sale->tipo_comprobante === 'factura' ? '01' : '03',
                'xml_path' => $xmlPath,
                'cdr_path' => $cdrPath,
                'hash_documento' => $sunatData['hash'],
                'codigo_respuesta' => $sunatData['sunatResponse']['cdrResponse']['code'],
                'descripcion_respuesta' => $sunatData['sunatResponse']['cdrResponse']['description'],
                'exitoso' => $sunatData['sunatResponse']['success'],
                'respuesta_completa' => $sunatData,
            ]);

            // Actualizar el último correlativo en document_series
            $this->actualizarCorrelativoDocumento($sale, $numeroDocumento);
            
        } catch (\Exception $e) {
            // Guardar error en BD
            \App\Models\SunatResponse::create([
                'venta_id' => $sale->id,
                'numero_documento' => 'ERROR',
                'tipo_documento' => $sale->tipo_comprobante === 'factura' ? '01' : '03',
                'xml_path' => '',
                'cdr_path' => null,
                'hash_documento' => '',
                'codigo_respuesta' => 'EXCEPTION',
                'descripcion_respuesta' => 'Excepción al guardar respuesta SUNAT: ' . $e->getMessage(),
                'exitoso' => false,
                'respuesta_completa' => ['error' => $e->getMessage()],
            ]);
        }
    }

    /**
     * Generar código QR con formato SUNAT
     */
    private function generarCodigoQR($sale, $sunatResponse)
    {
        try {
            // Extraer serie y correlativo del número de documento
            $numeroDocumento = $sunatResponse->numero_documento ?? '';
            if (preg_match('/^([A-Z]\d+)-(\d+)$/', $numeroDocumento, $matches)) {
                $serie = $matches[1];
                $correlativo = $matches[2];
            } else {
                // Si no se puede extraer, usar valores por defecto
                $serie = $sale->tipo_comprobante === 'factura' ? 'F001' : 'B001';
                $correlativo = '00000001';
            }

            // Determinar tipo de documento
            $tipoDoc = $sale->tipo_comprobante === 'factura' ? '01' : '03';

            // Determinar tipo de documento del cliente
            $tipoDocCliente = $sale->tipo_comprobante === 'factura' ? '6' : '1';

            // Calcular IGV
            $igv = $sale->total - ($sale->total / 1.18);

            // Formatear fecha
            $fecha = $sale->fecha_venta ? $sale->fecha_venta->format('Y-m-d') : now()->format('Y-m-d');

            // Construir código QR
            $codigoQR = sprintf(
                '%s|%s|%s|%s|%.2f|%.2f|%s|%s|%s',
                $sale->branch->company->ruc ?? '20100100100',  // RUC
                $tipoDoc,                                      // Tipo documento (01/03)
                $serie,                                        // Serie
                $correlativo,                                  // Correlativo
                $igv,                                          // IGV
                $sale->total,                                  // Total
                $fecha,                                        // Fecha
                $tipoDocCliente,                               // Tipo documento cliente (1/6)
                $sale->client->nro_documento ?? '00000000'     // Número documento cliente
            );

            return $codigoQR;
        } catch (\Exception $e) {
            Log::error("Error al generar código QR: " . $e->getMessage());
            return 'ERROR_QR';
        }
    }

    /**
     * Verificar y crear series de documentos si no existen
     */
    private function verificarSeriesDocumentos($sucursalId)
    {
        $tiposComprobantes = ['boleta', 'factura'];
        $seriesPorDefecto = [
            'boleta' => 'B001',
            'factura' => 'F001'
        ];

        foreach ($tiposComprobantes as $tipo) {
            $existe = DocumentSeries::where('sucursal_id', $sucursalId)
                ->where('tipo_comprobante', $tipo)
                ->exists();

            if (!$existe) {
                DocumentSeries::create([
                    'tipo_comprobante' => $tipo,
                    'serie' => $seriesPorDefecto[$tipo],
                    'ultimo_correlativo' => 0,
                    'sucursal_id' => $sucursalId
                ]);

                Log::info("Serie creada automáticamente: {$seriesPorDefecto[$tipo]} para {$tipo} en sucursal {$sucursalId}");
            }
        }
    }

    /**
     * Obtener el siguiente correlativo disponible para una serie
     */
    private function obtenerSiguienteCorrelativo($tipoComprobante, $sucursalId, $serie)
    {
        $documentSeries = DocumentSeries::where('serie', $serie)
            ->where('tipo_comprobante', $tipoComprobante)
            ->where('sucursal_id', $sucursalId)
            ->first();
        
        if ($documentSeries) {
            return $documentSeries->ultimo_correlativo + 1;
        }
        
        // Si no existe la serie, empezar desde 1
        return 1;
    }

    /**
     * Actualizar el último correlativo en document_series
     */
    private function actualizarCorrelativoDocumento($sale, $numeroDocumento)
    {
        try {
            // Extraer serie y correlativo del número de documento
            // Formatos soportados: F001-00000001, B002-10300686, etc.
            if (preg_match('/^([A-Z]\d+)-(\d+)$/', $numeroDocumento, $matches)) {
                $serie = $matches[1];
                $correlativo = (int)$matches[2];
                
                // Buscar la serie en document_series
                $documentSeries = DocumentSeries::where('serie', $serie)
                    ->where('tipo_comprobante', $sale->tipo_comprobante)
                    ->where('sucursal_id', $sale->sucursal_id)
                    ->first();
                
                if ($documentSeries) {
                    // Actualizar el último correlativo si el actual es mayor
                    if ($correlativo > $documentSeries->ultimo_correlativo) {
                        $documentSeries->update([
                            'ultimo_correlativo' => $correlativo
                        ]);
                        
                        Log::info("Correlativo actualizado para serie {$serie}: {$correlativo} (anterior: {$documentSeries->ultimo_correlativo})");
                    } else {
                        Log::info("Correlativo no actualizado - el actual ({$correlativo}) no es mayor que el último ({$documentSeries->ultimo_correlativo})");
                    }
                } else {
                    // Si no existe la serie, crearla
                    DocumentSeries::create([
                        'tipo_comprobante' => $sale->tipo_comprobante,
                        'serie' => $serie,
                        'ultimo_correlativo' => $correlativo,
                        'sucursal_id' => $sale->sucursal_id
                    ]);
                    
                    Log::info("Nueva serie creada: {$serie} con correlativo {$correlativo}");
                }
            } else {
                Log::warning("No se pudo extraer serie y correlativo del número de documento: {$numeroDocumento}");
            }
        } catch (\Exception $e) {
            Log::error("Error al actualizar correlativo: " . $e->getMessage());
        }
    }

    /**
     * Extraer número de documento del XML
     */
    private function extractDocumentNumberFromXml($xmlString)
    {
        preg_match('/<cbc:ID>([^<]+)<\/cbc:ID>/', $xmlString, $matches);
        return $matches[1] ?? 'N/A';
    }

    /**
     * Guardar archivo XML
     */
    private function saveXmlFile($xmlContent, $saleId, $numeroDocumento)
    {
        $filename = "sale_{$saleId}_{$numeroDocumento}.xml";
        $path = "public/sunat/xml/{$filename}";
        
        Storage::put($path, $xmlContent);
        
        return "storage/sunat/xml/{$filename}";
    }

    /**
     * Guardar archivo CDR
     */
    private function saveCdrFile($cdrZipBase64, $saleId, $numeroDocumento)
    {
        $filename = "sale_{$saleId}_{$numeroDocumento}.zip";
        $path = "public/sunat/cdr/{$filename}";
        
        $cdrContent = base64_decode($cdrZipBase64);
        Storage::put($path, $cdrContent);
        
        return "storage/sunat/cdr/{$filename}";
    }

    /**
     * Obtener código de barras del producto
     */
    private function getProductBarcode($producto)
    {
        if ($producto['tipo'] === 'producto') {
            if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                $presentacion = \App\Models\Presentation::find($producto['item_id']);
                if ($presentacion) {
                    $product = Product::find($presentacion->producto_id);
                    return $product->barcode ?? 'P001';
                }
            } else {
                $product = Product::find($producto['item_id']);
                return $product->barcode ?? 'P001';
            }
        } else {
            // Para combos, usar formato C + ID con ceros a la izquierda
            return 'C' . str_pad($producto['item_id'], 3, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Crear cliente real a partir de datos temporales
     */
    private function crearClienteReal($clienteTempId, $clienteNombre)
    {
        try {
            // Extraer documento del ID temporal
            $documento = substr($clienteTempId, 5); // Remover 'temp_' del inicio
            
            // Determinar tipo de documento
            $tipoDocumento = strlen($documento) === 8 ? 'DNI' : 'RUC';
            
            // Crear el cliente real
            $clienteNuevo = Client::create([
                'nombre_completo' => $clienteNombre,
                'tipo_documento' => $tipoDocumento,
                'nro_documento' => $documento,
                'telefono' => null,
                'email' => null,
                'direccion' => null,
                'activo' => true
            ]);
            
            Log::info("Cliente temporal convertido a real: {$clienteNuevo->id} - {$clienteNuevo->nombre_completo}");
            
            return $clienteNuevo->id;
            
        } catch (\Exception $e) {
            Log::error("Error al crear cliente real: " . $e->getMessage());
            throw new \Exception('Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Validar stock disponible en lote para todos los productos
     */
    private function validarStockEnLote($productos, $sucursalId)
    {
        try {
            // Obtener todos los IDs de productos únicos
            $productosIds = [];
            $productosInfo = [];
            
            foreach ($productos as $producto) {
                if ($producto['tipo'] === 'producto') {
                    if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                        // Es una presentación, obtener el producto padre
                        $presentacion = \App\Models\Presentation::find($producto['item_id']);
                        if ($presentacion) {
                            $productoId = $presentacion->producto_id;
                            $unidadesNecesarias = $producto['cantidad'] * $presentacion->unidades;
                            $productosIds[] = $productoId;
                            $productosInfo[$productoId] = [
                                'nombre' => $producto['nombre'],
                                'cantidad_solicitada' => $producto['cantidad'],
                                'unidades_necesarias' => $unidadesNecesarias,
                                'es_presentacion' => true,
                                'presentacion_unidades' => $presentacion->unidades
                            ];
                        }
                    } else {
                        // Producto individual
                        $productoId = $producto['item_id'];
                        $productosIds[] = $productoId;
                        $productosInfo[$productoId] = [
                            'nombre' => $producto['nombre'],
                            'cantidad_solicitada' => $producto['cantidad'],
                            'unidades_necesarias' => $producto['cantidad'],
                            'es_presentacion' => false
                        ];
                    }
                } elseif ($producto['tipo'] === 'combo') {
                    // Para combos, obtener productos individuales
                    $combo = \App\Models\Combo::with('products')->find($producto['item_id']);
                    if ($combo) {
                        foreach ($combo->products as $productoCombo) {
                            $productoId = $productoCombo->id;
                            $cantidadProductoNecesaria = $productoCombo->pivot->cantidad * $producto['cantidad'];
                            
                            if (!in_array($productoId, $productosIds)) {
                                $productosIds[] = $productoId;
                                $productosInfo[$productoId] = [
                                    'nombre' => $productoCombo->nombre,
                                    'cantidad_solicitada' => $cantidadProductoNecesaria,
                                    'unidades_necesarias' => $cantidadProductoNecesaria,
                                    'es_presentacion' => false,
                                    'es_combo' => true
                                ];
                            } else {
                                // Sumar a la cantidad existente si el producto ya está en la lista
                                $productosInfo[$productoId]['cantidad_solicitada'] += $cantidadProductoNecesaria;
                                $productosInfo[$productoId]['unidades_necesarias'] += $cantidadProductoNecesaria;
                            }
                        }
                    }
                }
            }
            
            // Obtener stock disponible para todos los productos en una sola consulta
            $stockDisponible = DB::table('product_warehouse')
                ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
                ->whereIn('product_warehouse.producto_id', $productosIds)
                ->where('warehouses.sucursal_id', $sucursalId)
                ->where(function($query) {
                    $query->whereNull('product_warehouse.fecha_vencimiento')
                          ->orWhere('product_warehouse.fecha_vencimiento', '>=', now()->toDateString());
                })
                ->select('product_warehouse.producto_id', DB::raw('SUM(product_warehouse.stock) as stock_total'))
                ->groupBy('product_warehouse.producto_id')
                ->get()
                ->keyBy('producto_id');
            
            // Validar stock para cada producto
            $errores = [];
            foreach ($productosInfo as $productoId => $info) {
                $stockTotal = $stockDisponible->get($productoId);
                $stockDisponibleProducto = $stockTotal ? $stockTotal->stock_total : 0;
                
                if ($info['unidades_necesarias'] > $stockDisponibleProducto) {
                    if ($info['es_presentacion']) {
                        $maximoPresentaciones = floor($stockDisponibleProducto / $info['presentacion_unidades']);
                        $errores[] = "Stock insuficiente para {$info['nombre']}. Disponible: {$maximoPresentaciones} presentaciones, Solicitado: {$info['cantidad_solicitada']}";
                    } else {
                        $errores[] = "Stock insuficiente para {$info['nombre']}. Disponible: {$stockDisponibleProducto} unidades, Solicitado: {$info['cantidad_solicitada']}";
                    }
                }
            }
            
            if (!empty($errores)) {
                return [
                    'valido' => false,
                    'error' => implode('; ', $errores),
                    'detalles' => $errores
                ];
            }
            
            return [
                'valido' => true,
                'error' => null,
                'detalles' => []
            ];
            
        } catch (\Exception $e) {
            Log::error("Error en validación de stock en lote: " . $e->getMessage());
            return [
                'valido' => false,
                'error' => 'Error al validar stock: ' . $e->getMessage(),
                'detalles' => []
            ];
        }
    }

    /**
     * Descontar stock en lote para todos los productos
     */
    private function descontarStockEnLote($productos, $sucursalId)
    {
        try {
            $lotesUsados = [];
            
            foreach ($productos as $producto) {
                if ($producto['tipo'] === 'producto') {
                    if (isset($producto['es_presentacion']) && $producto['es_presentacion']) {
                        // Es una presentación
                        $presentacion = \App\Models\Presentation::find($producto['item_id']);
                        if ($presentacion) {
                            $unidadesADescontar = $producto['cantidad'] * $presentacion->unidades;
                            $lotesProducto = $this->descontarStockConLotes($presentacion->producto_id, $sucursalId, $unidadesADescontar);
                            $lotesUsados[$producto['item_id']] = $lotesProducto;
                        }
                    } else {
                        // Producto individual
                        $lotesProducto = $this->descontarStockConLotes($producto['item_id'], $sucursalId, $producto['cantidad']);
                        $lotesUsados[$producto['item_id']] = $lotesProducto;
                    }
                } elseif ($producto['tipo'] === 'combo') {
                    // Para combos, descontar cada producto individual
                    $combo = \App\Models\Combo::with('products')->find($producto['item_id']);
                    if ($combo) {
                        $lotesCombo = [];
                        foreach ($combo->products as $productoCombo) {
                            $cantidadProductoNecesaria = $productoCombo->pivot->cantidad * $producto['cantidad'];
                            $lotesProducto = $this->descontarStockConLotes($productoCombo->id, $sucursalId, $cantidadProductoNecesaria);
                            $lotesCombo = array_merge($lotesCombo, $lotesProducto);
                        }
                        $lotesUsados[$producto['item_id']] = $lotesCombo;
                    }
                }
            }
            
            return $lotesUsados;
            
        } catch (\Exception $e) {
            Log::error("Error en descuento de stock en lote: " . $e->getMessage());
            throw new \Exception('Error al descontar stock: ' . $e->getMessage());
        }
    }

    /**
     * Generar JSON de nota de crédito para una venta
     */
    public function jsonNota(Sale $sale)
    {
        if (!auth()->user()->can('anular-ventas')) {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permisos para anular ventas.'
            ], 403);
        }

        // Validar que el empleado solo pueda ver ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permisos para ver ventas de esta sucursal.'
            ], 403);
        }

        try {
            // Cargar las relaciones necesarias
            $sale->load(['client', 'branch.company', 'products', 'combos']);

            // Determinar tipo de documento afectado
            $tipDocAfectado = strtolower($sale->tipo_comprobante) === 'factura' ? '01' : '03';

            // Determinar serie para nota de crédito
            $serieNota = strtolower($sale->tipo_comprobante) === 'factura' ? 'FC01' : 'BC01';

            // Obtener serie y correlativo de la nota de crédito
            $documentSeries = \App\Models\DocumentSeries::where('sucursal_id', $sale->sucursal_id)
                ->where('tipo_comprobante', strtolower($sale->tipo_comprobante) === 'factura' ? 'Nota de Crédito - Factura' : 'Nota de Crédito - Boleta')
                ->first();

            if (!$documentSeries) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se encontró serie de documento para nota de crédito.'
                ], 404);
            }

            // Obtener número de documento original (de la respuesta SUNAT)
            $sunatResponse = \App\Models\SunatResponse::where('venta_id', $sale->id)->first();
            $numDocfectado = $sunatResponse ? $sunatResponse->numero_documento : 'F001-00000001'; // Fallback

            // Generar detalles de productos y combos
            $details = [];
            
            // Obtener todos los items de la venta (productos y combos)
            $saleItems = DB::table('sale_products')
                ->where('venta_id', $sale->id)
                ->get();
            
            foreach ($saleItems as $item) {
                $mtoValorUnitario = $item->precio_unitario / 1.18; // Precio sin IGV
                $mtoValorVenta = $mtoValorUnitario * $item->cantidad;
                $mtoBaseIgv = $mtoValorVenta;
                $igv = $mtoBaseIgv * 0.18;
                $mtoPrecioUnitario = $item->precio_unitario;

                // Determinar si es producto o combo
                if ($item->producto_id !== null) {
                    // Es un producto
                    $product = Product::find($item->producto_id);
                    $nombre = $item->nombre_producto ?? $product->nombre ?? 'Producto';
                    
                    // Construir array para getProductBarcode
                    $productoArray = [
                        'tipo' => 'producto',
                        'item_id' => $item->producto_id,
                        'es_presentacion' => $item->presentacion_id !== null,
                        'nombre' => $nombre
                    ];
                    
                    $codProducto = $this->getProductBarcode($productoArray);
                } else {
                    // Es un combo
                    $combo = \App\Models\Combo::find($item->combo_id);
                    $nombre = $combo->nombre ?? 'Combo';
                    
                    // Construir array para getProductBarcode
                    $productoArray = [
                        'tipo' => 'combo',
                        'item_id' => $item->combo_id,
                        'nombre' => $nombre
                    ];
                    
                    $codProducto = $this->getProductBarcode($productoArray);
                }

                $details[] = [
                    'tipAfeIgv' => 10,
                    'codProducto' => $codProducto,
                    'unidad' => 'NIU',
                    'descripcion' => $nombre,
                    'cantidad' => $item->cantidad,
                    'mtoValorUnitario' => round($mtoValorUnitario, 2),
                    'mtoValorVenta' => round($mtoValorVenta, 2),
                    'mtoBaseIgv' => round($mtoBaseIgv, 2),
                    'porcentajeIgv' => 18,
                    'igv' => round($igv, 2),
                    'totalImpuestos' => round($igv, 2),
                    'mtoPrecioUnitario' => round($mtoPrecioUnitario, 2)
                ];
            }

            // Construir JSON de nota de crédito
            $json = [
                'ublVersion' => '2.1',
                'tipoDoc' => '07',
                'serie' => $documentSeries->serie,
                'correlativo' => (string)($documentSeries->ultimo_correlativo + 1),
                'fechaEmision' => now()->format('Y-m-d\TH:i:s-05:00'),
                'tipDocAfectado' => $tipDocAfectado,
                'numDocfectado' => $numDocfectado,
                'codMotivo' => '01',
                'desMotivo' => 'ANULACION DE LA OPERACION',
                'formaPago' => [
                    'moneda' => 'PEN',
                    'tipo' => 'Contado'
                ],
                'tipoMoneda' => 'PEN',
                'company' => [
                    'ruc' => (int)$sale->branch->company->ruc,
                    'razonSocial' => $sale->branch->company->razon_social,
                    'nombreComercial' => $sale->branch->company->razon_social,
                    'address' => [
                        'ubigueo' => '140101',
                        'departamento' => 'LAMBAYEQUE',
                        'provincia' => 'CHICLAYO',
                        'distrito' => 'CHICLAYO',
                        'urbanizacion' => '-',
                        'direccion' => $sale->branch->company->direccion,
                        'codLocal' => '0000'
                    ]
                ],
                'client' => [
                    'tipoDoc' => strtolower($sale->tipo_comprobante) === 'factura' ? '6' : '1',
                    'numDoc' => (int)$sale->client->nro_documento,
                    'rznSocial' => $sale->client->nombre_completo
                ],
                'details' => $details
            ];

            return response()->json([
                'success' => true,
                'json' => $json
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al generar JSON: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar nota de crédito a SUNAT
     */
    public function enviarNotaASunat(Sale $sale)
    {
        if (!auth()->user()->can('anular-ventas')) {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permisos para anular ventas.'
            ], 403);
        }

        // Validar que el empleado solo pueda anular ventas de su sucursal
        $user = auth()->user();
        if ($user->sucursal_id !== null && $sale->sucursal_id != $user->sucursal_id) {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permisos para anular ventas de esta sucursal.'
            ], 403);
        }

        try {
            // Generar JSON de nota de crédito
            $jsonResponse = $this->jsonNota($sale);
            $jsonData = $jsonResponse->getData();
            
            if (!$jsonData->success) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al generar JSON: ' . $jsonData->error
                ], 500);
            }

            $sunatJson = $jsonData->json;
            
            // Obtener token JWT de la sesión
            $jwtToken = session('jwt_token');
            
            // Enviar a SUNAT
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jwtToken
            ])->post('http://greenter.test/api/notes/send', $sunatJson);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Verificar si SUNAT respondió con éxito
                if (isset($data['sunatResponse']['success']) && $data['sunatResponse']['success']) {
                    // Guardar respuesta de SUNAT
                    $this->guardarRespuestaNotaSunat($sale, $data);
                    
                    // Actualizar correlativo de la serie
                    $this->actualizarCorrelativoNota($sale, $data);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Nota de crédito enviada exitosamente a SUNAT',
                        'data' => $data
                    ]);
                } else {
                    // SUNAT respondió pero con error
                    $errorMessage = $data['sunatResponse']['cdrResponse']['description'] ?? 'Error desconocido en SUNAT';
                    return response()->json([
                        'success' => false,
                        'error' => $errorMessage
                    ]);
                }
            } else {
                // Error en la comunicación con SUNAT
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Error de comunicación con SUNAT';
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Excepción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar respuesta de SUNAT para nota de crédito
     */
    private function guardarRespuestaNotaSunat($sale, $sunatData)
    {
        try {
            // Extraer número de documento del XML
            $numeroDocumento = $this->extractDocumentNumberFromXml($sunatData['xml']);
            
            // Guardar archivos XML y CDR
            $xmlPath = $this->saveXmlFile($sunatData['xml'], $sale->id, $numeroDocumento);
            $cdrPath = null;
            
            if (isset($sunatData['sunatResponse']['cdrZip'])) {
                $cdrPath = $this->saveCdrFile($sunatData['sunatResponse']['cdrZip'], $sale->id, $numeroDocumento);
            }
            
            // Guardar respuesta en BD
            \App\Models\SunatResponse::create([
                'venta_id' => $sale->id,
                'numero_documento' => $numeroDocumento,
                'tipo_documento' => '07', // Nota de crédito
                'xml_path' => $xmlPath,
                'cdr_path' => $cdrPath,
                'hash_documento' => $sunatData['hash'],
                'codigo_respuesta' => $sunatData['sunatResponse']['cdrResponse']['code'],
                'descripcion_respuesta' => $sunatData['sunatResponse']['cdrResponse']['description'],
                'exitoso' => $sunatData['sunatResponse']['success'],
                'respuesta_completa' => $sunatData,
            ]);
            
        } catch (\Exception $e) {
            // Guardar error en BD
            \App\Models\SunatResponse::create([
                'venta_id' => $sale->id,
                'numero_documento' => 'ERROR',
                'tipo_documento' => '07', // Nota de crédito
                'xml_path' => '',
                'cdr_path' => null,
                'hash_documento' => '',
                'codigo_respuesta' => 'EXCEPTION',
                'descripcion_respuesta' => 'Excepción al guardar respuesta SUNAT: ' . $e->getMessage(),
                'exitoso' => false,
                'respuesta_completa' => ['error' => $e->getMessage()],
            ]);
        }
    }

    /**
     * Actualizar correlativo de la serie de nota de crédito
     */
    private function actualizarCorrelativoNota($sale, $sunatData)
    {
        try {
            // Extraer número de documento del XML
            $numeroDocumento = $this->extractDocumentNumberFromXml($sunatData['xml']);
            
            // Extraer serie y correlativo
            if (preg_match('/^([A-Z]+\d+)-(\d+)$/', $numeroDocumento, $matches)) {
                $serie = $matches[1];
                $correlativo = (int)$matches[2];
                
                // Actualizar el último correlativo en document_series
                $documentSeries = \App\Models\DocumentSeries::where('sucursal_id', $sale->sucursal_id)
                    ->where('serie', $serie)
                    ->first();
                
                if ($documentSeries) {
                    $documentSeries->update([
                        'ultimo_correlativo' => $correlativo
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error al actualizar correlativo de nota: " . $e->getMessage());
        }
    }

    /**
     * Enviar nota de crédito a SUNAT automáticamente después de anular
     */
    private function enviarNotaASunatAutomatica($sale)
    {
        try {
            // Generar JSON de nota de crédito
            $jsonResponse = $this->jsonNota($sale);
            $jsonData = $jsonResponse->getData();
            
            if (!$jsonData->success) {
                throw new \Exception('Error al generar JSON: ' . $jsonData->error);
            }

            $sunatJson = $jsonData->json;
            
            // Obtener token JWT de la sesión
            $jwtToken = session('jwt_token');
            
            // Enviar a SUNAT
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $jwtToken
            ])->post('http://greenter.test/api/notes/send', $sunatJson);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Verificar si SUNAT respondió con éxito
                if (isset($data['sunatResponse']['success']) && $data['sunatResponse']['success']) {
                    // Guardar respuesta de SUNAT
                    $this->guardarRespuestaNotaSunat($sale, $data);
                    
                    // Actualizar correlativo de la serie
                    $this->actualizarCorrelativoNota($sale, $data);
                    
                    Log::info('Nota de crédito enviada exitosamente a SUNAT para venta ID: ' . $sale->id);
                } else {
                    // SUNAT respondió pero con error
                    $errorMessage = $data['sunatResponse']['cdrResponse']['description'] ?? 'Error desconocido en SUNAT';
                    Log::error('Error de SUNAT al enviar nota de crédito: ' . $errorMessage);
                    throw new \Exception($errorMessage);
                }
            } else {
                // Error en la comunicación con SUNAT
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Error de comunicación con SUNAT';
                Log::error('Error de comunicación con SUNAT: ' . $errorMessage);
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar nota de crédito a SUNAT automáticamente: ' . $e->getMessage());
            throw $e;
        }
    }
}
