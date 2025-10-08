<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CashBox;
use App\Models\CashBoxSession;
use App\Models\CashBoxMovement;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CashBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verificar permiso para ver cajas
        if (!auth()->user()->can('ver-cajas')) {
            abort(403, 'No tienes permisos para ver cajas.');
        }

        $user = auth()->user();
        
        // Filtrar cajas según el rol del usuario
        $query = CashBox::with(['branch', 'activeSession.movements', 'sessions']);
        
        // Si el usuario es empleado (tiene sucursal_id), solo mostrar cajas de su sucursal
        if ($user->sucursal_id !== null) {
            $query->where('sucursal_id', $user->sucursal_id);
        }
        // Si es administrador (sucursal_id = null), mostrar todas las cajas
        
        $cashBoxes = $query->orderBy('nombre')->get();

        // Calcular totales por método de pago y monto en caja para cada sesión activa
        foreach ($cashBoxes as $cashBox) {
            if ($cashBox->activeSession) {
                $totalesPorMetodo = $cashBox->activeSession->movements()
                    ->whereIn('tipo', ['ingreso', 'salida'])
                    ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
                    ->groupBy('metodo_pago', 'tipo')
                    ->get()
                    ->groupBy('metodo_pago');
                
                $cashBox->activeSession->totalesPorMetodo = $totalesPorMetodo;
                
                // Calcular monto en caja (efectivo)
                $montoApertura = $cashBox->activeSession->monto_apertura;
                $ingresosEfectivo = $cashBox->activeSession->movements()
                    ->where('tipo', 'ingreso')
                    ->where('metodo_pago', 'efectivo')
                    ->sum('monto');
                $salidasEfectivo = $cashBox->activeSession->movements()
                    ->where('tipo', 'salida')
                    ->where('metodo_pago', 'efectivo')
                    ->sum('monto');
                
                $cashBox->activeSession->montoEnCaja = $montoApertura + $ingresosEfectivo - $salidasEfectivo;
            }
        }

        // Obtener información sobre sucursales con y sin caja
        $totalBranches = Branch::count();
        $branchesWithCashBox = CashBox::count();
        $availableBranches = $totalBranches - $branchesWithCashBox;
        $canCreate = $availableBranches > 0;

        return view('web.cash-boxes.index', compact('cashBoxes', 'totalBranches', 'branchesWithCashBox', 'availableBranches', 'canCreate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar permiso para crear cajas
        if (!auth()->user()->can('crear-cajas')) {
            abort(403, 'No tienes permisos para crear cajas.');
        }

        $user = auth()->user();
        
        // Obtener sucursales que ya tienen caja
        $branchesWithCashBox = CashBox::pluck('sucursal_id')->toArray();
        
        // Filtrar sucursales según el rol del usuario y que no tengan caja
        $query = Branch::whereNotIn('id', $branchesWithCashBox)->orderBy('nombre');
        
        // Si el usuario es empleado, solo mostrar su sucursal (si no tiene caja)
        if ($user->sucursal_id !== null) {
            $query->where('id', $user->sucursal_id);
        }
        // Si es administrador, mostrar todas las sucursales que no tengan caja
        
        $availableBranches = $query->get();
        
        // Si no hay sucursales disponibles, redirigir con mensaje
        if ($availableBranches->isEmpty()) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'Todas las sucursales ya tienen una caja asignada. Solo se permite una caja por sucursal.');
        }
        
        return view('web.cash-boxes.create', compact('availableBranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar permiso para crear cajas
        if (!auth()->user()->can('crear-cajas')) {
            abort(403, 'No tienes permisos para crear cajas.');
        }

        $user = auth()->user();
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        // Si el usuario es empleado, verificar que solo pueda crear cajas en su sucursal
        if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes crear cajas en tu sucursal asignada.');
        }

        // Verificar que la sucursal no tenga ya una caja
        $existingCashBox = CashBox::where('sucursal_id', $request->sucursal_id)->first();
        if ($existingCashBox) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Esta sucursal ya tiene una caja asignada. Solo se permite una caja por sucursal.');
        }

        CashBox::create($request->all());

        return redirect()->route('cash-boxes.index')
            ->with('success', 'Caja creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CashBox $cashBox)
    {
        // Verificar permiso para ver cajas
        if (!auth()->user()->can('ver-cajas')) {
            abort(403, 'No tienes permisos para ver cajas.');
        }

        $cashBox->load(['branch', 'sessions' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('web.cash-boxes.show', compact('cashBox'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashBox $cashBox)
    {
        // Verificar permiso para editar cajas
        if (!auth()->user()->can('editar-cajas')) {
            abort(403, 'No tienes permisos para editar cajas.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda editar cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes editar cajas de tu sucursal asignada.');
        }
        
        // Filtrar sucursales según el rol del usuario
        $query = Branch::orderBy('nombre');
        
        // Si el usuario es empleado, solo mostrar su sucursal
        if ($user->sucursal_id !== null) {
            $query->where('id', $user->sucursal_id);
        }
        // Si es administrador, mostrar todas las sucursales
        
        $branches = $query->get();
        
        return view('web.cash-boxes.edit', compact('cashBox', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashBox $cashBox)
    {
        // Verificar permiso para editar cajas
        if (!auth()->user()->can('editar-cajas')) {
            abort(403, 'No tienes permisos para editar cajas.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda editar cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes editar cajas de tu sucursal asignada.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:branches,id',
        ]);

        // Si el usuario es empleado, verificar que no cambie la sucursal
        if ($user->sucursal_id !== null && $request->sucursal_id != $user->sucursal_id) {
            abort(403, 'No puedes cambiar la sucursal de la caja.');
        }

        // Verificar que la nueva sucursal no tenga ya una caja (si se está cambiando)
        if ($request->sucursal_id != $cashBox->sucursal_id) {
            $existingCashBox = CashBox::where('sucursal_id', $request->sucursal_id)->first();
            if ($existingCashBox) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'La sucursal seleccionada ya tiene una caja asignada. Solo se permite una caja por sucursal.');
            }
        }

        $cashBox->update($request->all());

        return redirect()->route('cash-boxes.index')
            ->with('success', 'Caja actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashBox $cashBox)
    {
        // Verificar permiso para eliminar cajas
        if (!auth()->user()->can('eliminar-cajas')) {
            abort(403, 'No tienes permisos para eliminar cajas.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda eliminar cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes eliminar cajas de tu sucursal asignada.');
        }

        // Verificar que no tenga sesiones activas
        if ($cashBox->sessions()->where('estado', 'abierta')->exists()) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'No se puede eliminar una caja con sesiones activas.');
        }

        $cashBox->delete();

        return redirect()->route('cash-boxes.index')
            ->with('success', 'Caja eliminada exitosamente.');
    }

    /**
     * Mostrar sesiones de una caja
     */
    public function sessions(CashBox $cashBox)
    {
        // Verificar permiso para ver sesiones de caja
        if (!auth()->user()->can('ver-sesiones-caja')) {
            abort(403, 'No tienes permisos para ver sesiones de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda ver sesiones de cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes ver sesiones de cajas de tu sucursal asignada.');
        }

        $sessions = $cashBox->sessions()
            ->with(['user', 'branch'])
            ->orderBy('fecha_hora_apertura', 'desc')
            ->paginate(15);

        return view('web.cash-boxes.sessions', compact('cashBox', 'sessions'));
    }

    /**
     * Mostrar detalles de una sesión
     */
    public function sessionDetails(CashBoxSession $session)
    {
        // Verificar permiso para ver sesiones de caja
        if (!auth()->user()->can('ver-sesiones-caja')) {
            abort(403, 'No tienes permisos para ver sesiones de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda ver sesiones de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes ver sesiones de cajas de tu sucursal asignada.');
        }

        $session->load(['cashBox', 'user', 'branch', 'movements' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        // Calcular totales por método de pago
        $totalesPorMetodo = $session->movements()
            ->whereIn('tipo', ['ingreso', 'salida'])
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
            ->groupBy('metodo_pago', 'tipo')
            ->get()
            ->groupBy('metodo_pago');
        
        // Calcular monto en caja (efectivo)
        $montoApertura = $session->monto_apertura;
        $ingresosEfectivo = $session->movements()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        $salidasEfectivo = $session->movements()
            ->where('tipo', 'salida')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        
        $montoEnCaja = $montoApertura + $ingresosEfectivo - $salidasEfectivo;
        
        return view('web.cash-boxes.session-details', compact('session', 'totalesPorMetodo', 'montoEnCaja'));
    }

    /**
     * Mostrar formulario para abrir sesión
     */
    public function openSession(CashBox $cashBox)
    {
        // Verificar permiso para abrir caja
        if (!auth()->user()->can('abrir-caja')) {
            abort(403, 'No tienes permisos para abrir caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda abrir cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes abrir cajas de tu sucursal asignada.');
        }

        // Verificar que no haya sesión activa
        if ($cashBox->activeSession) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'Esta caja ya tiene una sesión activa.');
        }

        // Obtener la última sesión cerrada para sugerir el monto de apertura
        $ultimaSesion = $cashBox->sessions()
            ->where('estado', 'cerrada')
            ->orderBy('fecha_hora_cierre', 'desc')
            ->first();

        $montoSugerido = 0;
        $esPrimeraVez = !$ultimaSesion;
        
        if ($ultimaSesion) {
            // Calcular el monto sugerido basado solo en efectivo
            // Monto de apertura + movimientos de efectivo (ingresos - salidas)
            $montoApertura = $ultimaSesion->monto_apertura;
            
            // Obtener movimientos de efectivo de la sesión anterior
            $ingresosEfectivo = $ultimaSesion->movements()
                ->where('tipo', 'ingreso')
                ->where('metodo_pago', 'efectivo')
                ->sum('monto');
                
            $salidasEfectivo = $ultimaSesion->movements()
                ->where('tipo', 'salida')
                ->where('metodo_pago', 'efectivo')
                ->sum('monto');
            
            // Calcular el efectivo disponible: apertura + ingresos - salidas
            $montoSugerido = $montoApertura + $ingresosEfectivo - $salidasEfectivo;
        }

        return view('web.cash-boxes.open-session', compact('cashBox', 'montoSugerido', 'esPrimeraVez', 'ultimaSesion'));
    }

    /**
     * Guardar nueva sesión
     */
    public function storeSession(Request $request, CashBox $cashBox)
    {
        // Verificar permiso para abrir caja
        if (!auth()->user()->can('abrir-caja')) {
            abort(403, 'No tienes permisos para abrir caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda crear sesiones en cajas de su sucursal
        if ($user->sucursal_id !== null && $cashBox->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes abrir cajas de tu sucursal asignada.');
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $session = CashBoxSession::create([
                'monto_apertura' => $request->monto_apertura,
                'fecha_hora_apertura' => now(),
                'estado' => 'abierta',
                'caja_id' => $cashBox->id,
                'sucursal_id' => $cashBox->sucursal_id,
                'usuario_id' => Auth::id(),
            ]);

            // Crear movimiento de apertura
            CashBoxMovement::create([
                'tipo' => 'apertura',
                'monto' => $request->monto_apertura,
                'descripcion' => 'Apertura de caja',
                'sesion_caja_id' => $session->id,
            ]);

            DB::commit();

            return redirect()->route('cash-boxes.session-details', $session)
                ->with('success', 'Sesión abierta exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error al abrir la sesión: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para cerrar sesión
     */
    public function closeSession(CashBoxSession $session)
    {
        // Verificar permiso para cerrar caja
        if (!auth()->user()->can('cerrar-caja')) {
            abort(403, 'No tienes permisos para cerrar caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda cerrar sesiones de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes cerrar sesiones de cajas de tu sucursal asignada.');
        }

        if ($session->estado !== 'abierta') {
            return redirect()->route('cash-boxes.session-details', $session)
                ->with('error', 'Esta sesión ya está cerrada.');
        }

        // Cargar movimientos y calcular totales por método de pago
        $session->load('movements');
        
        $totalesPorMetodo = $session->movements()
            ->whereIn('tipo', ['ingreso', 'salida'])
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
            ->groupBy('metodo_pago', 'tipo')
            ->get()
            ->groupBy('metodo_pago');

        // Calcular monto de cierre basado solo en efectivo
        $montoApertura = $session->monto_apertura;
        $ingresosEfectivo = $session->movements()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        $salidasEfectivo = $session->movements()
            ->where('tipo', 'salida')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        
        $montoCierreEfectivo = $montoApertura + $ingresosEfectivo - $salidasEfectivo;

        return view('web.cash-boxes.close-session', compact('session', 'totalesPorMetodo', 'montoCierreEfectivo'));
    }

    /**
     * Actualizar sesión (cerrar)
     */
    public function updateSession(Request $request, CashBoxSession $session)
    {
        // Verificar permiso para cerrar caja
        if (!auth()->user()->can('cerrar-caja')) {
            abort(403, 'No tienes permisos para cerrar caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda cerrar sesiones de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes cerrar sesiones de cajas de tu sucursal asignada.');
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'descuadre_descripcion' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Guardar la fecha de apertura original antes de actualizar
            $fechaAperturaOriginal = $session->fecha_hora_apertura;
            
            $session->update([
                'monto_cierre' => $request->monto_cierre,
                'fecha_hora_cierre' => now(),
                'estado' => 'cerrada',
            ]);
            
            // Verificar si la fecha de apertura se cambió y restaurarla si es necesario
            $session->refresh();
            if ($session->fecha_hora_apertura != $fechaAperturaOriginal) {
                $session->update(['fecha_hora_apertura' => $fechaAperturaOriginal]);
            }

            // Calcular totales por método de pago para el JSON
            $totalesPorMetodo = $session->movements()
                ->whereIn('tipo', ['ingreso', 'salida'])
                ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
                ->groupBy('metodo_pago', 'tipo')
                ->get()
                ->groupBy('metodo_pago');

            // Obtener montos por método
            $montoEfectivo = 0;
            $montoTarjeta = 0;
            $montoTransferencia = 0;
            $montoBilleteraVirtual = 0;

            if ($totalesPorMetodo->has('efectivo')) {
                $ingresos = $totalesPorMetodo['efectivo']->where('tipo', 'ingreso')->first()->total ?? 0;
                $salidas = $totalesPorMetodo['efectivo']->where('tipo', 'salida')->first()->total ?? 0;
                $montoEfectivo = $ingresos - $salidas;
            }
            if ($totalesPorMetodo->has('tarjeta')) {
                $ingresos = $totalesPorMetodo['tarjeta']->where('tipo', 'ingreso')->first()->total ?? 0;
                $salidas = $totalesPorMetodo['tarjeta']->where('tipo', 'salida')->first()->total ?? 0;
                $montoTarjeta = $ingresos - $salidas;
            }
            if ($totalesPorMetodo->has('transferencia')) {
                $ingresos = $totalesPorMetodo['transferencia']->where('tipo', 'ingreso')->first()->total ?? 0;
                $salidas = $totalesPorMetodo['transferencia']->where('tipo', 'salida')->first()->total ?? 0;
                $montoTransferencia = $ingresos - $salidas;
            }
            if ($totalesPorMetodo->has('billetera_virtual')) {
                $ingresos = $totalesPorMetodo['billetera_virtual']->where('tipo', 'ingreso')->first()->total ?? 0;
                $salidas = $totalesPorMetodo['billetera_virtual']->where('tipo', 'salida')->first()->total ?? 0;
                $montoBilleteraVirtual = $ingresos - $salidas;
            }

            // Crear JSON con todos los montos
            $jsonCierre = [
                'monto_apertura' => $session->monto_apertura,
                'monto_efectivo' => $montoEfectivo,
                'monto_tarjeta' => $montoTarjeta,
                'monto_transferencia' => $montoTransferencia,
                'monto_billetera_virtual' => $montoBilleteraVirtual,
                'descuadre' => $request->descuadre_descripcion ?: 'ninguno'
            ];

            // Crear movimiento de cierre
            CashBoxMovement::create([
                'tipo' => 'cierre',
                'monto' => $request->monto_cierre,
                'descripcion' => json_encode($jsonCierre),
                'sesion_caja_id' => $session->id,
            ]);

            DB::commit();

            return redirect()->route('cash-boxes.session-details', $session)
                ->with('success', 'Sesión cerrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error al cerrar la sesión: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar movimientos de una sesión
     */
    public function movements(CashBoxSession $session)
    {
        // Verificar permiso para ver movimientos de caja
        if (!auth()->user()->can('ver-movimientos-caja')) {
            abort(403, 'No tienes permisos para ver movimientos de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda ver movimientos de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes ver movimientos de cajas de tu sucursal asignada.');
        }

        $movements = $session->movements()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('web.cash-boxes.movements', compact('session', 'movements'));
    }

    /**
     * Mostrar formulario para crear movimiento
     */
    public function createMovement(CashBoxSession $session)
    {
        // Verificar permiso para crear movimientos de caja
        if (!auth()->user()->can('crear-movimientos-caja')) {
            abort(403, 'No tienes permisos para crear movimientos de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda crear movimientos en cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes crear movimientos en cajas de tu sucursal asignada.');
        }

        if ($session->estado !== 'abierta') {
            return redirect()->route('cash-boxes.session-details', $session)
                ->with('error', 'No se pueden agregar movimientos a una sesión cerrada.');
        }

        // Cargar movimientos y calcular totales por método de pago
        $session->load('movements');
        
        $totalesPorMetodo = $session->movements()
            ->whereIn('tipo', ['ingreso', 'salida'])
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
            ->groupBy('metodo_pago', 'tipo')
            ->get()
            ->groupBy('metodo_pago');

        // Calcular monto en caja (efectivo)
        $montoApertura = $session->monto_apertura;
        $ingresosEfectivo = $session->movements()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        $salidasEfectivo = $session->movements()
            ->where('tipo', 'salida')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        
        $montoEnCaja = $montoApertura + $ingresosEfectivo - $salidasEfectivo;

        return view('web.cash-boxes.create-movement', compact('session', 'totalesPorMetodo', 'montoEnCaja'));
    }

    /**
     * Guardar nuevo movimiento
     */
    public function storeMovement(Request $request, CashBoxSession $session)
    {
        // Verificar permiso para crear movimientos de caja
        if (!auth()->user()->can('crear-movimientos-caja')) {
            abort(403, 'No tienes permisos para crear movimientos de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda crear movimientos en cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes crear movimientos en cajas de tu sucursal asignada.');
        }

        $request->validate([
            'tipo' => 'required|in:ingreso,salida',
            'metodo_pago' => 'nullable|in:efectivo,transferencia,billetera_virtual,tarjeta',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:500',
        ]);

        if ($session->estado !== 'abierta') {
            return redirect()->back()
                ->with('error', 'No se pueden agregar movimientos a una sesión cerrada.');
        }

        CashBoxMovement::create([
            'tipo' => $request->tipo,
            'metodo_pago' => $request->metodo_pago,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'sesion_caja_id' => $session->id,
        ]);

        return redirect()->route('cash-boxes.session-details', $session)
            ->with('success', 'Movimiento registrado exitosamente.');
    }

    /**
     * Mostrar cuadre de caja
     */
    public function balance(CashBoxSession $session)
    {
        // Verificar permiso para ver cuadre de caja
        if (!auth()->user()->can('ver-cuadre-caja')) {
            abort(403, 'No tienes permisos para ver cuadre de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda ver cuadres de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes ver cuadres de cajas de tu sucursal asignada.');
        }

        $session->load(['movements', 'cashBox', 'user', 'branch']);
        
        // Calcular totales por método de pago
        $totalsByMethod = $session->movements()
            ->whereIn('tipo', ['ingreso', 'salida'])
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
            ->groupBy('metodo_pago', 'tipo')
            ->get()
            ->groupBy('metodo_pago');

        return view('web.cash-boxes.balance', compact('session', 'totalsByMethod'));
    }

    /**
     * Generar PDF de sesión de caja
     */
    public function sessionPdf(CashBoxSession $session)
    {
        // Verificar permiso para ver sesiones de caja
        if (!auth()->user()->can('ver-sesiones-caja')) {
            abort(403, 'No tienes permisos para ver sesiones de caja.');
        }

        $user = auth()->user();
        
        // Si el usuario es empleado, verificar que solo pueda ver sesiones de cajas de su sucursal
        if ($user->sucursal_id !== null && $session->sucursal_id != $user->sucursal_id) {
            abort(403, 'Solo puedes ver sesiones de cajas de tu sucursal asignada.');
        }

        $session->load(['cashBox', 'user', 'branch', 'movements' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        // Calcular totales por método de pago
        $totalesPorMetodo = $session->movements()
            ->whereIn('tipo', ['ingreso', 'salida'])
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total')
            ->groupBy('metodo_pago', 'tipo')
            ->get()
            ->groupBy('metodo_pago');
        
        // Calcular monto en caja (efectivo)
        $montoApertura = $session->monto_apertura;
        $ingresosEfectivo = $session->movements()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        $salidasEfectivo = $session->movements()
            ->where('tipo', 'salida')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');
        
        $montoEnCaja = $montoApertura + $ingresosEfectivo - $salidasEfectivo;

        $pdf = Pdf::loadView('web.cash-boxes.session-pdf', compact('session', 'totalesPorMetodo', 'montoEnCaja'));
        
        $filename = 'Sesion_Caja_' . $session->cashBox->nombre . '_' . $session->fecha_hora_apertura->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
