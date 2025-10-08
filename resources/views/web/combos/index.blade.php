<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Combos</h1>
            @can('crear-combos')
                <a href="{{ route('combos.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Crear Combo
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

        <!-- Barra de búsqueda -->
        <div class="mb-6">
            <form method="GET" action="{{ route('combos.index') }}">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Buscar combos por nombre..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Buscar
                        </button>
                        @if($search)
                            <a href="{{ route('combos.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de combos -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        @if(auth()->user()->canAny(['ver-combos', 'editar-combos', 'eliminar-combos']))
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($combos as $combo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $combo->id }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $combo->nombre }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <span class="font-semibold text-green-600">S/ {{ number_format($combo->precio, 2) }}</span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                @if($combo->estado)
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
                                <div class="text-xs">
                                    @foreach($combo->products as $product)
                                        <div class="mb-1">
                                            <span class="font-medium">{{ $product->nombre }}</span>
                                            <span class="text-gray-500">({{ $product->pivot->cantidad }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">
                                {{ $combo->created_at->format('d/m/Y H:i') }}
                            </td>
                            @if(auth()->user()->canAny(['ver-combos', 'editar-combos', 'eliminar-combos']))
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <div class="flex gap-2">
                                        @can('ver-combos')
                                            <a href="{{ route('combos.show', $combo) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-combos')
                                            <a href="{{ route('combos.edit', $combo) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                               title="Editar combo">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-combos')
                                            <button 
                                                onclick="confirmDelete(this)"
                                                data-id="{{ $combo->id }}"
                                                data-name="{{ $combo->nombre }}"
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                title="Eliminar combo">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron combos</p>
                                    <p class="text-sm">No hay combos registrados en el sistema.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($combos->hasPages())
            <div class="mt-6">
                {{ $combos->links() }}
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
                            ¿Estás seguro de que quieres eliminar el combo "<span id="comboName" class="font-semibold"></span>"?
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
            document.getElementById('comboName').textContent = name;
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
                form.action = `/combos/${id}`;
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
