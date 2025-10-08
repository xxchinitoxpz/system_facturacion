<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Combo</h1>
            <a href="{{ route('combos.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="createComboForm" method="POST" action="{{ route('combos.store') }}" class="space-y-6">
            @csrf

            <!-- Información del combo -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Combo</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Combo *</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            value="{{ old('nombre') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                            placeholder="Ej: Combo Familiar"
                            required
                        >
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">Precio del Combo *</label>
                        <input 
                            type="number" 
                            id="precio" 
                            name="precio" 
                            value="{{ old('precio') }}"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('precio') border-red-500 @enderror"
                            placeholder="0.00"
                            required
                        >
                        @error('precio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="estado" 
                            value="1"
                            {{ old('estado') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2"
                        >
                        <span class="ml-2 text-sm font-medium text-gray-700">Combo Activo</span>
                    </label>
                </div>
            </div>

                         <!-- Productos del combo -->
             <div class="bg-gray-50 p-4 rounded-lg">
                 <div class="flex items-center justify-between mb-4">
                     <h2 class="text-lg font-semibold text-gray-800">Productos del Combo</h2>
                 </div>
                 
                                   <div class="mb-4 flex gap-4">
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
                      <div class="flex items-end">
                          <button type="button" id="addProductBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                              </svg>
                              Agregar Manualmente
                          </button>
                      </div>
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

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('combos.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <span id="submitText">Crear Combo</span>
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

    <!-- Template para agregar productos -->
    <template id="productTemplate">
        <div class="producto-item border border-gray-200 rounded-lg p-4 bg-white">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-md font-medium text-gray-800">Producto</h3>
                <button type="button" class="removeProductBtn text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Producto *</label>
                    <select 
                        name="productos[INDEX][producto_id]"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                        <option value="">Seleccionar producto</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-precio="{{ $product->presentations->first()->precio_venta ?? 0 }}">
                                {{ $product->nombre }} - {{ $product->category->nombre }} - {{ $product->brand->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad *</label>
                    <input 
                        type="number" 
                        name="productos[INDEX][cantidad]"
                        min="1"
                        value="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="1"
                        required
                    >
                </div>
            </div>
        </div>
    </template>

         <script>
         const productos = @json($products);
         
         document.addEventListener('DOMContentLoaded', function() {
             const addProductBtn = document.getElementById('addProductBtn');
             const productosContainer = document.getElementById('productosContainer');
             const productTemplate = document.getElementById('productTemplate');
             const barcodeInput = document.getElementById('barcodeInput');
             const searchBarcodeBtn = document.getElementById('searchBarcodeBtn');
             let productIndex = 0;

             // Agregar primer producto por defecto
             addProduct();

             addProductBtn.addEventListener('click', addProduct);

            function addProduct() {
                const template = productTemplate.innerHTML.replace(/INDEX/g, productIndex);
                const productDiv = document.createElement('div');
                productDiv.innerHTML = template;
                productosContainer.appendChild(productDiv);

                // Agregar event listener para eliminar
                const removeBtn = productDiv.querySelector('.removeProductBtn');
                removeBtn.addEventListener('click', function() {
                    if (productosContainer.children.length > 1) {
                        productDiv.remove();
                    } else {
                        alert('Debe tener al menos un producto en el combo.');
                    }
                });

                productIndex++;
            }

                         // Validación del formulario
             document.getElementById('createComboForm').addEventListener('submit', function(e) {
                 const productos = productosContainer.querySelectorAll('.producto-item');
                 let isValid = true;

                 productos.forEach((producto, index) => {
                     const productoSelect = producto.querySelector('select[name*="[producto_id]"]');
                     const cantidadInput = producto.querySelector('input[name*="[cantidad]"]');

                     if (!productoSelect.value || !cantidadInput.value || cantidadInput.value < 1) {
                         isValid = false;
                     }
                 });

                 if (!isValid) {
                     e.preventDefault();
                     alert('Por favor complete todos los campos requeridos para cada producto.');
                     return;
                 }

                 // Spinner para el formulario
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

             // Funcionalidad del lector de códigos de barras
             
             // Buscar producto por código de barras
             function searchProductByBarcode(barcode) {
                 const product = productos.find(p => p.barcode === barcode);
                 if (product) {
                     // Verificar si el producto ya está agregado
                     const existingProduct = document.querySelector(`select[name*="producto_id"] option[value="${product.id}"]:checked`);
                     if (existingProduct) {
                         alert('Este producto ya está agregado al combo.');
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
                 const template = productTemplate.innerHTML.replace(/INDEX/g, productIndex);
                 const productDiv = document.createElement('div');
                 productDiv.innerHTML = template;
                 productosContainer.appendChild(productDiv);

                 // Seleccionar el producto automáticamente
                 const select = productDiv.querySelector('select[name*="[producto_id]"]');
                 select.value = product.id;

                 // Agregar event listener para eliminar
                 const removeBtn = productDiv.querySelector('.removeProductBtn');
                 removeBtn.addEventListener('click', function() {
                     if (productosContainer.children.length > 1) {
                         productDiv.remove();
                     } else {
                         alert('Debe tener al menos un producto en el combo.');
                     }
                 });

                 productIndex++;
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

             // Auto-focus en el campo de código de barras al cargar la página
             setTimeout(() => {
                 barcodeInput.focus();
             }, 100);
         });
     </script>
 </x-app-layout>
