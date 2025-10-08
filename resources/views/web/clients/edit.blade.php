<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Cliente: {{ $client->nombre_completo }}</h1>
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="editClientForm" method="POST" action="{{ route('clients.update', $client->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información del cliente -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Cliente</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" 
                               value="{{ old('nombre_completo', $client->nombre_completo) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre_completo') border-red-500 @enderror"
                               placeholder="Ej: Juan Pérez García"
                               required>
                        @error('nombre_completo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento *</label>
                            <select id="tipo_documento" name="tipo_documento"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tipo_documento') border-red-500 @enderror"
                                    required>
                                <option value="">Seleccionar tipo</option>
                                <option value="DNI" {{ old('tipo_documento', $client->tipo_documento) == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="RUC" {{ old('tipo_documento', $client->tipo_documento) == 'RUC' ? 'selected' : '' }}>RUC</option>
                                <option value="CE" {{ old('tipo_documento', $client->tipo_documento) == 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="PASAPORTE" {{ old('tipo_documento', $client->tipo_documento) == 'PASAPORTE' ? 'selected' : '' }}>PASAPORTE</option>
                            </select>
                            @error('tipo_documento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nro_documento" class="block text-sm font-medium text-gray-700 mb-2">Número de Documento *</label>
                            <input type="text" id="nro_documento" name="nro_documento" 
                                   value="{{ old('nro_documento', $client->nro_documento) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nro_documento') border-red-500 @enderror"
                                   placeholder="Ej: 12345678"
                                   required>
                            @error('nro_documento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" 
                               value="{{ old('telefono', $client->telefono) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telefono') border-red-500 @enderror"
                               placeholder="Ej: 999888777">
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" 
                               value="{{ old('email', $client->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                               placeholder="Ej: juan.perez@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                        <textarea id="direccion" name="direccion" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('direccion') border-red-500 @enderror"
                                  placeholder="Ej: Av. Arequipa 123, Lima">{{ old('direccion', $client->direccion) }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1"
                               {{ old('activo', $client->activo) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                        <span class="ml-2 text-sm font-medium text-gray-700">Cliente activo</span>
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('clients.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Cliente</span>
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
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Actualizando...';
            submitSpinner.classList.remove('hidden');
        });
    </script>
</x-app-layout>
