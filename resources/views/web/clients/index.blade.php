<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Clientes</h1>
            @can('crear-clientes')
                <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Crear Cliente
                </a>
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

        <!-- Buscador y filtros -->
        <div class="mb-6">
            <form method="GET" action="{{ route('clients.index') }}">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Buscar clientes por nombre, documento o email..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div class="w-48">
                        <select name="activo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ $activo === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ $activo === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Buscar
                        </button>
                        @if($search || $activo !== null)
                            <a href="{{ route('clients.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de clientes -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        @if(auth()->user()->canAny(['ver-clientes', 'editar-clientes', 'eliminar-clientes']))
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $client->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $client->nombre_completo }}</div>
                                    @if($client->direccion)
                                        <div class="text-gray-500 text-xs">{{ \Illuminate\Support\Str::limit($client->direccion, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $client->tipo_documento }}</div>
                                    <div class="text-gray-500">{{ $client->nro_documento }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $client->telefono ?: '-' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $client->email ?: '-' }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if($client->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $client->created_at->format('d/m/Y H:i') }}
                            </td>
                            @if(auth()->user()->canAny(['ver-clientes', 'editar-clientes', 'eliminar-clientes']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-clientes')
                                            <a href="{{ route('clients.show', $client->id) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-clientes')
                                            <a href="{{ route('clients.edit', $client->id) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                               title="Editar cliente">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-clientes')
                                            <button onclick="confirmDelete(this)"
                                                    data-id="{{ $client->id }}"
                                                    data-name="{{ $client->nombre_completo }}"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                    title="Eliminar cliente">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron clientes</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($clients->hasPages())
            <div class="mt-6">
                {{ $clients->appends(request()->query())->links() }}
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
                            ¿Estás seguro de que quieres eliminar el cliente "<span id="clientName" class="font-semibold"></span>"?
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
            
            document.getElementById('clientName').textContent = name;
            document.getElementById('deleteForm').action = `/clients/${id}`;
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
