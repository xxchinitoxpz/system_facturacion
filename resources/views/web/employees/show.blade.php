<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Empleado</h1>
            <div class="flex gap-2">
                @can('editar-empleados')
                <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    Editar
                </a>
                @endcan
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>

        <!-- Información Personal -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Personal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nombre Completo</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->nombre_completo }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->telefono }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Cargo</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->cargo }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->direccion }}</p>
                </div>
            </div>
        </div>

        <!-- Información del Documento -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Documento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Documento</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->tipo_documento }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Número de Documento</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->nro_documento }}</p>
                </div>
            </div>
        </div>

        <!-- Información Laboral -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Laboral</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Sucursal</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->branch->nombre }}</p>
                    <p class="text-sm text-gray-500">{{ $employee->branch->company->razon_social }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Ingreso</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->fecha_ingreso->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    @if($employee->activo)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="bg-indigo-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Adicional</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        @can('eliminar-empleados')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form id="deleteForm" action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Eliminar Empleado
                </button>
            </form>
        </div>
        @endcan
    </div>

    <!-- Modal de confirmación de eliminación -->
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
                        ¿Estás seguro de que quieres eliminar al empleado <strong>{{ $employee->nombre_completo }}</strong>? Esta acción no se puede deshacer.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button onclick="submitDeleteForm()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <script>
        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
        }

        function submitDeleteForm() {
            document.getElementById('deleteForm').submit();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout> 