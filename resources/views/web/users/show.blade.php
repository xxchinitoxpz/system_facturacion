<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalle de Usuario: {{ $user->name }}</h1>
            <div class="flex gap-3">
                @can('editar-usuarios')
                <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Editar
                </a>
                @endcan
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Información Principal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Nombre Completo</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">ID del Usuario</h3>
                <p class="text-lg font-semibold text-indigo-700">{{ $user->id }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Total de Roles</h3>
                <p class="text-lg font-semibold text-blue-600">{{ $user->roles->count() }} roles</p>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Usuario</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Email</h4>
                    <p class="text-sm text-gray-900">{{ $user->email }}</p>
                </div>

                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Fecha de Registro</h4>
                    <p class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Última Actualización</h4>
                    <p class="text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div class="bg-orange-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Total de Permisos</h4>
                    <p class="text-sm text-gray-900">{{ $user->getAllPermissions()->count() }} permisos</p>
                </div>
            </div>
        </div>

        <!-- Roles Asignados -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Roles Asignados</h3>
            
            @if($user->roles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($user->roles as $role)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                                <span class="text-sm font-medium text-blue-800">{{ $role->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="text-gray-500">Este usuario no tiene roles asignados.</p>
                </div>
            @endif
        </div>

        <!-- Permisos del Usuario -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Permisos del Usuario</h3>
            
            @php
                $permissions = $user->getAllPermissions();
            @endphp
            
            @if($permissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-green-800">{{ $permission->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="text-gray-500">Este usuario no tiene permisos asignados.</p>
                </div>
            @endif
        </div>

        <!-- Información de Auditoría -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Información de Auditoría</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Creado:</span> {{ $user->created_at->format('d/m/Y H:i:s') }}
                </div>
                <div>
                    <span class="font-medium">Última actualización:</span> {{ $user->updated_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
            @can('eliminar-usuarios')
                @if($user->id !== auth()->id())
                    <button 
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        onclick="confirmDelete(this)"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Eliminar Usuario
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                        No puedes eliminar tu propia cuenta
                    </span>
                @endif
            @endcan
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
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
                            ¿Estás seguro de que quieres eliminar al usuario "<span id="userName" class="font-semibold"></span>"?
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
            document.getElementById('userName').textContent = name;
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
                form.action = `/users/${id}`;
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