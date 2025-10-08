<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Inventario</h1>
            <div class="flex gap-2">
                @can('editar-inventario')
                    <a href="{{ route('inventory.edit', $inventory->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <!-- Información del producto -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Producto</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre del Producto</h3>
                    <p class="text-lg text-gray-900">{{ $inventory->producto_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Código de Barras</h3>
                    <p class="text-lg text-gray-900 font-mono">{{ $inventory->barcode }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Categoría</h3>
                    <p class="text-lg text-gray-900">{{ $inventory->categoria_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Marca</h3>
                    <p class="text-lg text-gray-900">{{ $inventory->marca_nombre }}</p>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Descripción</h3>
                    <p class="text-gray-900">{{ $inventory->producto_descripcion }}</p>
                </div>
            </div>
        </div>

        <!-- Información del almacén -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Almacén</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre del Almacén</h3>
                    <p class="text-lg text-gray-900">{{ $inventory->almacen_nombre }}</p>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Descripción del Almacén</h3>
                    <p class="text-gray-900">{{ $inventory->almacen_descripcion ?: 'Sin descripción' }}</p>
                </div>
            </div>
        </div>

        <!-- Información del inventario -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Inventario</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Stock Actual</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold {{ $inventory->stock > 10 ? 'text-green-600' : ($inventory->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $inventory->stock }}
                        </span>
                        <span class="text-sm text-gray-500">unidades</span>
                    </div>
                    @if($inventory->stock <= 10)
                        <p class="text-sm text-red-600 mt-1">Stock bajo</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Fecha de Vencimiento</h3>
                    @if($inventory->fecha_vencimiento)
                        @php
                            $fecha = \Carbon\Carbon::parse($inventory->fecha_vencimiento);
                            $diasRestantes = now()->diffInDays($fecha, false);
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-semibold {{ $diasRestantes > 30 ? 'text-green-600' : ($diasRestantes > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $fecha->format('d/m/Y') }}
                            </span>
                        </div>
                        @if($diasRestantes > 0)
                            <p class="text-sm text-gray-600 mt-1">{{ $diasRestantes }} días restantes</p>
                        @else
                            <p class="text-sm text-red-600 mt-1">Producto vencido</p>
                        @endif
                    @else
                        <p class="text-lg text-gray-500">Sin fecha de vencimiento</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Estado</h3>
                    @if($inventory->stock > 0)
                        @if($inventory->fecha_vencimiento && \Carbon\Carbon::parse($inventory->fecha_vencimiento)->isPast())
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                Vencido
                            </span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                Disponible
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            Sin Stock
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Creado:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($inventory->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Actualizado:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($inventory->updated_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="text-gray-900">{{ $inventory->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones adicionales -->
        <div class="mt-6 flex justify-end gap-4">
            @can('eliminar-inventario')
                <button 
                    onclick="confirmDelete({{ $inventory->id }}, '{{ $inventory->producto_nombre }}')"
                    id="deleteBtn"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2"
                >
                    <span id="deleteText">Eliminar Registro</span>
                    <div id="deleteSpinner" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            @endcan
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(inventoryId, productName) {
            if (confirm(`¿Estás seguro de que quieres eliminar el registro de inventario para "${productName}"? Esta acción no se puede deshacer.`)) {
                const deleteBtn = document.getElementById('deleteBtn');
                const deleteText = document.getElementById('deleteText');
                const deleteSpinner = document.getElementById('deleteSpinner');
                
                // Deshabilitar el botón y mostrar spinner
                deleteBtn.disabled = true;
                deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                deleteText.textContent = 'Eliminando...';
                deleteSpinner.classList.remove('hidden');
                
                const form = document.getElementById('deleteForm');
                form.action = `/inventory/${inventoryId}`;
                form.submit();
            }
        }
    </script>
</x-app-layout>
