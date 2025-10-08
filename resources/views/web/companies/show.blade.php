<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de la Empresa</h1>
            <div class="flex gap-2">
                @can('editar-empresas')
                    <a href="{{ route('companies.edit', $company->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Información Básica -->
            <div class="space-y-6">
                <div class="bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Razón Social</label>
                            <p class="mt-1 text-lg font-semibold text-indigo-700">{{ $company->razon_social }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">RUC</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800">{{ $company->ruc }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Dirección</label>
                            <p class="mt-1 text-gray-800">{{ $company->direccion }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Usuario Asignado</label>
                            <p class="mt-1 text-gray-800">{{ $company->user->name ?? 'No asignado' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Modo Producción</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $company->production ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $company->production ? 'Sí' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                @if($company->logo_path)
                    <div class="bg-indigo-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-indigo-800 mb-4">Logo</h3>
                        <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo de la empresa" class="w-32 h-32 object-cover rounded-lg shadow">
                    </div>
                @endif
            </div>

            <!-- Configuración SUNAT -->
            <div class="space-y-6">
                <div class="bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">Configuración SUNAT</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Usuario SOL</label>
                            <p class="mt-1 text-gray-800">{{ $company->sol_user ?? 'No configurado' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Contraseña SOL</label>
                            <p class="mt-1 text-gray-800">{{ $company->sol_pass ? '••••••••' : 'No configurada' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Certificado</label>
                            <p class="mt-1 text-gray-800">{{ $company->cert_path ? basename($company->cert_path) : 'No configurado' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Client ID</label>
                            <p class="mt-1 text-gray-800">{{ $company->client_id ?? 'No configurado' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Client Secret</label>
                            <p class="mt-1 text-gray-800">{{ $company->client_secret ? '••••••••' : 'No configurado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Sistema -->
                <div class="bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Sistema</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID de la Empresa</label>
                            <p class="mt-1 text-gray-800">{{ $company->id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Creación</label>
                            <p class="mt-1 text-gray-800">{{ $company->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Última Actualización</label>
                            <p class="mt-1 text-gray-800">{{ $company->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Empresa creada el {{ $company->created_at->diffForHumans() }}
                </div>
                <div class="flex gap-2">
                    @can('eliminar-empresas')
                        @if(\App\Models\Company::count() > 1)
                            <form id="deleteCompanyForm" action="{{ route('companies.destroy', $company->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="deleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                    <span id="deleteText">Eliminar Empresa</span>
                                    <div id="deleteSpinner" class="hidden">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </button>
                            </form>
                        @else
                            <span class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" title="No se puede eliminar la única empresa del sistema">
                                No se puede eliminar
                            </span>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <script>
        // Solo ejecutar si existe el formulario de eliminación
        const deleteForm = document.getElementById('deleteCompanyForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('¿Estás seguro de que quieres eliminar esta empresa? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                    return;
                }
                
                const deleteBtn = document.getElementById('deleteBtn');
                const deleteText = document.getElementById('deleteText');
                const deleteSpinner = document.getElementById('deleteSpinner');
                
                // Deshabilitar el botón y mostrar spinner
                deleteBtn.disabled = true;
                deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                deleteText.textContent = 'Eliminando...';
                deleteSpinner.classList.remove('hidden');
                
                // El formulario se enviará normalmente
            });
        }
    </script>
</x-app-layout>
