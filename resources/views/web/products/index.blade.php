<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Productos</h1>
            @can('crear-productos')
                <a href="{{ route('products.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Crear Producto
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
            <form method="GET" action="{{ route('products.index') }}">
                <div class="flex gap-4 items-end">
                    <!-- Campo de búsqueda principal (más grande) -->
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Buscar productos por nombre, descripción o código de barras..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Select de categoría -->
                    <div class="w-48">
                        <select name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoria_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Select de marca -->
                    <div class="w-48">
                        <select name="marca_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas las marcas</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $marca_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Buscar
                        </button>
                        @if($search || $categoria_id || $marca_id)
                            <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de productos -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presentaciones</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        @if(auth()->user()->canAny(['ver-productos', 'editar-productos', 'eliminar-productos']))
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $product->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $product->nombre }}</div>
                                    <div class="text-gray-500 text-xs">{{ str()->limit($product->descripcion, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $product->category->nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $product->brand->nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-mono">{{ $product->barcode }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div class="space-y-1">
                                    @foreach($product->presentations as $presentation)
                                        <div class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            {{ $presentation->nombre }} - S/{{ number_format($presentation->precio_venta, 2) }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $product->created_at->format('d/m/Y H:i') }}</td>
                            @if(auth()->user()->canAny(['ver-productos', 'editar-productos', 'eliminar-productos']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-productos')
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-productos')
                                            <a href="{{ route('products.edit', $product) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                               title="Editar producto">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-productos')
                                            <button 
                                                onclick="confirmDelete(this)"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->nombre }}"
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                title="Eliminar producto">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['ver-productos', 'editar-productos', 'eliminar-productos']) ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron productos</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($products->hasPages())
            <div class="mt-6">
                {{ $products->appends(request()->query())->links() }}
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
                            ¿Estás seguro de que quieres eliminar el producto "<span id="productName" class="font-semibold"></span>"?
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

    <script>
        function confirmDelete(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            document.getElementById('productName').textContent = name;
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
                form.action = `/products/${id}`;
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
    </script>
</x-app-layout>
