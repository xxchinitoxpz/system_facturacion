<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalle de Rol: {{ $role->name }}</h1>
            <div class="flex gap-3">
                <a href="{{ route('roles.edit', $role->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Editar
                </a>
                <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Nombre del Rol</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $role->name }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">ID del Rol</h3>
                <p class="text-lg font-semibold text-indigo-700">{{ $role->id }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Total de Permisos</h3>
                <p class="text-lg font-semibold text-blue-600">{{ $role->permissions->count() }} permisos</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Usuarios Asignados</h3>
                <p class="text-lg font-semibold text-green-600">{{ $users->count() }} usuarios</p>
            </div>
        </div>

        <!-- Permisos Asignados -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Permisos Asignados</h3>
            
            @if($role->permissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($role->permissions as $permission)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium text-blue-800">{{ $permission->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <p class="text-gray-500">Este rol no tiene permisos asignados.</p>
                </div>
            @endif
        </div>

        <!-- Usuarios Asignados -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Usuarios con este Rol</h3>
            
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha de Registro</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-semibold">{{ $user->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800">{{ $user->email }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800">{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <form action="{{ route('roles.remove') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="hidden" name="role" value="{{ $role->name }}">
                                            <button type="submit" 
                                                    onclick="return confirm('¿Estás seguro de que quieres remover el rol de este usuario?')"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                                                Remover Rol
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500">No hay usuarios asignados a este rol.</p>
                </div>
            @endif
        </div>

        <!-- Asignar Rol a Usuario -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Asignar Rol a Usuario</h3>
            <form action="{{ route('roles.assign') }}" method="POST" class="flex gap-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role->name }}">
                <div class="flex-1">
                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar usuario...</option>
                        @foreach(\App\Models\User::whereDoesntHave('roles', function($query) use ($role) {
                            $query->where('name', $role->name);
                        })->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Asignar Rol
                </button>
            </form>
        </div>

        <!-- Información de Auditoría -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Información de Auditoría</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Creado:</span> {{ $role->created_at->format('d/m/Y H:i:s') }}
                </div>
                <div>
                    <span class="font-medium">Última actualización:</span> {{ $role->updated_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
            @if($users->count() == 0)
                <button 
                    data-id="{{ $role->id }}"
                    data-name="{{ $role->name }}"
                    onclick="confirmDelete(this)"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    Eliminar Rol
                </button>
            @endif
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
                            ¿Estás seguro de que quieres eliminar el rol "<span id="roleName" class="font-semibold"></span>"?
                            <br>
                            <span class="text-red-600">Esta acción no se puede deshacer.</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button 
                                id="confirmDelete"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            >
                                Sí, eliminar
                            </button>
                            <button 
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
            document.getElementById('roleName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('confirmDelete').onclick = function() {
                const form = document.getElementById('deleteForm');
                form.action = `/roles/${id}`;
                form.submit();
            };
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