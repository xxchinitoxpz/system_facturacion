<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de la Categoría</h1>
            <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <!-- Información de la Categoría -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-sm text-gray-900">{{ $category->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <p class="text-sm text-gray-900">{{ $category->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                    <p class="text-sm text-gray-900">{{ $category->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                    <p class="text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y H:i:s') }}</p>
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
                            <p class="text-2xl font-semibold text-gray-900">{{ $category->products_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
            @can('editar-categorias')
                <a href="{{ route('categories.edit', $category) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Editar Categoría
                </a>
            @endcan
            @can('eliminar-categorias')
                <button onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar Categoría
                </button>
            @endcan
        </div>
    </div>

    <!-- Formulario de eliminación -->
    <form id="deleteForm" action="{{ route('categories.destroy', $category) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('¿Estás seguro de que quieres eliminar la categoría "{{ $category->nombre }}"? Esta acción no se puede deshacer.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout> 