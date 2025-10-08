<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Ventas</h1>
            @can('crear-ventas')
                <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
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

        <!-- Barra de búsqueda y filtros -->
        <div class="mb-6">
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Campo de búsqueda -->
                    <div class="lg:col-span-2">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Buscar por serie, correlativo, tipo de comprobante o cliente..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Select de estado -->
                    <div>
                        <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $estadoOption)
                                <option value="{{ $estadoOption }}" {{ $estado == $estadoOption ? 'selected' : '' }}>
                                    {{ ucfirst($estadoOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Select de cliente -->
                    <div>
                        <select name="cliente_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los clientes</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $cliente_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    @if(auth()->user()->sucursal_id === null)
                        <!-- Select de sucursal solo para administradores -->
                        <div>
                            <select name="sucursal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas las sucursales</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $sucursal_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Para empleados, mostrar sucursal fija y campo oculto -->
                        <div>
                            <input 
                                type="text" 
                                value="{{ auth()->user()->branch->nombre ?? 'Sucursal no asignada' }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly
                                placeholder="Sucursal asignada"
                            >
                            <input type="hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id }}">
                        </div>
                    @endif
                    
                    <!-- Fecha inicio -->
                    <div>
                        <input 
                            type="date" 
                            name="fecha_inicio" 
                            value="{{ $fecha_inicio }}" 
                            placeholder="Fecha inicio"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Fecha fin -->
                    <div>
                        <input 
                            type="date" 
                            name="fecha_fin" 
                            value="{{ $fecha_fin }}" 
                            placeholder="Fecha fin"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Buscar
                    </button>
                    @if($search || $estado || $cliente_id || $sucursal_id || $fecha_inicio || $fecha_fin)
                        <a href="{{ route('sales.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
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
                                                 @if(auth()->user()->canAny(['ver-ventas', 'editar-ventas', 'anular-ventas']))
                             <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                         @endif
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
                                                         @if(auth()->user()->canAny(['ver-ventas', 'editar-ventas', 'anular-ventas']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-ventas')
                                            <a href="{{ route('sales.show', $sale) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-ventas')
                                            @if(strtolower($sale->tipo_comprobante) === 'ticket' && $sale->estado !== 'anulada')
                                                <a href="{{ route('sales.edit', $sale) }}" 
                                                   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                                   title="Editar venta">
                                                    Editar
                                                </a>
                                            @else
                                                <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs cursor-not-allowed"
                                                      title="{{ $sale->estado === 'anulada' ? 'No se pueden editar ventas anuladas' : 'Solo se pueden editar ventas de tipo ticket' }}">
                                                    Editar
                                                </span>
                                            @endif
                                        @endcan
                                        @can('anular-ventas')
                                            @if($sale->estado !== 'anulada')
                                                @php
                                                    $puedeAnular = true;
                                                    $mensajeAnular = 'Anular venta';
                                                    
                                                    // Validar tiempo límite para boletas y facturas
                                                    if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
                                                        $fechaVenta = $sale->fecha_venta->startOfDay();
                                                        $fechaActual = now()->startOfDay();
                                                        $diasTranscurridos = $fechaVenta->diffInDays($fechaActual);
                                                        if ($diasTranscurridos >= 2) {
                                                            $puedeAnular = false;
                                                            $mensajeAnular = 'No se pueden anular boletas y facturas después de 2 días';
                                                        }
                                                    }
                                                @endphp
                                                
                                                @if($puedeAnular)
                                                    <button 
                                                        onclick="confirmAnular(this)"
                                                        data-id="{{ $sale->id }}"
                                                        data-total="{{ $sale->total }}"
                                                        data-sale="{{ $sale->id }}"
                                                        class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors text-xs"
                                                        title="Anular venta y enviar nota de crédito a SUNAT">
                                                        Anular
                                                    </button>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs cursor-not-allowed"
                                                          title="{{ $mensajeAnular }}">
                                                        Anular
                                                    </span>
                                                @endif
                                            @endif
                                        @endcan
                                        
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                                                         <td colspan="{{ auth()->user()->canAny(['ver-ventas', 'editar-ventas', 'anular-ventas']) ? 9 : 8 }}" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <p class="text-xl font-semibold text-gray-600 mb-2">No se encontraron ventas</p>
                                    <p class="text-sm text-gray-500 mb-4">Intenta ajustar los filtros de búsqueda arriba</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Usa los filtros de fecha, sucursal y estado para encontrar ventas específicas</span>
                                    </div>
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

    

    <!-- Modal de confirmación para anular -->
    <div id="anularModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar anulación</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            ¿Estás seguro de que quieres anular la venta con total S/<span id="anularTotal" class="font-semibold"></span>?
                            <br>
                            <span class="text-orange-600">Esta acción cambiará el estado a "Anulada".</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button 
                                id="confirmAnular"
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2"
                            >
                                <span id="anularText">Sí, anular</span>
                                <div id="anularSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                            <button 
                                id="cancelAnular"
                                onclick="closeAnularModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



         <!-- Formularios ocultos -->

    <form id="anularForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

         <script>

        function confirmAnular(button) {
            const id = button.getAttribute('data-id');
            const total = button.getAttribute('data-total');
            const saleId = button.getAttribute('data-sale');
            document.getElementById('anularTotal').textContent = total;
            document.getElementById('anularModal').classList.remove('hidden');
            
            document.getElementById('confirmAnular').onclick = function() {
                const confirmBtn = document.getElementById('confirmAnular');
                const anularText = document.getElementById('anularText');
                const anularSpinner = document.getElementById('anularSpinner');
                const cancelBtn = document.getElementById('cancelAnular');
                
                // Deshabilitar botones y mostrar spinner
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                cancelBtn.disabled = true;
                cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                anularText.textContent = 'Anulando y enviando a SUNAT...';
                anularSpinner.classList.remove('hidden');
                
                // Primero anular la venta
                const form = document.getElementById('anularForm');
                form.action = `/sales/${id}/anular`;
                form.submit();
                
                // Después de un breve delay, enviar la nota de crédito a SUNAT
                setTimeout(() => {
                    enviarNotaASunatDespuesDeAnular(saleId);
                }, 2000);
            };
        }

        

        function closeAnularModal() {
            document.getElementById('anularModal').classList.add('hidden');
            resetAnularModal();
        }

        

        function resetAnularModal() {
            const confirmBtn = document.getElementById('confirmAnular');
            const anularText = document.getElementById('anularText');
            const anularSpinner = document.getElementById('anularSpinner');
            const cancelBtn = document.getElementById('cancelAnular');
            
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            anularText.textContent = 'Sí, anular';
            anularSpinner.classList.add('hidden');
        }

                 // Cerrar modales al hacer clic fuera de ellos

        document.getElementById('anularModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAnularModal();
            }
        });

        // Función para enviar nota de crédito a SUNAT después de anular
        function enviarNotaASunatDespuesDeAnular(saleId) {
            // Enviar a SUNAT
            fetch(`/sales/${saleId}/enviar-nota-sunat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Nota de crédito enviada exitosamente a SUNAT');
                    // Recargar la página para mostrar el estado actualizado
                    window.location.reload();
                } else {
                    console.error('Error al enviar a SUNAT:', data.error);
                    alert('Error al enviar nota de crédito a SUNAT: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar la nota de crédito a SUNAT');
            });
        }
    </script>
</x-app-layout>
