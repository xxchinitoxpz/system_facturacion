<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Producto</h1>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="editProductForm" method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información del producto -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Producto</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Producto *</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            value="{{ old('nombre', $product->nombre) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                            placeholder="Ej: Cigarros Marlboro"
                            required
                        >
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Código de Barras *</label>
                        <input 
                            type="text" 
                            id="barcode" 
                            name="barcode" 
                            value="{{ old('barcode', $product->barcode) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('barcode') border-red-500 @enderror"
                            placeholder="Ej: 123456789"
                            required
                        >
                        @error('barcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                        <select 
                            id="categoria_id" 
                            name="categoria_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('categoria_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('categoria_id', $product->categoria_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-2">Marca *</label>
                        <select 
                            id="marca_id" 
                            name="marca_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('marca_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar marca</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('marca_id', $product->marca_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('descripcion') border-red-500 @enderror"
                        placeholder="Descripción detallada del producto"
                        required
                    >{{ old('descripcion', $product->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Presentaciones -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Presentaciones</h2>
                    <button 
                        type="button" 
                        onclick="addPresentation()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm"
                    >
                        + Agregar Presentación
                    </button>
                </div>

                <div id="presentaciones-container" class="space-y-4">
                    <!-- Las presentaciones existentes y nuevas se mostrarán aquí -->
                    @foreach($product->presentations as $index => $presentation)
                        <div class="bg-white p-4 rounded-lg border border-gray-200 presentation-card" data-presentation-id="{{ $presentation->id }}">
                            <!-- Campo hidden para marcar presentaciones a eliminar -->
                            <input type="hidden" name="presentaciones[{{ $index }}][id]" value="{{ $presentation->id }}">
                            <input type="hidden" name="presentaciones[{{ $index }}][_delete]" value="0" class="delete-flag">
                            
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-md font-medium text-gray-800">Presentación {{ $index + 1 }}</h3>
                                <button 
                                    type="button" 
                                    onclick="removePresentation(this)"
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition-colors"
                                >
                                    Eliminar
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                                    <input 
                                        type="text" 
                                        name="presentaciones[{{ $index }}][nombre]" 
                                        value="{{ $presentation->nombre }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Ej: Cajetilla, Unidad"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio de Venta *</label>
                                    <input 
                                        type="number" 
                                        name="presentaciones[{{ $index }}][precio_venta]" 
                                        value="{{ $presentation->precio_venta }}"
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="0.00"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidades *</label>
                                    <input 
                                        type="number" 
                                        name="presentaciones[{{ $index }}][unidades]" 
                                        value="{{ $presentation->unidades }}"
                                        min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="1"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('presentaciones')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Producto</span>
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
        let presentationIndex = {{ $product->presentations->count() }};

        function addPresentation() {
            const container = document.getElementById('presentaciones-container');
            const presentationDiv = document.createElement('div');
            presentationDiv.className = 'bg-white p-4 rounded-lg border border-gray-200 presentation-card';
            presentationDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-md font-medium text-gray-800">Presentación ${presentationIndex + 1}</h3>
                    <button 
                        type="button" 
                        onclick="removePresentation(this)"
                        class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition-colors"
                    >
                        Eliminar
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input 
                            type="text" 
                            name="presentaciones[${presentationIndex}][nombre]" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ej: Cajetilla, Unidad"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio de Venta *</label>
                        <input 
                            type="number" 
                            name="presentaciones[${presentationIndex}][precio_venta]" 
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="0.00"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidades *</label>
                        <input 
                            type="number" 
                            name="presentaciones[${presentationIndex}][unidades]" 
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="1"
                            required
                        >
                    </div>
                </div>
            `;
            container.appendChild(presentationDiv);
            presentationIndex++;
        }

        function removePresentation(button) {
            const presentationCard = button.closest('.presentation-card');
            const deleteFlag = presentationCard.querySelector('.delete-flag');
            
            // Si es una presentación existente (tiene ID), marcar para eliminar
            if (deleteFlag) {
                deleteFlag.value = '1';
                // Ocultar el card con animación
                presentationCard.style.opacity = '0.5';
                presentationCard.style.pointerEvents = 'none';
                presentationCard.style.backgroundColor = '#fee2e2';
                
                // Cambiar el botón a "Restaurar"
                button.textContent = 'Restaurar';
                button.className = 'px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm transition-colors';
                button.onclick = function() { restorePresentation(this); };
            } else {
                // Si es una presentación nueva, eliminar completamente
                presentationCard.remove();
            }
        }

        function restorePresentation(button) {
            const presentationCard = button.closest('.presentation-card');
            const deleteFlag = presentationCard.querySelector('.delete-flag');
            
            // Restaurar el card
            deleteFlag.value = '0';
            presentationCard.style.opacity = '1';
            presentationCard.style.pointerEvents = 'auto';
            presentationCard.style.backgroundColor = '';
            
            // Cambiar el botón de vuelta a "Eliminar"
            button.textContent = 'Eliminar';
            button.className = 'px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition-colors';
            button.onclick = function() { removePresentation(this); };
        }

        // Spinner para el formulario
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
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
