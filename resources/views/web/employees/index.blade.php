<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Empleados</h1>
            @can('crear-empleados')
                <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Crear Empleado
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
        
        <!-- Buscador -->
        <div class="mb-6">
            <form method="GET" action="{{ route('employees.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Buscar por nombre, documento, email o cargo..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('employees.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Tabla de empleados -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Documento</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cargo</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                        @canany(['ver-empleados', 'editar-empleados', 'eliminar-empleados'])
                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($employees as $employee)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $employee->id }}</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                {{ $employee->nombre_completo }}
                                <div class="text-xs text-gray-500">{{ $employee->telefono }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $employee->tipo_documento }}: {{ $employee->nro_documento }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $employee->email }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $employee->cargo }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $employee->branch->nombre }}
                                <div class="text-xs text-gray-500">{{ $employee->branch->company->razon_social }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                @if($employee->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            @canany(['ver-empleados', 'editar-empleados', 'eliminar-empleados'])
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    <div class="flex gap-2">
                                        @can('ver-empleados')
                                            <a href="{{ route('employees.show', $employee->id) }}" 
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs" 
                                               title="Ver detalles">
                                                Ver
                                            </a>
                                        @endcan
                                        @can('editar-empleados')
                                            <a href="{{ route('employees.edit', $employee->id) }}" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs" 
                                               title="Editar">
                                                Editar
                                            </a>
                                        @endcan
                                        @can('eliminar-empleados')
                                            <button onclick="confirmDelete(this)"
                                                    data-id="{{ $employee->id }}"
                                                    data-name="{{ $employee->nombre_completo }}"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs" 
                                                    title="Eliminar">
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['ver-empleados', 'editar-empleados', 'eliminar-empleados']) ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-500">
                                @if($search)
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-2">No se encontraron empleados</p>
                                        <p class="text-gray-600">No hay empleados que coincidan con la búsqueda "{{ $search }}"</p>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-2">No hay empleados registrados</p>
                                        <p class="text-gray-600">Comienza creando el primer empleado del sistema</p>
                                        @can('crear-empleados')
                                            <a href="{{ route('employees.create') }}" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                Crear Primer Empleado
                                            </a>
                                        @endcan
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($employees->hasPages())
            <div class="mt-6">
                {{ $employees->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de confirmación -->
    @can('eliminar-empleados')
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Confirmar eliminación</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        ¿Estás seguro de que quieres eliminar al empleado <strong id="employeeName"></strong>? Esta acción no se puede deshacer.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <form id="deleteForm" method="POST" class="inline">
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
    </div>
    @endcan

    <script>
        function confirmDelete(button) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const employeeName = document.getElementById('employeeName');
            const employeeId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            employeeName.textContent = name;
            form.action = `/employees/${employeeId}`;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

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