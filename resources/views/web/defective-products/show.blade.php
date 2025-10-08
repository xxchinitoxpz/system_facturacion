<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Producto Defectuoso</h1>
            <div class="flex gap-2">
                @can('editar-productos-defectuosos')
                    <a href="{{ route('defective-products.edit', $defectiveProduct->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('defective-products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <!-- Información del producto defectuoso -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Producto Defectuoso</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Producto</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->producto_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Código de Barras</h3>
                    <p class="text-lg text-gray-900 font-mono">{{ $defectiveProduct->barcode }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Categoría</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->categoria_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Marca</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->marca_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Cantidad</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $defectiveProduct->cantidad }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Estado</h3>
                    @if($defectiveProduct->estado == 'cambiado')
                        <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">Cambiado</span>
                    @elseif($defectiveProduct->estado == 'almacenado')
                        <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">Almacenado</span>
                    @elseif($defectiveProduct->estado == 'deshechado')
                        <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">Deshechado</span>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Fecha de Registro</h3>
                    <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($defectiveProduct->fecha_registro)->format('d/m/Y') }}</p>
                </div>

                @if($defectiveProduct->observaciones)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Observaciones</h3>
                        <p class="text-gray-900">{{ $defectiveProduct->observaciones }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Creado:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($defectiveProduct->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Actualizado:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($defectiveProduct->updated_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">ID:</span>
                        <span class="text-gray-900">{{ $defectiveProduct->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del producto -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Producto</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->producto_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Código de Barras</h3>
                    <p class="text-lg text-gray-900 font-mono">{{ $defectiveProduct->barcode }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Categoría</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->categoria_nombre }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Marca</h3>
                    <p class="text-lg text-gray-900">{{ $defectiveProduct->marca_nombre }}</p>
                </div>

                @if($defectiveProduct->producto_descripcion)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Descripción</h3>
                        <p class="text-gray-900">{{ $defectiveProduct->producto_descripcion }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="mt-6 flex justify-end gap-4">
            @can('eliminar-productos-defectuosos')
                <button onclick="confirmDelete({{ $defectiveProduct->id }}, '{{ $defectiveProduct->producto_nombre }}')" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Eliminar
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
        function confirmDelete(defectiveProductId, productName) {
            if (confirm(`¿Estás seguro de que quieres eliminar el registro de producto defectuoso "${productName}"? Esta acción no se puede deshacer.`)) {
                const form = document.getElementById('deleteForm');
                form.action = `/defective-products/${defectiveProductId}`;
                form.submit();
            }
        }
    </script>
</x-app-layout>
