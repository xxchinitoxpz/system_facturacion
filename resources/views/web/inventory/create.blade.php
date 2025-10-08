<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Inventario Masivo</h1>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="createInventoryForm" method="POST" action="{{ route('inventory.store') }}" class="space-y-6">
            @csrf

            <!-- Paso 1: Seleccionar Almacén -->
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-400">
                <h2 class="text-lg font-semibold text-blue-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Paso 1: Selecciona el Almacén
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($warehouses as $warehouse)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="almacen_id" value="{{ $warehouse->id }}" 
                                   class="sr-only" required>
                            <div class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $warehouse->nombre }}</h3>
                                        @if($warehouse->descripcion)
                                            <p class="text-sm text-gray-500">{{ $warehouse->descripcion }}</p>
                                        @endif
                                    </div>
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                        <div class="w-3 h-3 bg-blue-600 rounded-full hidden"></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('almacen_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Paso 2: Agregar Productos (se muestra después de seleccionar almacén) -->
            <div id="productosSection" class="hidden">
                <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-400">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Paso 2: Agrega los Productos y sus Stocks
                        </h2>
                        <div class="flex items-center gap-2 text-sm text-green-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Lector de código de barras activo</span>
                        </div>
                    </div>
                    
                                         <div class="mb-4 flex gap-4 items-end">
                         <div class="flex-1">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Lector de Códigos de Barras</label>
                             <div class="flex gap-2">
                                 <input type="text" id="barcodeInput" 
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                        placeholder="Escanea o ingresa el código de barras">
                                 <button type="button" id="searchBarcodeBtn" 
                                         class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                     Buscar
                                 </button>
                             </div>
                             <p class="text-xs text-gray-500 mt-1">Escanea el código de barras del producto para agregarlo automáticamente</p>
                         </div>
                         <button type="button" id="addProductBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                             </svg>
                             Agregar Manualmente
                         </button>
                     </div>

                    <div id="productosContainer" class="space-y-4">
                        <!-- Los productos se agregarán dinámicamente aquí -->
                    </div>

                                         @error('productos')
                         <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                     
                     <!-- Mensaje de éxito para productos escaneados -->
                     <div id="scanSuccessMessage" class="hidden mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                         <div class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                             <span id="scanSuccessText">Producto agregado exitosamente</span>
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
                    <span id="submitText">Guardar Inventario</span>
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
        let productCounter = 0;

                 // Mostrar sección de productos cuando se selecciona un almacén
         document.querySelectorAll('input[name="almacen_id"]').forEach(radio => {
             radio.addEventListener('change', function() {
                 document.getElementById('productosSection').classList.remove('hidden');
                 // Agregar el primer producto automáticamente
                 if (document.querySelectorAll('.producto-item').length === 0) {
                     addProduct();
                 }
                 
                 // Quitar el focus del radio button para que funcione el escáner
                 this.blur();
                 
                 // Configurar detección automática de código de barras cuando se selecciona almacén
                 configurarLectorCodigoBarras();
             });
         });

        // Función para agregar producto
        function addProduct() {
            const container = document.getElementById('productosContainer');
            const productDiv = document.createElement('div');
            productDiv.className = 'producto-item bg-white p-4 rounded-lg border border-gray-200';
            productDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Producto #${productCounter + 1}</h4>
                    <button type="button" onclick="removeProduct(this)" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Producto <span class="text-red-500">*</span></label>
                        <select name="productos[${productCounter}][producto_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Seleccionar producto</option>
                            ${productos.map(product => `
                                <option value="${product.id}">${product.nombre} - ${product.category.nombre} - ${product.brand.nombre}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="productos[${productCounter}][stock]" min="0" value="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Vencimiento</label>
                        <input type="date" name="productos[${productCounter}][fecha_vencimiento]" 
                               min="${new Date(Date.now() + 86400000).toISOString().split('T')[0]}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Opcional</p>
                    </div>
                </div>
            `;
            container.appendChild(productDiv);
            productCounter++;
        }

        // Función para remover producto
        function removeProduct(button) {
            button.closest('.producto-item').remove();
            updateProductNumbers();
        }

        // Actualizar números de productos
        function updateProductNumbers() {
            document.querySelectorAll('.producto-item').forEach((item, index) => {
                item.querySelector('h4').textContent = `Producto #${index + 1}`;
            });
        }

                 // Agregar producto al hacer clic en el botón
         document.getElementById('addProductBtn').addEventListener('click', addProduct);

         // Funcionalidad del lector de códigos de barras
         const barcodeInput = document.getElementById('barcodeInput');
         const searchBarcodeBtn = document.getElementById('searchBarcodeBtn');

         // Buscar producto por código de barras
         function searchProductByBarcode(barcode) {
             const product = productos.find(p => p.barcode === barcode);
             if (product) {
                 // Verificar si el producto ya está agregado
                 const existingProduct = document.querySelector(`select[name*="producto_id"] option[value="${product.id}"]:checked`);
                 if (existingProduct) {
                     alert('Este producto ya está agregado al inventario.');
                     return;
                 }
                 
                 // Agregar el producto automáticamente
                 addProductWithData(product);
                 barcodeInput.value = '';
                 barcodeInput.focus();
                 
                 // Mostrar mensaje de éxito
                 showScanSuccessMessage(product.nombre);
             } else {
                 alert('Producto no encontrado. Verifica el código de barras.');
             }
         }

         // Mostrar mensaje de éxito al escanear
         function showScanSuccessMessage(productName) {
             const successMessage = document.getElementById('scanSuccessMessage');
             const successText = document.getElementById('scanSuccessText');
             
             successText.textContent = `"${productName}" agregado exitosamente`;
             successMessage.classList.remove('hidden');
             
             // Ocultar el mensaje después de 3 segundos
             setTimeout(() => {
                 successMessage.classList.add('hidden');
             }, 3000);
         }

         // Agregar producto con datos pre-cargados
         function addProductWithData(product) {
             const container = document.getElementById('productosContainer');
             const productDiv = document.createElement('div');
             productDiv.className = 'producto-item bg-white p-4 rounded-lg border border-gray-200';
             productDiv.innerHTML = `
                 <div class="flex items-center justify-between mb-3">
                     <h4 class="font-medium text-gray-900">Producto #${productCounter + 1}</h4>
                     <button type="button" onclick="removeProduct(this)" class="text-red-600 hover:text-red-800">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                     </button>
                 </div>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Producto <span class="text-red-500">*</span></label>
                         <select name="productos[${productCounter}][producto_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                             <option value="">Seleccionar producto</option>
                             ${productos.map(p => `
                                 <option value="${p.id}" ${p.id === product.id ? 'selected' : ''}>${p.nombre} - ${p.category.nombre} - ${p.brand.nombre}</option>
                             `).join('')}
                         </select>
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Stock <span class="text-red-500">*</span></label>
                         <input type="number" name="productos[${productCounter}][stock]" min="0" value="0" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="0" required>
                     </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Vencimiento</label>
                         <input type="date" name="productos[${productCounter}][fecha_vencimiento]" 
                                min="${new Date(Date.now() + 86400000).toISOString().split('T')[0]}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                         <p class="text-xs text-gray-500 mt-1">Opcional</p>
                     </div>
                 </div>
             `;
             container.appendChild(productDiv);
             productCounter++;
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

         // Configurar detección automática de código de barras cuando se selecciona un almacén
         // (sin focus automático en el input)
         
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
             
             // Verificar que se haya seleccionado un almacén
             const almacenSeleccionado = document.querySelector('input[name="almacen_id"]:checked');
             if (!almacenSeleccionado) {
                 mostrarMensajeError('Primero debes seleccionar un almacén');
                 return;
             }
             
             // Verificar que la sección de productos esté visible
             const productosSection = document.getElementById('productosSection');
             if (productosSection.classList.contains('hidden')) {
                 productosSection.classList.remove('hidden');
             }
             
             // Buscar el producto por código de barras
             const product = productos.find(p => p.barcode === codigo);
             if (product) {
                 // Verificar si el producto ya está agregado
                 const existingProduct = document.querySelector(`select[name*="producto_id"] option[value="${product.id}"]:checked`);
                 if (existingProduct) {
                     mostrarMensajeError('Este producto ya está agregado al inventario');
                     return;
                 }
                 
                 // Buscar un card vacío para usar primero
                 const cardsVacios = document.querySelectorAll('.producto-item');
                 let cardVacioEncontrado = false;
                 
                 for (let card of cardsVacios) {
                     const selectProducto = card.querySelector('select[name*="producto_id"]');
                     if (selectProducto && selectProducto.value === '') {
                         // Usar este card vacío
                         fillProductCard(card, product);
                         cardVacioEncontrado = true;
                         break;
                     }
                 }
                 
                 // Si no hay card vacío, crear uno nuevo
                 if (!cardVacioEncontrado) {
                     addProductWithData(product);
                 }
                 
                 // Mostrar mensaje de éxito
                 showScanSuccessMessage(product.nombre);
                 
                 // Mostrar mensaje de confirmación
                 mostrarMensajeExito(`Producto agregado: ${product.nombre}`);
             } else {
                 mostrarMensajeError('Producto no encontrado. Verifica el código de barras.');
             }
         }
         
         // Función para llenar un card existente con datos del producto
         function fillProductCard(card, product) {
             const selectProducto = card.querySelector('select[name*="producto_id"]');
             if (selectProducto) {
                 selectProducto.value = product.id;
             }
             
             // Opcional: establecer un stock por defecto de 1
             const inputStock = card.querySelector('input[name*="stock"]');
             if (inputStock && inputStock.value === '0') {
                 inputStock.value = '1';
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

        // Spinner para el formulario
        document.getElementById('createInventoryForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Guardando...';
            submitSpinner.classList.remove('hidden');
        });

                 // Estilo para los radio buttons de almacén
         document.querySelectorAll('input[name="almacen_id"]').forEach(radio => {
             radio.addEventListener('change', function() {
                 // Remover selección de todos
                 document.querySelectorAll('input[name="almacen_id"]').forEach(r => {
                     const label = r.closest('label');
                     const card = label.querySelector('.border-2');
                     const dot = label.querySelector('.bg-blue-600');
                     
                     card.classList.remove('border-blue-300');
                     card.classList.add('border-gray-200');
                     dot.classList.add('hidden');
                 });
                 
                 // Seleccionar el actual
                 const currentLabel = this.closest('label');
                 const currentCard = currentLabel.querySelector('.border-2');
                 const currentDot = currentLabel.querySelector('.bg-blue-600');
                 
                 currentCard.classList.remove('border-gray-200');
                 currentCard.classList.add('border-blue-300');
                 currentDot.classList.remove('hidden');
             });
         });
    </script>
</x-app-layout>
