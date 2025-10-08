<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Producto Defectuoso</h1>
            <a href="{{ route('defective-products.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="editDefectiveProductForm" method="POST" action="{{ route('defective-products.update', $defectiveProduct->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información del producto defectuoso -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Producto Defectuoso</h2>
                
                <!-- Lector de código de barras -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-md font-semibold text-blue-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                            </svg>
                            Lector de Códigos de Barras
                        </h3>
                        <div class="flex items-center gap-2 text-sm text-blue-600">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <span>Lector activo</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <input type="text" id="barcodeInput" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Escanea o ingresa el código de barras del producto">
                            <p class="text-xs text-gray-500 mt-1">Escanea el código de barras para seleccionar el producto automáticamente</p>
                        </div>
                        <button type="button" id="searchBarcodeBtn" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Buscar
                        </button>
                    </div>
                    
                    <!-- Mensaje de éxito para productos escaneados -->
                    <div id="scanSuccessMessage" class="hidden mt-3 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span id="scanSuccessText">Producto seleccionado exitosamente</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="producto_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Producto <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="producto_id" 
                            name="producto_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('producto_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('producto_id', $defectiveProduct->producto_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->nombre }} - {{ $product->category->nombre }} - {{ $product->brand->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="cantidad" 
                            name="cantidad" 
                            value="{{ old('cantidad', $defectiveProduct->cantidad) }}"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('cantidad') border-red-500 @enderror"
                            placeholder="1"
                            required
                        >
                        @error('cantidad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="estado" 
                            name="estado" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('estado') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar estado</option>
                            <option value="cambiado" {{ old('estado', $defectiveProduct->estado) == 'cambiado' ? 'selected' : '' }}>Cambiado</option>
                            <option value="almacenado" {{ old('estado', $defectiveProduct->estado) == 'almacenado' ? 'selected' : '' }}>Almacenado</option>
                            <option value="deshechado" {{ old('estado', $defectiveProduct->estado) == 'deshechado' ? 'selected' : '' }}>Deshechado</option>
                        </select>
                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('observaciones') border-red-500 @enderror"
                        placeholder="Detalles adicionales sobre el producto defectuoso..."
                    >{{ old('observaciones', $defectiveProduct->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Opcional. Máximo 500 caracteres.</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('defective-products.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Producto Defectuoso</span>
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
        const productos = @json($products);
        
        // Funcionalidad del lector de códigos de barras
        const barcodeInput = document.getElementById('barcodeInput');
        const searchBarcodeBtn = document.getElementById('searchBarcodeBtn');
        const productoSelect = document.getElementById('producto_id');

        // Buscar producto por código de barras
        function searchProductByBarcode(barcode) {
            const product = productos.find(p => p.barcode === barcode);
            if (product) {
                // Seleccionar el producto en el dropdown
                productoSelect.value = product.id;
                
                // Limpiar el campo de código de barras
                barcodeInput.value = '';
                barcodeInput.focus();
                
                // Mostrar mensaje de éxito
                showScanSuccessMessage(product.nombre);
                
                // Mostrar toast de confirmación
                mostrarMensajeExito(`Producto seleccionado: ${product.nombre}`);
            } else {
                mostrarMensajeError('Producto no encontrado. Verifica el código de barras.');
            }
        }

        // Mostrar mensaje de éxito al escanear
        function showScanSuccessMessage(productName) {
            const successMessage = document.getElementById('scanSuccessMessage');
            const successText = document.getElementById('scanSuccessText');
            
            successText.textContent = `"${productName}" seleccionado exitosamente`;
            successMessage.classList.remove('hidden');
            
            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                successMessage.classList.add('hidden');
            }, 3000);
        }

        // Event listeners para el lector de códigos de barras
        searchBarcodeBtn.addEventListener('click', function() {
            const barcode = barcodeInput.value.trim();
            if (barcode) {
                searchProductByBarcode(barcode);
            }
        });

        // Buscar al presionar Enter en el campo de código de barras
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value.trim();
                if (barcode) {
                    searchProductByBarcode(barcode);
                }
            }
        });

        // Configurar detección automática de código de barras
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
                    
                    // Si estamos en un campo de texto, verificar si es el campo de código de barras
                    const esCampoBarcode = elementoActivo.id === 'barcodeInput';
                    
                    // Procesar el código de barras si:
                    // 1. No estamos en un campo de texto, O
                    // 2. Estamos en el campo de código de barras (para permitir escaneo manual)
                    if (!esCampoTexto || esCampoBarcode) {
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
            
            // Buscar el producto por código de barras
            const product = productos.find(p => p.barcode === codigo);
            if (product) {
                // Seleccionar el producto en el dropdown
                productoSelect.value = product.id;
                
                // Mostrar mensaje de éxito
                showScanSuccessMessage(product.nombre);
                
                // Mostrar mensaje de confirmación
                mostrarMensajeExito(`Producto seleccionado: ${product.nombre}`);
            } else {
                mostrarMensajeError('Producto no encontrado. Verifica el código de barras.');
            }
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
        
        // Función para mostrar mensaje de error
        function mostrarMensajeError(mensaje) {
            // Crear un toast temporal
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = mensaje;
            document.body.appendChild(toast);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        // Inicializar el lector de códigos de barras cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            configurarLectorCodigoBarras();
        });

        // Spinner para el formulario
        document.getElementById('editDefectiveProductForm').addEventListener('submit', function(e) {
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
