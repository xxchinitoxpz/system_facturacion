<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Empleado</h1>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="createEmployeeForm" action="{{ route('employees.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Información Personal -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre_completo') border-red-500 @enderror"
                               placeholder="Ej: Juan Pérez García"
                               required>
                        @error('nombre_completo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                               placeholder="Ej: juan.perez@empresa.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telefono') border-red-500 @enderror"
                               placeholder="Ej: 999888777"
                               required>
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700 mb-2">
                            Cargo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="cargo" name="cargo" value="{{ old('cargo') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('cargo') border-red-500 @enderror"
                               placeholder="Ej: Vendedor, Administrador, etc."
                               required>
                        @error('cargo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección <span class="text-red-500">*</span>
                    </label>
                    <textarea id="direccion" name="direccion" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('direccion') border-red-500 @enderror"
                              placeholder="Ej: Av. Principal 123, Distrito, Ciudad"
                              required>{{ old('direccion') }}</textarea>
                    @error('direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información del Documento -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Documento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_documento" name="tipo_documento" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tipo_documento') border-red-500 @enderror"
                                required>
                            <option value="">Seleccionar tipo</option>
                            <option value="DNI" {{ old('tipo_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                            <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>CE</option>
                            <option value="PASAPORTE" {{ old('tipo_documento') == 'PASAPORTE' ? 'selected' : '' }}>PASAPORTE</option>
                            <option value="RUC" {{ old('tipo_documento') == 'RUC' ? 'selected' : '' }}>RUC</option>
                        </select>
                        @error('tipo_documento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nro_documento" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Documento <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nro_documento" name="nro_documento" value="{{ old('nro_documento') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nro_documento') border-red-500 @enderror"
                               placeholder="Ej: 12345678"
                               required>
                        @error('nro_documento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información Laboral -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Laboral</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sucursal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sucursal <span class="text-red-500">*</span>
                        </label>
                        <select id="sucursal_id" name="sucursal_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sucursal_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccionar sucursal</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('sucursal_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nombre }} - {{ $branch->company->razon_social }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Ingreso <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('fecha_ingreso') border-red-500 @enderror"
                               required>
                        @error('fecha_ingreso')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        <span class="ml-2 text-sm font-medium text-gray-700">Empleado Activo</span>
                    </label>
                </div>
            </div>

            <!-- Información de Usuario -->
            <div class="bg-green-50 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-green-800">Información de Usuario (Opcional)</h3>
                    <label class="flex items-center">
                        <input type="checkbox" id="crear_usuario" name="crear_usuario" value="1" {{ old('crear_usuario') ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                        <span class="ml-2 text-sm font-medium text-gray-700">Crear usuario asociado</span>
                    </label>
                </div>
                
                <div id="usuarioFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ old('crear_usuario') ? '' : 'hidden' }}">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Usuario <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="username" name="username" value="{{ old('username') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('username') border-red-500 @enderror"
                               placeholder="Ej: usuario@empresa.com">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('password') border-red-500 @enderror"
                               placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Repite la contraseña">
                    </div>

                    <div class="flex items-end">
                        <div class="text-xs text-gray-500">
                            <p>• El usuario tendrá acceso al sistema</p>
                            <p>• Se asignará automáticamente el rol "Usuario"</p>
                            <p>• Los datos se vincularán con el empleado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('employees.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Crear Empleado</span>
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
        const crearUsuarioCheckbox = document.getElementById('crear_usuario');
        const usuarioFields = document.getElementById('usuarioFields');
        const submitText = document.getElementById('submitText');
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        // Mostrar/ocultar campos de usuario
        crearUsuarioCheckbox.addEventListener('change', function() {
            if (this.checked) {
                usuarioFields.classList.remove('hidden');
                username.required = password.required = passwordConfirmation.required = true;
                submitText.textContent = 'Crear Empleado y Usuario';
            } else {
                usuarioFields.classList.add('hidden');
                username.required = password.required = passwordConfirmation.required = false;
                username.value = password.value = passwordConfirmation.value = '';
                submitText.textContent = 'Crear Empleado';
            }
        });

        // Manejo del formulario
        document.getElementById('createEmployeeForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Creando...';
            submitSpinner.classList.remove('hidden');
        });
    </script>
</x-app-layout> 