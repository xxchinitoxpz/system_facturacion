<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Registro de Inventario</h1>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="editInventoryForm" method="POST" action="{{ route('inventory.update', $inventory->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información del registro -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Registro</h2>
                
                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <!-- Información del producto (solo lectura) -->
                     <div class="bg-white p-4 rounded-lg border border-gray-200">
                         <h3 class="text-md font-semibold text-gray-800 mb-3">Información del Producto</h3>
                         <div class="space-y-2">
                             <div>
                                 <label class="block text-sm font-medium text-gray-600">Producto</label>
                                 <p class="text-sm text-gray-900 font-medium">{{ $inventory->producto_nombre }}</p>
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-600">Almacén</label>
                                 <p class="text-sm text-gray-900 font-medium">{{ $inventory->almacen_nombre }}</p>
                             </div>
                         </div>
                     </div>

                     <!-- Campos editables -->
                     <div class="space-y-4">
                         <div>
                             <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                 Stock <span class="text-red-500">*</span>
                             </label>
                             <input 
                                 type="number" 
                                 id="stock" 
                                 name="stock" 
                                 value="{{ old('stock', $inventory->stock) }}"
                                 min="0"
                                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('stock') border-red-500 @enderror"
                                 placeholder="0"
                                 required
                             >
                             @error('stock')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>

                         <div>
                             <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                 Fecha de Vencimiento
                             </label>
                             <input 
                                 type="date" 
                                 id="fecha_vencimiento" 
                                 name="fecha_vencimiento" 
                                 value="{{ old('fecha_vencimiento', $inventory->fecha_vencimiento) }}"
                                 min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('fecha_vencimiento') border-red-500 @enderror"
                             >
                             @error('fecha_vencimiento')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                             <p class="mt-1 text-xs text-gray-500">Opcional. Debe ser posterior a hoy.</p>
                         </div>
                     </div>
                 </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Registro</span>
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
        // Spinner para el formulario
        document.getElementById('editInventoryForm').addEventListener('submit', function(e) {
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
