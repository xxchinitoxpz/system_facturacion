<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Inventario</h1>
            <div class="flex gap-2">
                @can('ver-inventario')
                    <button onclick="openReportModal()" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Descargar PDF
                    </button>
                @endcan
                @can('crear-inventario')
                    <a href="{{ route('inventory.import.form') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Importar Excel
                    </a>
                    <a href="{{ route('inventory.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        Crear Registro
                    </a>
                @endcan
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

        @if(session('warning'))
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('import_errors'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="font-semibold text-red-800 mb-2">Errores de importación:</h4>
                <div class="max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $error)
                        <div class="text-sm text-red-700 mb-1">
                            <strong>Fila {{ $error['row'] }}:</strong> {{ $error['error'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Barra de búsqueda y filtros -->
        <div class="mb-6">
            <form method="GET" action="{{ route('inventory.index') }}">
                <div class="flex gap-4 items-center">
                    <!-- Campo de búsqueda principal (más grande) -->
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Buscar por producto, código de barras o almacén..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Select de almacén -->
                    <div class="w-48">
                        <select name="almacen_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los almacenes</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $almacen_id == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Toggle para mostrar productos con stock 0 -->
                    <div class="flex items-center gap-2">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input 
                                    type="checkbox" 
                                    name="mostrar_stock_cero" 
                                    value="1" 
                                    {{ $mostrar_stock_cero ? 'checked' : '' }}
                                    class="sr-only"
                                    onchange="this.form.submit()"
                                >
                                <div class="block bg-gray-300 w-12 h-6 rounded-full transition-colors duration-200 ease-in-out {{ $mostrar_stock_cero ? 'bg-indigo-600' : '' }}"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 ease-in-out {{ $mostrar_stock_cero ? 'transform translate-x-6' : '' }}"></div>
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">Mostrar stock 0</span>
                        </label>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Buscar
                        </button>
                        @if($search || $almacen_id || $mostrar_stock_cero)
                            <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de inventario -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Almacén</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actualización</th>
                        @if(auth()->user()->canAny(['ver-inventario', 'editar-inventario', 'eliminar-inventario']))
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventory as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $item->producto_nombre }}</div>
                                    <div class="text-gray-500 text-xs">{{ $item->categoria_nombre }} - {{ $item->marca_nombre }}</div>
                                    <div class="text-gray-400 text-xs font-mono">{{ $item->barcode }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->almacen_nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->stock > 10 ? 'bg-green-100 text-green-800' : ($item->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item->stock }} unidades
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                @if($item->fecha_vencimiento)
                                    @php
                                        $fecha = \Carbon\Carbon::parse($item->fecha_vencimiento);
                                        $diasRestantes = now()->diffInDays($fecha, false);
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $diasRestantes > 30 ? 'bg-green-100 text-green-800' : ($diasRestantes > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $fecha->format('d/m/Y') }}
                                        @if($diasRestantes > 0)
                                            <br><span class="text-xs">({{ $diasRestantes }} días)</span>
                                        @else
                                            <br><span class="text-xs">(Vencido)</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Sin fecha</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}</td>
                            @if(auth()->user()->canAny(['ver-inventario', 'editar-inventario', 'eliminar-inventario']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-inventario')
                                            <a href="{{ route('inventory.show', $item->id) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-inventario')
                                            <a href="{{ route('inventory.edit', $item->id) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                               title="Editar inventario">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-inventario')
                                            <button 
                                                onclick="confirmDelete(this)"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->producto_nombre }}"
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                title="Eliminar registro">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['ver-inventario', 'editar-inventario', 'eliminar-inventario']) ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron registros de inventario</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($inventory->hasPages())
            <div class="mt-6">
                {{ $inventory->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de confirmación -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar eliminación</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            ¿Estás seguro de que quieres eliminar el registro de inventario para "<span id="inventoryProductName" class="font-semibold"></span>"?
                            <br>
                            <span class="text-red-600">Esta acción no se puede deshacer.</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button 
                                id="confirmDelete"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2"
                            >
                                <span id="confirmText">Sí, eliminar</span>
                                <div id="confirmSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                            <button 
                                id="cancelDelete"
                                onclick="closeDeleteModal()"
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

    <!-- Formulario oculto para eliminar -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <style>
        /* Estilos para el toggle switch */
        .dot {
            transition: transform 0.2s ease-in-out;
        }
        
        input:checked + .block .dot {
            transform: translateX(1.5rem);
        }
        
        input:checked + .block {
            background-color: #4f46e5;
        }
    </style>

    <script>
        function confirmDelete(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            document.getElementById('inventoryProductName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
            
            document.getElementById('confirmDelete').onclick = function() {
                const confirmBtn = document.getElementById('confirmDelete');
                const confirmText = document.getElementById('confirmText');
                const confirmSpinner = document.getElementById('confirmSpinner');
                const cancelBtn = document.getElementById('cancelDelete');
                
                // Deshabilitar botones y mostrar spinner
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                cancelBtn.disabled = true;
                cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                confirmText.textContent = 'Eliminando...';
                confirmSpinner.classList.remove('hidden');
                
                // Enviar formulario
                const form = document.getElementById('deleteForm');
                form.action = `/inventory/${id}`;
                form.submit();
            };
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            
            // Resetear estado del botón
            const confirmBtn = document.getElementById('confirmDelete');
            const confirmText = document.getElementById('confirmText');
            const confirmSpinner = document.getElementById('confirmSpinner');
            const cancelBtn = document.getElementById('cancelDelete');
            
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            confirmText.textContent = 'Sí, eliminar';
            confirmSpinner.classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Funciones para el modal de reportes
        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
        }

        function downloadReport(type) {
            const params = new URLSearchParams({
                search: '{{ $search }}',
                almacen_id: '{{ $almacen_id }}',
                mostrar_stock_cero: '{{ $mostrar_stock_cero }}'
            });
            
            let url;
            if (type === 'detailed') {
                url = '{{ route("inventory.pdf") }}?' + params.toString();
            } else if (type === 'grouped') {
                url = '{{ route("inventory.stock-report") }}?' + params.toString();
            }
            
            window.open(url, '_blank');
            closeReportModal();
        }
    </script>

    <!-- Modal para seleccionar tipo de reporte -->
    <div id="reportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="mt-2 px-7 py-3">
                    <h3 class="text-lg font-medium text-gray-900 text-center">Seleccionar Tipo de Reporte</h3>
                    <div class="mt-4 space-y-3">
                        <button onclick="downloadReport('detailed')" 
                                class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Inventario Detallado
                        </button>
                        <button onclick="downloadReport('grouped')" 
                                class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Stock Agrupado
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-center px-4 py-3">
                    <button onclick="closeReportModal()" 
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
