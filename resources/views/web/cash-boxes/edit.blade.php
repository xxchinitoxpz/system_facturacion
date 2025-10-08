<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Caja: {{ $cashBox->nombre }}</h1>
            <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="editCashBoxForm" action="{{ route('cash-boxes.update', $cashBox->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Información de la Caja -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Caja</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Caja <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            value="{{ old('nombre', $cashBox->nombre) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                            placeholder="Ej: Caja Principal, Caja 2, etc."
                            required
                        >
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sucursal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sucursal <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="sucursal_id" 
                            name="sucursal_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sucursal_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar sucursal</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('sucursal_id', $cashBox->sucursal_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('descripcion') border-red-500 @enderror"
                        placeholder="Descripción opcional de la caja..."
                    >{{ old('descripcion', $cashBox->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Adicional</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado Actual</label>
                        <div class="flex items-center gap-2">
                            @if($cashBox->activeSession)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                    Abierta
                                </span>
                                <span class="text-sm text-gray-500">
                                    Sesión activa desde {{ $cashBox->activeSession->fecha_hora_apertura->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                    Cerrada
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal Actual</label>
                        <p class="text-sm text-gray-600">{{ $cashBox->branch->nombre }}</p>
                    </div>
                </div>

                @if($cashBox->activeSession)
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-600 font-bold">Nota:</span>
                            <p class="text-sm text-yellow-800">
                                Esta caja tiene una sesión activa. Los cambios se aplicarán pero no afectarán la sesión actual.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Caja</span>
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
        document.getElementById('editCashBoxForm').addEventListener('submit', function(e) {
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
