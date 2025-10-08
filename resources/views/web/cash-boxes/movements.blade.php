@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Movimientos de Caja</h1>
            <div class="flex gap-2">
                @if($session->estado === 'abierta')
                    @can('crear-movimiento-caja')
                    <a href="{{ route('cash-boxes.create-movement', $session->id) }}" 
                       class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                       title="Nuevo movimiento">
                        Nuevo Movimiento
                    </a>
                    @endcan
                @endif
                <a href="{{ route('cash-boxes.session-details', $session->id) }}" 
                   class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors text-xs"
                   title="Volver">
                    Volver
                </a>
            </div>
        </div>

        <!-- Información de la Sesión -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Sesión</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Caja</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $session->cashBox->nombre }}</p>
                    <p class="text-sm text-gray-500">{{ $session->cashBox->branch->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sesión</label>
                    <p class="text-lg font-semibold text-gray-900">#{{ $session->id }}</p>
                    <p class="text-sm text-gray-500">Abierta por: {{ $session->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    @if($session->estado === 'abierta')
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Abierta
                        </span>
                    @elseif($session->estado === 'cerrada')
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                            Cerrada
                        </span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                            Cierre Temporal
                        </span>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saldo Actual</label>
                    <p class="text-2xl font-bold text-green-600">S/ {{ number_format($session->saldo_actual, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="bg-green-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-green-800 mb-4">Resumen Financiero</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Apertura</label>
                    <p class="text-xl font-semibold text-green-600">S/ {{ number_format($session->monto_apertura, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Ingresos</label>
                    <p class="text-xl font-semibold text-green-600">S/ {{ number_format($session->total_ingresos, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Salidas</label>
                    <p class="text-xl font-semibold text-red-600">S/ {{ number_format($session->total_salidas, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Actual</label>
                    <p class="text-2xl font-bold text-indigo-600">S/ {{ number_format($session->saldo_actual, 2) }}</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Lista de Movimientos -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Historial de Movimientos</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $movements->total() }} movimientos
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha/Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $movement->created_at->format('d/m/Y') }}</div>
                                    <div class="text-gray-500">{{ $movement->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->tipo === 'apertura')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                            Apertura
                                        </span>
                                    @elseif($movement->tipo === 'cierre')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                            Cierre
                                        </span>
                                    @elseif($movement->tipo === 'ingreso')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Ingreso
                                        </span>
                                    @elseif($movement->tipo === 'salida')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                            Salida
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($movement->metodo_pago)
                                        @switch($movement->metodo_pago)
                                            @case('efectivo')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                    Efectivo
                                                </span>
                                                @break
                                            @case('transferencia')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                    Transferencia
                                                </span>
                                                @break
                                            @case('billetera_virtual')
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                                    Billetera Virtual
                                                </span>
                                                @break
                                            @case('tarjeta')
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">
                                                    Tarjeta
                                                </span>
                                                @break
                                            @default
                                                <span class="text-gray-400">-</span>
                                        @endswitch
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="{{ $movement->descripcion }}">
                                        {{ $movement->descripcion }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($movement->tipo === 'ingreso' || $movement->tipo === 'apertura')
                                        <span class="text-green-600">+ S/ {{ number_format($movement->monto, 2) }}</span>
                                    @elseif($movement->tipo === 'salida' || $movement->tipo === 'cierre')
                                        <span class="text-red-600">- S/ {{ number_format($movement->monto, 2) }}</span>
                                    @else
                                        <span class="text-gray-600">S/ {{ number_format($movement->monto, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay movimientos</h3>
                                        <p class="mt-1 text-sm text-gray-500">Esta sesión aún no tiene movimientos registrados.</p>
                                        @if($session->estado === 'abierta')
                                            @can('crear-movimiento-caja')
                                            <div class="mt-6">
                                                <a href="{{ route('cash-boxes.create-movement', $session->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Crear Primer Movimiento
                                                </a>
                                            </div>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($movements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>

        <!-- Filtros y Estadísticas -->
        @if($movements->count() > 0)
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Estadísticas por Método de Pago -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4">Por Método de Pago</h4>
                    <div class="space-y-3">
                        @php
                            $statsByMethod = $movements->groupBy('metodo_pago')->map(function($group) {
                                return [
                                    'ingresos' => $group->where('tipo', 'ingreso')->sum('monto'),
                                    'salidas' => $group->where('tipo', 'salida')->sum('monto'),
                                    'total' => $group->where('tipo', 'ingreso')->sum('monto') - $group->where('tipo', 'salida')->sum('monto')
                                ];
                            });
                        @endphp
                        
                        @foreach($statsByMethod as $method => $stats)
                            @if($method)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        @switch($method)
                                            @case('efectivo')
                                                Efectivo
                                                @break
                                            @case('transferencia')
                                                Transferencia
                                                @break
                                            @case('billetera_virtual')
                                                Billetera Virtual
                                                @break
                                            @case('tarjeta')
                                                Tarjeta
                                                @break
                                            @default
                                                {{ ucfirst($method) }}
                                        @endswitch
                                    </span>
                                    <span class="text-sm font-semibold {{ $stats['total'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        S/ {{ number_format($stats['total'], 2) }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Estadísticas por Tipo -->
                <div class="bg-green-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-green-800 mb-4">Por Tipo</h4>
                    <div class="space-y-3">
                        @php
                            $statsByType = $movements->groupBy('tipo')->map(function($group) {
                                return $group->sum('monto');
                            });
                        @endphp
                        
                        @foreach($statsByType as $type => $total)
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">
                                    @switch($type)
                                        @case('apertura')
                                            Apertura
                                            @break
                                        @case('cierre')
                                            Cierre
                                            @break
                                        @case('ingreso')
                                            Ingresos
                                            @break
                                        @case('salida')
                                            Salidas
                                            @break
                                        @default
                                            {{ ucfirst($type) }}
                                    @endswitch
                                </span>
                                <span class="text-sm font-semibold {{ in_array($type, ['ingreso', 'apertura']) ? 'text-green-600' : 'text-red-600' }}">
                                    @if(in_array($type, ['ingreso', 'apertura']))
                                        + S/ {{ number_format($total, 2) }}
                                    @else
                                        - S/ {{ number_format($total, 2) }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Información</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Primer Movimiento</span>
                            <span class="text-sm text-gray-600">
                                {{ $movements->first()->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Último Movimiento</span>
                            <span class="text-sm text-gray-600">
                                {{ $movements->last()->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total Movimientos</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $movements->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
