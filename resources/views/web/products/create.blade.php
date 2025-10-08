<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Producto</h1>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="createProductForm" method="POST" action="{{ route('products.store') }}" class="space-y-6">
            @csrf

            <!-- Información del producto -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Información del Producto</h2>
                    <div class="flex items-center gap-2 text-sm text-green-600">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Lector de código de barras activo</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Producto *</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            value="{{ old('nombre') }}"
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
                            value="{{ old('barcode') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('barcode') border-red-500 @enderror"
                            placeholder="Ej: 123456789"
                            required
                            autocomplete="off"
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
                             class="select2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('categoria_id') border-red-500 @enderror"
                             required
                         >
                             <option value="">Seleccionar categoría</option>
                             @foreach($categories as $category)
                                 <option value="{{ $category->id }}" {{ old('categoria_id') == $category->id ? 'selected' : '' }}>
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
                             class="select2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('marca_id') border-red-500 @enderror"
                             required
                         >
                             <option value="">Seleccionar marca</option>
                             @foreach($brands as $brand)
                                 <option value="{{ $brand->id }}" {{ old('marca_id') == $brand->id ? 'selected' : '' }}>
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
                    >{{ old('descripcion') }}</textarea>
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
                    <!-- Las presentaciones se agregarán dinámicamente aquí -->
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
                    <span id="submitText">Crear Producto</span>
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
        let presentationIndex = 0;

        function addPresentation() {
            const container = document.getElementById('presentaciones-container');
            const presentationDiv = document.createElement('div');
            presentationDiv.className = 'bg-white p-4 rounded-lg border border-gray-200';
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
            // Buscar el contenedor principal de la presentación (el div con clase bg-white)
            const presentationCard = button.closest('.bg-white');
            if (presentationCard) {
                presentationCard.remove();
            }
        }

        // Agregar una presentación por defecto al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            addPresentation();
            
            // Prevenir envío del formulario cuando se presiona Enter en el campo de código de barras
            const barcodeInput = document.getElementById('barcode');
            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Opcional: mover el foco al siguiente campo
                    document.getElementById('nombre').focus();
                }
            });
            
            // Configurar detección automática de código de barras
            configurarLectorCodigoBarras();
        });
        
        // Función para configurar la detección automática de código de barras
        function configurarLectorCodigoBarras() {
            let codigoBarrasBuffer = '';
            let ultimaTeclaTiempo = 0;
            const TIEMPO_ENTRE_TECLAS = 50; // milisegundos entre teclas del scanner
            
            // Escuchar todas las teclas en el documento
            document.addEventListener('keydown', function(e) {
                const tiempoActual = new Date().getTime();
                
                // Si es la primera tecla o han pasado más de 50ms, reiniciar el buffer
                if (tiempoActual - ultimaTeclaTiempo > TIEMPO_ENTRE_TECLAS) {
                    codigoBarrasBuffer = '';
                }
                
                ultimaTeclaTiempo = tiempoActual;
                
                // Agregar la tecla al buffer (excepto Enter)
                if (e.key !== 'Enter') {
                    codigoBarrasBuffer += e.key;
                }
                
                // Si se presiona Enter, procesar el código de barras
                if (e.key === 'Enter' && codigoBarrasBuffer.length > 0) {
                    e.preventDefault(); // Prevenir el comportamiento por defecto
                    
                    // Verificar que no estemos en un campo de texto activo
                    const elementoActivo = document.activeElement;
                    const esCampoTexto = elementoActivo.tagName === 'INPUT' || 
                                       elementoActivo.tagName === 'TEXTAREA' || 
                                       elementoActivo.contentEditable === 'true';
                    
                    // Si no estamos en un campo de texto, procesar el código de barras
                    if (!esCampoTexto) {
                        procesarCodigoBarras(codigoBarrasBuffer);
                    }
                    
                    // Limpiar el buffer
                    codigoBarrasBuffer = '';
                }
            });
        }
        
        // Función para procesar el código de barras escaneado
        function procesarCodigoBarras(codigo) {
            console.log('Código de barras detectado:', codigo);
            
            // Establecer el código de barras en el campo correspondiente
            const campoBarcode = document.getElementById('barcode');
            campoBarcode.value = codigo;
            
            // Mover el foco al campo de nombre para facilitar la entrada de datos
            document.getElementById('nombre').focus();
            
            // Mostrar mensaje de confirmación
            mostrarMensajeExito(`Código de barras escaneado: ${codigo}`);
        }
        
        // Función para mostrar mensaje de éxito
        function mostrarMensajeExito(mensaje) {
            // Crear un toast temporal
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = mensaje;
            document.body.appendChild(toast);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        // Spinner para el formulario
        document.getElementById('createProductForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Creando...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });
    </script>
</x-app-layout>
