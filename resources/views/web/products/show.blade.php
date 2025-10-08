<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Producto</h1>
            <div class="flex gap-2">
                @can('editar-productos')
                    <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre</h3>
                    <p class="text-lg text-gray-900">{{ $product->nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Código de Barras</h3>
                    <p class="text-lg text-gray-900 font-mono">{{ $product->barcode }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Categoría</h3>
                    <p class="text-lg text-gray-900">{{ $product->category->nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Marca</h3>
                    <p class="text-lg text-gray-900">{{ $product->brand->nombre }}</p>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Descripción</h3>
                    <p class="text-gray-900">{{ $product->descripcion }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Creado:</span>
                        <span class="text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Actualizado:</span>
                        <span class="text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="text-gray-900">{{ $product->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Presentaciones -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Presentaciones</h2>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                    {{ $product->presentations->count() }} presentación(es)
                </span>
            </div>

            @if($product->presentations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-indigo-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio de Venta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidades</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio por Unidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($product->presentations as $index => $presentation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $presentation->nombre }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="font-semibold text-green-600">S/{{ number_format($presentation->precio_venta, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $presentation->unidades }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="text-blue-600">S/{{ number_format($presentation->precio_venta / $presentation->unidades, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $presentation->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No hay presentaciones registradas</p>
                    <p class="text-sm text-gray-400">Este producto no tiene presentaciones configuradas</p>
                </div>
            @endif
        </div>

        <!-- Acciones adicionales -->
        <div class="mt-6 flex justify-end gap-4">
            @can('eliminar-productos')
                <button 
                    onclick="confirmDelete({{ $product->id }}, '{{ $product->nombre }}')"
                    id="deleteBtn"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2"
                >
                    <span id="deleteText">Eliminar Producto</span>
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
        function confirmDelete(productId, productName) {
            if (confirm(`¿Estás seguro de que quieres eliminar el producto "${productName}"? Esta acción eliminará también todas sus presentaciones y no se puede deshacer.`)) {
                const deleteBtn = document.getElementById('deleteBtn');
                const deleteText = document.getElementById('deleteText');
                const deleteSpinner = document.getElementById('deleteSpinner');
                
                // Deshabilitar el botón y mostrar spinner
                deleteBtn.disabled = true;
                deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                deleteText.textContent = 'Eliminando...';
                deleteSpinner.classList.remove('hidden');
                
                const form = document.getElementById('deleteForm');
                form.action = `/products/${productId}`;
                form.submit();
            }
        }
    </script>
</x-app-layout>
