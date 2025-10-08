@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Sesiones de Caja: {{ $cashBox->nombre }}</h1>
            <div class="flex gap-2">
                @if(!$cashBox->activeSession)
                    @can('abrir-caja')
                    <a href="{{ route('cash-boxes.open-session', $cashBox->id) }}" 
                       class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                       title="Abrir sesión">
                        Abrir Sesión
                    </a>
                    @endcan
                @endif
                <a href="{{ route('cash-boxes.index') }}" 
                   class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors text-xs"
                   title="Volver">
                    Volver
                </a>
            </div>
        </div>

        <!-- Información de la Caja -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Caja</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Caja</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cashBox->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cashBox->branch->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado Actual</label>
                    @if($cashBox->activeSession)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Abierta
                        </span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                            Cerrada
                        </span>
                    @endif
                </div>
            </div>
            @if($cashBox->descripcion)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <p class="text-gray-600">{{ $cashBox->descripcion }}</p>
                </div>
            @endif
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

        <!-- Lista de Sesiones -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Historial de Sesiones</h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $sessions->total() }} sesiones
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sesión
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Apertura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cierre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto Apertura
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto Cierre
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saldo
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $session->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $session->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $session->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $session->fecha_hora_apertura->format('d/m/Y') }}</div>
                                    <div class="text-gray-500">{{ $session->fecha_hora_apertura->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($session->fecha_hora_cierre)
                                        <div>{{ $session->fecha_hora_cierre->format('d/m/Y') }}</div>
                                        <div class="text-gray-500">{{ $session->fecha_hora_cierre->format('H:i:s') }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($session->estado === 'abierta')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Abierta
                                        </span>
                                    @elseif($session->estado === 'cerrada')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                            Cerrada
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                            Cierre Temporal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600">
                                    S/ {{ number_format($session->monto_apertura, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($session->monto_cierre)
                                        <span class="text-indigo-600">S/ {{ number_format($session->monto_cierre, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($session->estado === 'abierta')
                                        <span class="text-green-600 font-bold">S/ {{ number_format($session->saldo_actual, 2) }}</span>
                                    @else
                                        <span class="text-gray-600">S/ {{ number_format($session->saldo_actual, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('cash-boxes.session-details', $session->id) }}" 
                                           class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                           title="Ver detalles">
                                            Ver
                                        </a>
                                        <a href="{{ route('cash-boxes.session-pdf', $session->id) }}" 
                                           class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                           title="Descargar PDF" target="_blank">
                                            PDF
                                        </a>
                                        @if($session->estado === 'abierta')
                                            @can('cerrar-caja')
                                            <a href="{{ route('cash-boxes.close-session', $session->id) }}" 
                                               class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors text-xs"
                                               title="Cerrar sesión">
                                                Cerrar
                                            </a>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay sesiones</h3>
                                        <p class="mt-1 text-sm text-gray-500">Esta caja aún no tiene sesiones registradas.</p>
                                        @if(!$cashBox->activeSession)
                                            @can('abrir-caja')
                                            <div class="mt-6">
                                                                                                 <a href="{{ route('cash-boxes.open-session', $cashBox->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                     Abrir Primera Sesión
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
            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
