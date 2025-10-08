<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Proveedor</h1>
            <div class="flex gap-2">
                @can('editar-proveedores')
                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>

        <!-- Información del proveedor -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información básica -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Información Básica</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombre Completo</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->nombre_completo }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tipo de Documento</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->tipo_documento }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Número de Documento</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->nro_documento }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Estado</label>
                            <div class="mt-1">
                                @if($supplier->activo)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de contacto -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Información de Contacto</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Teléfono</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                @if($supplier->telefono)
                                    <a href="tel:{{ $supplier->telefono }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $supplier->telefono }}
                                    </a>
                                @else
                                    <span class="text-gray-400">No especificado</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $supplier->email }}
                                    </a>
                                @else
                                    <span class="text-gray-400">No especificado</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dirección</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                @if($supplier->direccion)
                                    {{ $supplier->direccion }}
                                @else
                                    <span class="text-gray-400">No especificada</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Adicional</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Fecha de Creación</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Última Actualización</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplier->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        @if(auth()->user()->canAny(['editar-proveedores', 'eliminar-proveedores']))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Acciones</h2>
                
                <div class="flex gap-3">
                    @can('editar-proveedores')
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Proveedor
                        </a>
                    @endcan
                    
                    @can('eliminar-proveedores')
                        <button onclick="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar Proveedor
                        </button>
                    @endcan
                </div>
            </div>
        @endif
    </div>

    <!-- Formulario de eliminación -->
    <form id="deleteForm" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('¿Estás seguro de que quieres eliminar este proveedor? Esta acción no se puede deshacer.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout>
