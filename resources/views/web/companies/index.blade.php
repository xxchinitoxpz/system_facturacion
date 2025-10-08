<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Empresas</h1>
            @can('crear-empresas')
                @if($companies->count() == 0)
                    <a href="{{ route('companies.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Crear Empresa
                    </a>
                @else
                    <span class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed">
                        Empresa ya creada
                    </span>
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

        <!-- Información del sistema -->
        @if($companies->count() > 0)
            <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Información:</strong> Solo se permite una empresa por sistema. Puedes editar la empresa existente.</span>
                </div>
            </div>
        @endif
        
        <!-- Buscador -->
        <div class="mb-6">
            <form method="GET" action="{{ route('companies.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Buscar por razón social, RUC o dirección..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('companies.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Tabla de empresas -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Logo</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Razón Social</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">RUC</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dirección</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Producción</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($companies as $company)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $company->id }}</td>
                            <td class="px-4 py-2 text-sm">
                                @if($company->logo_path)
                                    <img src="{{ asset('storage/' . $company->logo_path) }}" 
                                         alt="Logo de {{ $company->razon_social }}" 
                                         class="w-12 h-12 object-cover rounded-lg border">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-6.75 4.5h.75m-1.5-1.5h.75M21 21V9.75M21 21h-3.375c-.621 0-1.125-.504-1.125-1.125V21M21 21h-3.375c-.621 0-1.125-.504-1.125-1.125V9.75" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-indigo-700 font-semibold">{{ $company->razon_social }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $company->ruc }}</span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">{{ str()->limit($company->direccion, 50) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $company->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $company->production ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $company->production ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <div class="flex gap-2">
                                    @can('ver-empresas')
                                        <a href="{{ route('companies.show', $company->id) }}" 
                                           class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                           title="Ver detalles">
                                            Ver
                                        </a>
                                    @endcan
                                    @can('editar-empresas')
                                        <a href="{{ route('companies.edit', $company->id) }}" 
                                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs">
                                            Editar
                                        </a>
                                    @endcan
                                    @can('eliminar-empresas')
                                        @if($companies->count() > 1)
                                            <button data-id="{{ $company->id }}"
                                                    data-name="{{ $company->razon_social }}"
                                                    onclick="confirmDelete(this)"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                                                Eliminar
                                            </button>
                                        @else
                                            <span class="px-3 py-1 bg-gray-400 text-white rounded text-xs cursor-not-allowed" 
                                                  title="No se puede eliminar la única empresa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                </svg>
                                            </span>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-400">
                                @if($search)
                                    No se encontraron empresas que coincidan con "{{ $search }}"
                                @else
                                    No hay empresas registradas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="mt-6">
            {{ $companies->appends(['search' => $search])->links() }}
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
                            ¿Estás seguro de que quieres eliminar la empresa "<span id="companyName" class="font-semibold"></span>"?
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
            
            document.getElementById('companyName').textContent = name;
            document.getElementById('deleteForm').action = `/companies/${id}`;
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
