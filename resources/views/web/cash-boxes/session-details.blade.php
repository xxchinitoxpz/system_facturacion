<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de Sesión</h1>
            <div class="flex gap-2">
                @if($session->estado === 'abierta')
                    @can('cerrar-caja')
                    <a href="{{ route('cash-boxes.close-session', $session->id) }}" 
                       class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Cerrar Sesión
                    </a>
                    @endcan
                    @can('crear-movimientos-caja')
                    <a href="{{ route('cash-boxes.create-movement', $session->id) }}" 
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Nuevo Movimiento
                    </a>
                    @endcan
                @endif
                <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
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

        <!-- Información de la Sesión -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Información Principal -->
            <div class="lg:col-span-2">
                <div class="bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Sesión</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Caja</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $session->cashBox->nombre }}</p>
                            <p class="text-sm text-gray-500">{{ $session->cashBox->branch->nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            @if($session->estado === 'abierta')
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                    Abierta
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                    Cerrada
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Abierta por</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $session->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $session->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Apertura</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $session->fecha_hora_apertura->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $session->fecha_hora_apertura->format('H:i:s') }}</p>
                        </div>
                    </div>
                    @if($session->fecha_hora_cierre)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Cierre</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $session->fecha_hora_cierre->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $session->fecha_hora_cierre->format('H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto de Cierre</label>
                                    <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($session->monto_cierre, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div class="lg:col-span-1">
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">Resumen Financiero</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monto de Apertura</label>
                            <p class="text-2xl font-bold text-green-600">S/ {{ number_format($session->monto_apertura, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Ingresos</label>
                            <p class="text-xl font-semibold text-green-600">S/ {{ number_format($session->total_ingresos, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Salidas</label>
                            <p class="text-xl font-semibold text-red-600">S/ {{ number_format($session->total_salidas, 2) }}</p>
                        </div>
                        <!-- Total General y Total en Caja -->
                        <div class="pt-4 border-t border-green-200">
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Total General -->
                                <div class="text-center p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <span class="block text-sm font-medium text-gray-700 mb-1">Total General:</span>
                                    <span class="text-lg font-bold text-indigo-600">S/ {{ number_format($session->saldo_actual, 2) }}</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Todos los métodos
                                    </p>
                                </div>
                                
                                <!-- Total en Caja (Efectivo) -->
                                <div class="text-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <span class="block text-sm font-medium text-gray-700 mb-1">Total en Caja:</span>
                                    <span class="text-lg font-bold text-green-600">S/ {{ number_format($montoEnCaja, 2) }}</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Solo efectivo
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Totales por Método de Pago -->
                        @if($totalesPorMetodo->count() > 0)
                            <div class="pt-4 border-t border-green-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Totales por Método de Pago</label>
                                <div class="space-y-2">
                                    @foreach(['efectivo', 'tarjeta', 'transferencia', 'billetera_virtual'] as $metodo)
                                        @if($totalesPorMetodo->has($metodo))
                                            @php
                                                $ingresos = $totalesPorMetodo[$metodo]->where('tipo', 'ingreso')->first()->total ?? 0;
                                                $salidas = $totalesPorMetodo[$metodo]->where('tipo', 'salida')->first()->total ?? 0;
                                                $neto = $ingresos - $salidas;
                                            @endphp
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="font-medium text-gray-700">
                                                    @switch($metodo)
                                                        @case('efectivo')
                                                            Efectivo
                                                            @break
                                                        @case('tarjeta')
                                                            Tarjeta
                                                            @break
                                                        @case('transferencia')
                                                            Transferencia
                                                            @break
                                                        @case('billetera_virtual')
                                                            Billetera Virtual
                                                            @break
                                                    @endswitch
                                                </span>
                                                <span class="font-semibold {{ $neto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    S/ {{ number_format($neto, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Movimientos -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Movimientos</h3>
                <div class="flex gap-2">
                    @if($session->estado === 'abierta')
                        @can('crear-movimientos-caja')
                        <a href="{{ route('cash-boxes.create-movement', $session->id) }}" 
                           class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                           title="Nuevo movimiento">
                            Nuevo Movimiento
                        </a>
                        @endcan
                    @endif
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método de Pago
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
                        @forelse($session->movements as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->tipo === 'ingreso')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Ingreso
                                        </span>
                                    @elseif($movement->tipo === 'salida')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                            Salida
                                        </span>
                                    @elseif($movement->tipo === 'apertura')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                            Apertura
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                            Cierre
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($movement->metodo_pago)
                                        {{ ucfirst($movement->metodo_pago) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $movement->descripcion }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(in_array($movement->tipo, ['ingreso', 'apertura']))
                                        <span class="text-green-600">+ S/ {{ number_format($movement->monto, 2) }}</span>
                                    @else
                                        <span class="text-red-600">- S/ {{ number_format($movement->monto, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay movimientos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
