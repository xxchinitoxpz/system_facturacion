@php
use Illuminate\Support\Str;
@endphp

<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-indigo-800">Almacenes</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Almacenes creados: {{ $stats['branchesWithWarehouse'] }} de {{ $stats['totalBranches'] }} sucursales
                    @if($stats['availableBranches'] > 0)
                        <span class="text-green-600">({{ $stats['availableBranches'] }} sucursal{{ $stats['availableBranches'] > 1 ? 'es' : '' }} disponible{{ $stats['availableBranches'] > 1 ? 's' : '' }})</span>
                    @else
                        <span class="text-red-600">(Todas las sucursales tienen almacén)</span>
                    @endif
                </p>
            </div>
            @can('crear-almacenes')
                @if($stats['canCreate'])
                    <a href="{{ route('warehouses.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        Crear Almacén
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
            <form method="GET" action="{{ route('warehouses.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Buscar almacenes..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('warehouses.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabla de almacenes -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        @if(auth()->user()->canAny(['ver-almacenes', 'editar-almacenes', 'eliminar-almacenes']))
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($warehouses as $warehouse)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $warehouse->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold">{{ $warehouse->nombre }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $warehouse->branch->nombre ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ Str::limit($warehouse->descripcion, 50) ?: '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $warehouse->created_at->format('d/m/Y H:i') }}</td>
                            @if(auth()->user()->canAny(['ver-almacenes', 'editar-almacenes', 'eliminar-almacenes']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-almacenes')
                                            <a href="{{ route('warehouses.show', $warehouse) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-almacenes')
                                            <a href="{{ route('warehouses.edit', $warehouse) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                               title="Editar almacén">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-almacenes')
                                            <button onclick="confirmDelete(this)"
                                                    data-id="{{ $warehouse->id }}"
                                                    data-name="{{ $warehouse->nombre }}"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                    title="Eliminar almacén">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['ver-almacenes', 'editar-almacenes', 'eliminar-almacenes']) ? 6 : 5 }}" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No hay almacenes</p>
                                    <p class="text-gray-500 mb-4">Comienza creando tu primer almacén</p>
                                    @can('crear-almacenes')
                                        <a href="{{ route('warehouses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                            Crear Primer Almacén
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($warehouses->hasPages())
            <div class="mt-6">
                {{ $warehouses->appends(['search' => $search])->links() }}
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
                            ¿Estás seguro de que quieres eliminar el almacén "<span id="warehouseName" class="font-semibold"></span>"?
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
            
            document.getElementById('warehouseName').textContent = name;
            document.getElementById('deleteForm').action = `/warehouses/${id}`;
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