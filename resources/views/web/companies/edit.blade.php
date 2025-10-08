<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Empresa</h1>
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

        <form action="{{ route('companies.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="editCompanyForm">
            @csrf
            @method('PUT')
            
            <!-- Información Básica de la Empresa -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica de la Empresa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                            Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="razon_social" id="razon_social" value="{{ old('razon_social', $company->razon_social) }}" 
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
                        <input type="text" name="ruc" id="ruc" value="{{ old('ruc', $company->ruc) }}" maxlength="11"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('ruc') border-red-500 @enderror"
                               placeholder="Ingrese el RUC (11 dígitos)" required>
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
                                  placeholder="Ingrese la dirección completa de la empresa" required>{{ old('direccion', $company->direccion) }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="logo_path" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo
                        </label>
                        @if($company->logo_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo actual" class="w-20 h-20 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-1">Logo actual</p>
                            </div>
                        @endif
                        <input type="file" name="logo_path" id="logo_path" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('logo_path') border-red-500 @enderror"
                               accept="image/*">
                        <p class="mt-1 text-xs text-gray-500">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB. Dejar vacío para mantener el actual.</p>
                        @error('logo_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Usuario Asignado
                        </label>
                        <select name="user_id" id="user_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                            <option value="">Seleccionar usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $company->user_id) == $user->id ? 'selected' : '' }}>
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
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Configuración SUNAT</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sol_user" class="block text-sm font-medium text-gray-700 mb-2">
                            Usuario SOL
                        </label>
                        <input type="text" name="sol_user" id="sol_user" value="{{ old('sol_user', $company->sol_user) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sol_user') border-red-500 @enderror"
                               placeholder="Ingrese el usuario SOL">
                        @error('sol_user')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sol_pass" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña SOL
                        </label>
                        <input type="password" name="sol_pass" id="sol_pass" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sol_pass') border-red-500 @enderror"
                               placeholder="Dejar vacío para mantener la actual">
                        <p class="mt-1 text-xs text-gray-500">Dejar vacío para mantener la contraseña actual.</p>
                        @error('sol_pass')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cert_path" class="block text-sm font-medium text-gray-700 mb-2">
                            Certificado Digital
                        </label>
                        @if($company->cert_path)
                            <div class="mb-2">
                                <span class="text-sm text-gray-600">Certificado actual: {{ basename($company->cert_path) }}</span>
                            </div>
                        @endif
                        <input type="file" name="cert_path" id="cert_path" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('cert_path') border-red-500 @enderror"
                               accept=".pem">
                        <p class="mt-1 text-xs text-gray-500">Formato permitido: PEM. Máximo 2MB. Dejar vacío para mantener el actual.</p>
                        @error('cert_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client ID
                        </label>
                        <input type="text" name="client_id" id="client_id" value="{{ old('client_id', $company->client_id) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('client_id') border-red-500 @enderror"
                               placeholder="Ingrese el Client ID">
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-2">
                            Client Secret
                        </label>
                        <input type="password" name="client_secret" id="client_secret" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('client_secret') border-red-500 @enderror"
                               placeholder="Dejar vacío para mantener el actual">
                        <p class="mt-1 text-xs text-gray-500">Dejar vacío para mantener el secret actual.</p>
                        @error('client_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="production" value="1" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   {{ old('production', $company->production) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Modo producción</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Marque esta opción si la empresa está en modo producción.</p>
                        @error('production')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('companies.index') }}" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Empresa</span>
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
        document.getElementById('editCompanyForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Actualizando...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });
    </script>
</x-app-layout>
