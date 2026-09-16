<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\Request;

class ComprobanteController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('ver-comprobantes')) {
            abort(403, 'No tienes permisos para ver comprobantes.');
        }

        try {
            $user = auth()->user();
            $search = $request->input('search');
            $estado = $request->input('estado');
            $tipo_comprobante = $request->input('tipo_comprobante');
            $cliente_id = $request->input('cliente_id');
            $sucursal_id = $request->input('sucursal_id');
            $fecha_inicio = $request->input('fecha_inicio');
            $fecha_fin = $request->input('fecha_fin');

            $sales = Sale::query()
                ->with(['client', 'branch', 'user', 'sunatResponses' => fn ($q) => $q->orderByDesc('id')])
                ->whereIn('tipo_comprobante', ['boleta', 'factura'])
                ->when($user->sucursal_id !== null, function ($query) use ($user) {
                    $query->where('sucursal_id', $user->sucursal_id);
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('serie', 'like', "%{$search}%")
                            ->orWhere('correlativo', 'like', "%{$search}%")
                            ->orWhere('tipo_comprobante', 'like', "%{$search}%")
                            ->orWhereHas('client', function ($clientQuery) use ($search) {
                                $clientQuery->where('nombre_completo', 'like', "%{$search}%")
                                    ->orWhere('nro_documento', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($estado, fn ($query) => $query->where('estado', $estado))
                ->when($tipo_comprobante, fn ($query) => $query->where('tipo_comprobante', $tipo_comprobante))
                ->when($cliente_id, fn ($query) => $query->where('cliente_id', $cliente_id))
                ->when($sucursal_id, fn ($query) => $query->where('sucursal_id', $sucursal_id))
                ->when($fecha_inicio && $fecha_fin, function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('fecha_venta', [$fecha_inicio, $fecha_fin]);
                })
                ->orderBy('fecha_venta', 'desc')
                ->paginate(10)
                ->withQueryString();

            $clients = Client::where('activo', true)->orderBy('nombre_completo')->get();
            $branches = Branch::orderBy('nombre')->get();
            $estados = ['completada', 'anulada', 'pendiente'];
            $tipos = ['boleta', 'factura'];

            return view('web.comprobantes.index', compact(
                'sales',
                'search',
                'clients',
                'branches',
                'estados',
                'tipos',
                'estado',
                'tipo_comprobante',
                'cliente_id',
                'sucursal_id',
                'fecha_inicio',
                'fecha_fin'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los comprobantes: ' . $e->getMessage());
        }
    }
}
