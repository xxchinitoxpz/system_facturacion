<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\CashBox;
use App\Models\CashBoxSession;
use App\Models\CashBoxMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $hoy = Carbon::today();

        // Obtener la sucursal seleccionada
        $sucursalSeleccionada = $request->get('sucursal_id');
        
        // Si el usuario es empleado, solo puede ver su sucursal
        if ($user->sucursal_id !== null) {
            $sucursalSeleccionada = $user->sucursal_id;
        }
        
        // Si no hay sucursal seleccionada y el usuario es admin, usar la primera sucursal
        if (!$sucursalSeleccionada && $user->sucursal_id === null) {
            $sucursalSeleccionada = \App\Models\Branch::first()->id ?? null;
        }

        // Obtener la sesión activa de la caja de la sucursal seleccionada
        $sesionActiva = null;
        if ($sucursalSeleccionada) {
            $caja = CashBox::where('sucursal_id', $sucursalSeleccionada)->first();
            if ($caja) {
                $sesionActiva = $caja->activeSession;
            }
        }

        // Ventas de la sesión activa (a través de movimientos de caja)
        $totalVentasSesion = 0;
        $cantidadVentasSesion = 0;
        
        if ($sesionActiva) {
            // Obtener ventas de la sesión activa a través de movimientos de caja
            $movimientosVentas = $sesionActiva->movements()
                ->where('tipo', 'ingreso')
                ->whereNotNull('venta_id')
                ->with('sale')
                ->get();
            
            $totalVentasSesion = $movimientosVentas->sum('monto');
            $cantidadVentasSesion = $movimientosVentas->count();
        }


        // Productos con bajo stock (menos de 10 unidades)
        $productosBajoStock = DB::table('product_warehouse')
            ->join('products', 'product_warehouse.producto_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.almacen_id', '=', 'warehouses.id')
            ->select([
                'products.nombre as producto_nombre',
                'warehouses.nombre as almacen_nombre',
                'product_warehouse.stock',
                'product_warehouse.fecha_vencimiento'
            ])
            ->where('product_warehouse.stock', '<=', 10)
            ->when($sucursalSeleccionada, function ($query) use ($sucursalSeleccionada) {
                $query->where('warehouses.sucursal_id', $sucursalSeleccionada);
            })
            ->orderBy('product_warehouse.stock', 'asc')
            ->limit(5)
            ->get();

        $cantidadProductosBajoStock = $productosBajoStock->count();

        // Saldo de la sesión activa (general)
        $saldoSesionActiva = 0;
        if ($sesionActiva) {
            $saldoSesionActiva = $sesionActiva->saldo_actual;
        }

        // Saldo de efectivo de la sesión activa (apertura + movimientos de efectivo)
        $saldoEfectivoSesion = 0;
        if ($sesionActiva) {
            $montoApertura = $sesionActiva->monto_apertura;
            $ingresosEfectivo = $sesionActiva->movements()
                ->where('tipo', 'ingreso')
                ->where('metodo_pago', 'efectivo')
                ->sum('monto');
            $salidasEfectivo = $sesionActiva->movements()
                ->where('tipo', 'salida')
                ->where('metodo_pago', 'efectivo')
                ->sum('monto');
            
            $saldoEfectivoSesion = $montoApertura + $ingresosEfectivo - $salidasEfectivo;
        }

        // Ventas de los últimos 7 días para el gráfico
        $ventasUltimos7Dias = Sale::where('fecha_venta', '>=', Carbon::now()->subDays(7))
            ->where('estado', 'completada')
            ->when($sucursalSeleccionada, function ($query) use ($sucursalSeleccionada) {
                $query->where('sucursal_id', $sucursalSeleccionada);
            })
            ->selectRaw('DATE(fecha_venta) as fecha, SUM(total) as total_ventas, COUNT(*) as cantidad_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Últimas ventas
        $ultimasVentas = Sale::with(['client', 'branch'])
            ->where('estado', 'completada')
            ->when($sucursalSeleccionada, function ($query) use ($sucursalSeleccionada) {
                $query->where('sucursal_id', $sucursalSeleccionada);
            })
            ->orderBy('fecha_venta', 'desc')
            ->limit(5)
            ->get();

        // Obtener todas las sucursales para el selector
        $sucursales = \App\Models\Branch::orderBy('nombre')->get();

        return view('dashboard', compact(
            'totalVentasSesion',
            'cantidadVentasSesion',
            'productosBajoStock',
            'cantidadProductosBajoStock',
            'saldoSesionActiva',
            'saldoEfectivoSesion',
            'ventasUltimos7Dias',
            'ultimasVentas',
            'sesionActiva',
            'sucursales',
            'sucursalSeleccionada'
        ));
    }

    private function calcularSaldoCaja($cashBox)
    {
        if (!$cashBox->activeSession) {
            return 0;
        }

        return $cashBox->activeSession->saldo_actual;
    }
}
