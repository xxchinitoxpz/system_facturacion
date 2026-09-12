<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-reportes')) {
            abort(403, 'No tienes permisos para ver reportes.');
        }

        $user = auth()->user();
        $preset = $request->input('preset', 'this_year');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        [$inicio, $fin, $preset] = $this->resolvePeriod($preset, $fechaInicio, $fechaFin);

        $sucursalId = $request->input('sucursal_id');
        if ($user->sucursal_id !== null) {
            $sucursalId = (string) $user->sucursal_id;
        }

        $baseQuery = Sale::query()
            ->where('estado', 'completada')
            ->whereBetween('fecha_venta', [$inicio->copy()->startOfDay(), $fin->copy()->endOfDay()])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId));

        $totalVendido = (float) (clone $baseQuery)->sum('total');
        $cantidadVentas = (int) (clone $baseQuery)->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalVendido / $cantidadVentas : 0;

        [$prevInicio, $prevFin] = $this->previousPeriod($inicio, $fin);
        $prevTotal = (float) Sale::query()
            ->where('estado', 'completada')
            ->whereBetween('fecha_venta', [$prevInicio->copy()->startOfDay(), $prevFin->copy()->endOfDay()])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->sum('total');

        $variacionPorcentaje = null;
        if ($prevTotal > 0) {
            $variacionPorcentaje = (($totalVendido - $prevTotal) / $prevTotal) * 100;
        } elseif ($totalVendido > 0) {
            $variacionPorcentaje = 100.0;
        } else {
            $variacionPorcentaje = 0.0;
        }

        $porMes = (clone $baseQuery)
            ->selectRaw('YEAR(fecha_venta) as anio, MONTH(fecha_venta) as mes, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get()
            ->map(function ($row) {
                $total = (float) $row->total;
                $cantidad = (int) $row->cantidad;
                return [
                    'anio' => (int) $row->anio,
                    'mes' => (int) $row->mes,
                    'etiqueta' => Carbon::create((int) $row->anio, (int) $row->mes, 1)->translatedFormat('M Y'),
                    'total' => $total,
                    'cantidad' => $cantidad,
                    'ticket_promedio' => $cantidad > 0 ? $total / $cantidad : 0,
                ];
            });

        $porAnio = Sale::query()
            ->where('estado', 'completada')
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->selectRaw('YEAR(fecha_venta) as anio, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('anio')
            ->orderBy('anio')
            ->get()
            ->map(fn ($row) => [
                'anio' => (int) $row->anio,
                'total' => (float) $row->total,
                'cantidad' => (int) $row->cantidad,
            ]);

        $porComprobante = (clone $baseQuery)
            ->selectRaw('tipo_comprobante, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('tipo_comprobante')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'tipo' => $row->tipo_comprobante ?: 'sin_tipo',
                'etiqueta' => ucfirst($row->tipo_comprobante ?: 'Sin tipo'),
                'total' => (float) $row->total,
                'cantidad' => (int) $row->cantidad,
            ]);

        $porSucursal = (clone $baseQuery)
            ->leftJoin('branches', 'sales.sucursal_id', '=', 'branches.id')
            ->select([
                'sales.sucursal_id',
                DB::raw('COALESCE(branches.nombre, "Sin sucursal") as sucursal'),
                DB::raw('SUM(sales.total) as total'),
                DB::raw('COUNT(*) as cantidad'),
            ])
            ->groupBy('sales.sucursal_id', 'branches.nombre')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $total = (float) $row->total;
                $cantidad = (int) $row->cantidad;
                return [
                    'sucursal' => $row->sucursal,
                    'total' => $total,
                    'cantidad' => $cantidad,
                    'ticket_promedio' => $cantidad > 0 ? $total / $cantidad : 0,
                ];
            });

        $sucursales = $user->sucursal_id === null
            ? Branch::orderBy('nombre')->get(['id', 'nombre'])
            : Branch::where('id', $user->sucursal_id)->get(['id', 'nombre']);

        return view('web.reports.sales', [
            'preset' => $preset,
            'fechaInicio' => $inicio->toDateString(),
            'fechaFin' => $fin->toDateString(),
            'sucursalId' => $sucursalId,
            'sucursales' => $sucursales,
            'totalVendido' => $totalVendido,
            'cantidadVentas' => $cantidadVentas,
            'ticketPromedio' => $ticketPromedio,
            'variacionPorcentaje' => $variacionPorcentaje,
            'prevTotal' => $prevTotal,
            'prevInicio' => $prevInicio->toDateString(),
            'prevFin' => $prevFin->toDateString(),
            'porMes' => $porMes,
            'porAnio' => $porAnio,
            'porComprobante' => $porComprobante,
            'porSucursal' => $porSucursal,
            'chartMensual' => [
                'labels' => $porMes->pluck('etiqueta'),
                'totales' => $porMes->pluck('total'),
                'cantidades' => $porMes->pluck('cantidad'),
            ],
            'chartAnual' => [
                'labels' => $porAnio->pluck('anio'),
                'totales' => $porAnio->pluck('total'),
            ],
            'chartComprobante' => [
                'labels' => $porComprobante->pluck('etiqueta'),
                'totales' => $porComprobante->pluck('total'),
            ],
            'chartSucursal' => [
                'labels' => $porSucursal->pluck('sucursal'),
                'totales' => $porSucursal->pluck('total'),
            ],
        ]);
    }

    private function resolvePeriod(string $preset, ?string $fechaInicio, ?string $fechaFin): array
    {
        $hoy = Carbon::today();

        if ($preset === 'custom' && $fechaInicio && $fechaFin) {
            $inicio = Carbon::parse($fechaInicio)->startOfDay();
            $fin = Carbon::parse($fechaFin)->endOfDay();
            if ($inicio->gt($fin)) {
                [$inicio, $fin] = [$fin->copy()->startOfDay(), $inicio->copy()->endOfDay()];
            }
            return [$inicio, $fin, 'custom'];
        }

        return match ($preset) {
            'last_year' => [
                $hoy->copy()->subYear()->startOfYear(),
                $hoy->copy()->subYear()->endOfYear(),
                'last_year',
            ],
            'last_12_months' => [
                $hoy->copy()->subMonths(11)->startOfMonth(),
                $hoy->copy()->endOfDay(),
                'last_12_months',
            ],
            default => [
                $hoy->copy()->startOfYear(),
                $hoy->copy()->endOfDay(),
                'this_year',
            ],
        };
    }

    private function previousPeriod(Carbon $inicio, Carbon $fin): array
    {
        $days = $inicio->diffInDays($fin) + 1;
        $prevFin = $inicio->copy()->subDay()->endOfDay();
        $prevInicio = $prevFin->copy()->subDays($days - 1)->startOfDay();

        return [$prevInicio, $prevFin];
    }
}
