<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Almacén</h1>
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
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

        <!-- Información del Almacén -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-sm text-gray-900">{{ $warehouse->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <p class="text-sm text-gray-900 font-semibold">{{ $warehouse->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <p class="text-sm text-gray-900">{{ $warehouse->branch->nombre ?? 'No asignada' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                    <p class="text-sm text-gray-900">{{ $warehouse->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <p class="text-sm text-gray-900">{{ $warehouse->descripcion ?: 'Sin descripción' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                    <p class="text-sm text-gray-900">{{ $warehouse->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="bg-green-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-green-800 mb-4">Estadísticas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Productos Asociados</p>
                            <p class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
            @can('editar-almacenes')
                <a href="{{ route('warehouses.edit', $warehouse) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Editar Almacén
                </a>
            @endcan
            @can('eliminar-almacenes')
                <button onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar Almacén
                </button>
            @endcan
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmar Eliminación</h3>
                <p class="text-gray-600 mb-6">¿Estás seguro de que quieres eliminar el almacén "{{ $warehouse->nombre }}"?</p>
                <div class="flex gap-3 justify-end">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout> 