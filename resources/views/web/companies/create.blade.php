<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Empresa</h1>
            <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="createCompanyForm">
            @csrf
            
            <!-- Información Básica -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica de la Empresa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                            Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="razon_social" id="razon_social" value="{{ old('razon_social') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('razon_social') border-red-500 @enderror"
                               placeholder="Ingrese la razón social de la empresa" required autofocus>
                        @error('razon_social')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-2">
                            RUC <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="ruc" id="ruc" value="{{ old('ruc') }}" maxlength="11"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('ruc') border-red-500 @enderror"
                                   placeholder="Ingrese el RUC (11 dígitos)" required>
                            <div id="rucSpinner" class="hidden absolute right-3 top-1/2 transform -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="rucMessage" class="mt-1 text-sm hidden"></div>
                        @error('ruc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <textarea name="direccion" id="direccion" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('direccion') border-red-500 @enderror"
                                  placeholder="Ingrese la dirección completa de la empresa" required>{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="logo_path" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        <input type="file" name="logo_path" id="logo_path" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('logo_path') border-red-500 @enderror"
                               accept="image/*">
                        <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</p>
                        @error('logo_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Usuario Asignado</label>
                        <select name="user_id" id="user_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                            <option value="">Seleccionar usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Configuración SUNAT -->
            <div class="bg-yellow-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4">Configuración SUNAT</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sol_user" class="block text-sm font-medium text-gray-700 mb-2">Usuario SOL</label>
                        <input type="text" name="sol_user" id="sol_user" value="{{ old('sol_user') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sol_user') border-red-500 @enderror"
                               placeholder="Usuario SOL de SUNAT">
                        @error('sol_user')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sol_pass" class="block text-sm font-medium text-gray-700 mb-2">Contraseña SOL</label>
                        <input type="password" name="sol_pass" id="sol_pass" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sol_pass') border-red-500 @enderror"
                               placeholder="Contraseña SOL de SUNAT">
                        @error('sol_pass')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cert_path" class="block text-sm font-medium text-gray-700 mb-2">Certificado Digital</label>
                        <input type="file" name="cert_path" id="cert_path" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('cert_path') border-red-500 @enderror"
                               accept=".pem,.txt">
                        <p class="mt-1 text-xs text-gray-500">Formato: PEM. Máximo 2MB.</p>
                        @error('cert_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                        <input type="text" name="client_id" id="client_id" value="{{ old('client_id') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('client_id') border-red-500 @enderror"
                               placeholder="Client ID de SUNAT">
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                        <input type="password" name="client_secret" id="client_secret" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('client_secret') border-red-500 @enderror"
                               placeholder="Client Secret de SUNAT">
                        @error('client_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="production" id="production" value="1" {{ old('production') ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="production" class="ml-2 block text-sm text-gray-700">
                            Modo Producción (SUNAT)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('companies.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Crear Empresa</span>
                    <div id="submitSpinner" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>

    <script>
        let rucTimeout;
        let isConsultingRuc = false;

        // Consultar RUC automáticamente
        async function consultarRUC(ruc) {
            if (isConsultingRuc || ruc.length !== 11) return;
            
            isConsultingRuc = true;
            const spinner = document.getElementById('rucSpinner');
            const message = document.getElementById('rucMessage');
            
            spinner.classList.remove('hidden');
            message.classList.add('hidden');
            
            try {
                const response = await fetch('/api/peru/informacion-empresa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ruc })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Autocompletar campos
                    document.getElementById('razon_social').value = data.data.nombre_o_razon_social || '';
                    document.getElementById('direccion').value = data.data.direccion_completa || data.data.direccion || '';
                    
                    message.textContent = 'Datos obtenidos correctamente';
                    message.className = 'mt-1 text-sm text-green-600';
                } else {
                    message.textContent = data.message || 'No se encontraron datos para este RUC';
                    message.className = 'mt-1 text-sm text-red-600';
                }
                message.classList.remove('hidden');
            } catch (error) {
                console.error('Error al consultar RUC:', error);
                message.textContent = 'Error al consultar el RUC. Intente nuevamente.';
                message.className = 'mt-1 text-sm text-red-600';
                message.classList.remove('hidden');
            } finally {
                spinner.classList.add('hidden');
                isConsultingRuc = false;
            }
        }

        // Event listeners
        document.getElementById('ruc').addEventListener('input', function(e) {
            const ruc = e.target.value.replace(/\D/g, '');
            e.target.value = ruc;
            
            clearTimeout(rucTimeout);
            
            if (ruc.length === 11) {
                rucTimeout = setTimeout(() => consultarRUC(ruc), 1000);
            } else {
                document.getElementById('rucMessage').classList.add('hidden');
            }
        });

        document.getElementById('createCompanyForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Creando...';
            submitSpinner.classList.remove('hidden');
        });
    </script>
</x-app-layout>
