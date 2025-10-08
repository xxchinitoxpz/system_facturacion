<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-indigo-800">Sucursales y Series</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Sucursales creadas: {{ $totalBranches }} de 2 permitidas
                </p>
            </div>
            @can('crear-sucursales')
                @if($canCreate)
                    <a href="{{ route('branches.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Crear Sucursal
                    </a>
                @else
                    <div class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                        Límite alcanzado
                    </div>
                @endif
            @endcan
        </div>

        <!-- Mensajes de sesión -->
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
            <form method="GET" action="{{ route('branches.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Buscar por nombre, dirección o teléfono..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('branches.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Tabla de sucursales -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dirección</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Empresa</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Series</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($branches as $branch)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $branch->id }}</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $branch->nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $branch->direccion }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $branch->telefono }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $branch->company->razon_social ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $branch->documentSeries->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $branch->documentSeries->count() }} {{ $branch->documentSeries->count() == 1 ? 'serie' : 'series' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                <div class="flex gap-2">
                                    @can('ver-sucursales')
                                        <a href="{{ route('branches.show', $branch->id) }}" 
                                           class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs" 
                                           title="Ver detalles">
                                            Ver
                                        </a>
                                    @endcan
                                    @can('editar-sucursales')
                                        <a href="{{ route('branches.edit', $branch->id) }}" 
                                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs" 
                                           title="Editar">
                                            Editar
                                        </a>
                                    @endcan
                                    @can('eliminar-sucursales')
                                        <button onclick="confirmDelete(this)"
                                                data-id="{{ $branch->id }}"
                                                data-name="{{ $branch->nombre }}"
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs" 
                                                title="Eliminar">
                                            Eliminar
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-400">
                                @if($search)
                                    No se encontraron sucursales que coincidan con "{{ $search }}"
                                @else
                                    No hay sucursales registradas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="mt-6">
            {{ $branches->appends(['search' => $search])->links() }}
        </div>
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
                            ¿Estás seguro de que quieres eliminar la sucursal "<span id="branchName" class="font-semibold"></span>"?
                            <br>
                            <span class="text-red-600">Esta acción no se puede deshacer.</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button id="confirmDelete"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                <span id="confirmText">Sí, eliminar</span>
                                <div id="confirmSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                            <button onclick="closeDeleteModal()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de eliminación -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            document.getElementById('branchName').textContent = name;
            document.getElementById('deleteForm').action = `/branches/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            const spinner = document.getElementById('confirmSpinner');
            const text = document.getElementById('confirmText');
            
            spinner.classList.remove('hidden');
            text.textContent = 'Eliminando...';
            
            document.getElementById('deleteForm').submit();
        });

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Cerrar modal haciendo clic fuera
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout> 