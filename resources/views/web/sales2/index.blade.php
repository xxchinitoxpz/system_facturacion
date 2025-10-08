<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Venta 2</h1>
            @can('crear-ventas')
                <a href="{{ route('sales2.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Crear Venta
                </a>
            @endcan
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

        <!-- Buscador -->
        <div class="mb-6">
            <form method="GET" action="{{ route('sales2.index') }}">
                <div class="flex gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Buscar ventas..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('sales2.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de ventas -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprobante</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $sale->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $sale->fecha_venta->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $sale->client->nombre_completo }}</div>
                                    <div class="text-gray-500 text-xs">{{ $sale->client->tipo_documento }}: {{ $sale->client->nro_documento }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $sale->tipo_comprobante }}</div>
                                    @if($sale->serie && $sale->correlativo)
                                        <div class="text-gray-500 text-xs">{{ $sale->serie }}-{{ $sale->correlativo }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $sale->branch->nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold">
                                S/{{ number_format($sale->total, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if($sale->estado === 'completada')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Completada
                                    </span>
                                @elseif($sale->estado === 'anulada')
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        Anulada
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $sale->user->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">
                                <div class="flex gap-2">
                                    <a href="{{ route('sales2.show', $sale) }}" 
                                       class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                       title="Ver detalles">
                                        Ver
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <p class="text-xl font-semibold text-gray-600 mb-2">No se encontraron ventas</p>
                                    <p class="text-sm text-gray-500">Aún no hay ventas registradas en el sistema</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($sales->hasPages())
            <div class="mt-6">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
