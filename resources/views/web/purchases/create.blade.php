<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Compra</h1>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="createPurchaseForm" method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Información General y del Proveedor -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General y del Proveedor</h2>
                
                                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                     <div>
                         <label for="proveedor_documento" class="block text-sm font-medium text-gray-700 mb-2">Documento del Proveedor *</label>
                         <div class="relative">
                             <input 
                                 type="text" 
                                 id="proveedor_documento" 
                                 name="proveedor_documento"
                                 value="{{ old('proveedor_documento', '') }}"
                                 class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('proveedor_documento') border-red-500 @enderror"
                                 placeholder="Digite DNI o RUC"
                                 required
                             >
                             <div id="proveedor_spinner" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                                 <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                 </svg>
                             </div>
                         </div>
                         <input type="hidden" id="proveedor_id" name="proveedor_id" value="{{ old('proveedor_id', '') }}">
                         @error('proveedor_documento')
                             <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                         @enderror
                     </div>

                     <div>
                         <label for="proveedor_nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Proveedor</label>
                         <input 
                             type="text" 
                             id="proveedor_nombre" 
                             name="proveedor_nombre"
                             value="{{ old('proveedor_nombre', '') }}"
                             class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                             placeholder="Se obtendrá automáticamente del documento"
                             readonly
                         >
                     </div>

                    @if(auth()->user()->sucursal_id === null)
                        <!-- Solo mostrar select de sucursal para administradores -->
                        <div>
                            <label for="sucursal_id" class="block text-sm font-medium text-gray-700 mb-2">Sucursal *</label>
                            <select 
                                id="sucursal_id" 
                                name="sucursal_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sucursal_id') border-red-500 @enderror"
                                required
                            >
                                <option value="">Seleccionar sucursal</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('sucursal_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <!-- Para empleados, mostrar sucursal fija y campo oculto -->
                        <div>
                            <label for="sucursal_display" class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                            <input 
                                type="text" 
                                id="sucursal_display" 
                                value="{{ auth()->user()->branch->nombre ?? 'Sucursal no asignada' }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly
                            >
                            <input type="hidden" id="sucursal_id_hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id }}">
                        </div>
                    @endif

                    <div>
                        <label for="comprobante_path" class="block text-sm font-medium text-gray-700 mb-2">Comprobante (opcional)</label>
                        <input 
                            type="file" 
                            id="comprobante_path" 
                            name="comprobante_path"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('comprobante_path') border-red-500 @enderror"
                        >
                        @error('comprobante_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total" class="block text-sm font-medium text-gray-700 mb-2">Total de la Compra</label>
                        <input 
                            type="number" 
                            id="total" 
                            name="total" 
                            step="0.01" 
                            min="0" 
                            value="0.00"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                            placeholder="0.00"
                            required
                            readonly
                        >
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('observaciones') border-red-500 @enderror"
                        placeholder="Observaciones adicionales sobre la compra..."
                    >{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Layout de dos columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- COLUMNA IZQUIERDA: Productos (más grande) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Sección de Productos -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Productos</h2>
                            <div class="flex items-center gap-2 text-sm text-green-600">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span>Lector de código de barras activo (global)</span>
                                <div id="barcode-status" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Listo</div>
                            </div>
                        </div>
                        
                        <!-- Controles para agregar productos -->
                        <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-1">
                                    <label for="tipo_item" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Item</label>
                                    <select id="tipo_item" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="producto">Producto</option>
                                        <option value="presentacion">Presentación</option>
                                    </select>
                                </div>
                                
                                <div id="producto_select" class="producto-field md:col-span-3">
                                    <label for="buscar_producto" class="block text-sm font-medium text-gray-700 mb-2">Buscar Producto</label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="buscar_producto" 
                                            placeholder="Buscar por nombre o código de barras..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                        <div id="resultados_busqueda" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            <!-- Los resultados se mostrarán aquí -->
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="presentacion_select" class="presentacion-field hidden md:col-span-3">
                                    <label for="buscar_presentacion" class="block text-sm font-medium text-gray-700 mb-2">Buscar Presentación</label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="buscar_presentacion" 
                                            placeholder="Buscar presentación por nombre..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                        <div id="resultados_busqueda_presentacion" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            <!-- Los resultados de presentaciones se mostrarán aquí -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Presentaciones del Producto Seleccionado -->
                        <div id="presentaciones_producto" class="mb-4 p-4 bg-white border border-gray-200 rounded-lg hidden">
                            <h3 class="text-md font-semibold text-gray-800 mb-3">Presentaciones Disponibles</h3>
                            <div id="lista_presentaciones" class="space-y-2">
                                <!-- Las presentaciones se mostrarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Tabla de productos agregados -->
                        <div class="overflow-x-auto">
                            <table id="tabla_productos" class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-indigo-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="productos_agregados" class="divide-y divide-gray-200">
                                    <!-- Los productos se agregarán dinámicamente aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Campos ocultos para enviar los productos -->
                    <div id="productos_data" style="display: none;">
                        <!-- Los datos de productos se agregarán aquí dinámicamente -->
                    </div>
                </div>
                
                <!-- COLUMNA DERECHA: Información del Producto Seleccionado -->
                <div class="space-y-6">
                    
                    <!-- Información del Producto Seleccionado -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalles del Producto</h2>
                        
                        <div id="producto_info" class="space-y-4 hidden">
                            <!-- Información del producto seleccionado -->
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <div id="producto_nombre" class="font-semibold text-gray-800 mb-2"></div>
                                <div id="producto_categoria" class="text-sm text-gray-600 mb-2"></div>
                                <div id="producto_marca" class="text-sm text-gray-600 mb-4"></div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="cantidad_producto" class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                                        <input 
                                            type="number" 
                                            id="cantidad_producto" 
                                            min="1" 
                                            value="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            onchange="calcularSubtotal()"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="precio_unitario" class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario *</label>
                                        <input 
                                            type="number" 
                                            id="precio_unitario" 
                                            step="0.01" 
                                            min="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            onchange="calcularSubtotal()"
                                        >
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                                    <input 
                                        type="date" 
                                        id="fecha_vencimiento"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                </div>
                                
                                <div class="mt-4">
                                    <label for="subtotal_producto" class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                    <input 
                                        type="number" 
                                        id="subtotal_producto" 
                                        step="0.01" 
                                        min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                        readonly
                                    >
                                </div>
                                
                                                                 <div class="mt-4">
                                     <button 
                                         type="button" 
                                         id="agregar_producto_btn"
                                         class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                                     >
                                         Agregar Producto
                                     </button>
                                 </div>
                            </div>
                        </div>
                        
                        <div id="sin_producto" class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                            <p>Busca un producto para agregarlo a la compra</p>
                        </div>
                    </div>
                </div>
            </div>

                         <!-- Botones -->
             <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                 <a href="{{ route('purchases.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                     Cancelar
                 </a>
                 <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                     <span id="submitText">Crear Compra</span>
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
                 let productosAgregados = [];
         let contadorProductos = 0;
         let productoSeleccionado = null;
         let products = JSON.parse('{!! json_encode($products) !!}');
         let suppliers = JSON.parse('{!! json_encode($suppliers) !!}');

        // Cambiar entre producto y presentación
        document.getElementById('tipo_item').addEventListener('change', function() {
            const tipo = this.value;
            const productoSelect = document.getElementById('producto_select');
            const presentacionSelect = document.getElementById('presentacion_select');
            
            if (tipo === 'producto') {
                productoSelect.classList.remove('hidden');
                presentacionSelect.classList.add('hidden');
                document.getElementById('buscar_presentacion').value = '';
                document.getElementById('resultados_busqueda_presentacion').classList.add('hidden');
                document.getElementById('buscar_producto').value = '';
                productoSeleccionado = null;
            } else {
                productoSelect.classList.add('hidden');
                presentacionSelect.classList.remove('hidden');
                document.getElementById('buscar_producto').value = '';
                document.getElementById('resultados_busqueda').classList.add('hidden');
                productoSeleccionado = null;
            }
        });

        // Búsqueda de productos
        let timeoutBusqueda;
        document.getElementById('buscar_producto').addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            const query = this.value.trim();
            
            if (query.length < 2) {
                document.getElementById('resultados_busqueda').classList.add('hidden');
                return;
            }
            
            timeoutBusqueda = setTimeout(() => {
                buscarProductos(query);
            }, 300);
        });

        // Detectar código de barras en el campo de búsqueda
        document.getElementById('buscar_producto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                
                // Si parece ser un código de barras
                if (query.length >= 8 && query.length <= 13 && /^\d+$/.test(query)) {
                    const productoEncontrado = products.find(product => 
                        product.barcode && product.barcode === query
                    );
                    
                    if (productoEncontrado) {
                        seleccionarProducto(productoEncontrado);
                        this.value = '';
                    } else {
                        alert('Producto no encontrado con el código de barras: ' + query);
                        this.value = '';
                    }
                } else {
                    // Buscar normalmente
                    const resultados = document.getElementById('resultados_busqueda');
                    if (!resultados.classList.contains('hidden')) {
                        const primerResultado = resultados.querySelector('div');
                        if (primerResultado) {
                            primerResultado.click();
                        }
                    }
                }
            }
        });

        // Búsqueda de presentaciones
        document.getElementById('buscar_presentacion').addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            const query = this.value.trim();
            
            if (query.length < 2) {
                document.getElementById('resultados_busqueda_presentacion').classList.add('hidden');
                return;
            }
            
            timeoutBusqueda = setTimeout(() => {
                buscarPresentaciones(query);
            }, 300);
        });

        // Prevenir envío del formulario con Enter en el campo de búsqueda
        document.getElementById('buscar_producto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        document.getElementById('buscar_presentacion').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                const resultados = document.getElementById('resultados_busqueda_presentacion');
                if (!resultados.classList.contains('hidden')) {
                    const primerResultado = resultados.querySelector('div');
                    if (primerResultado) {
                        primerResultado.click();
                    }
                }
            }
        });

        // Función para buscar productos
        function buscarProductos(query) {
            const resultados = document.getElementById('resultados_busqueda');
            resultados.innerHTML = '';
            
            const productosFiltrados = products.filter(product => 
                product.nombre.toLowerCase().includes(query.toLowerCase()) ||
                (product.barcode && product.barcode.includes(query))
            );
            
            if (productosFiltrados.length === 0) {
                resultados.innerHTML = '<div class="p-3 text-gray-500">No se encontraron productos</div>';
            } else {
                // Si es un código de barras y hay exactamente un resultado que coincide, seleccionarlo automáticamente
                if (esCodigoBarras(query) && productosFiltrados.length === 1 && productosFiltrados[0].barcode === query) {
                    seleccionarProducto(productosFiltrados[0]);
                    return;
                }
                
                productosFiltrados.forEach(product => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
                    div.innerHTML = `
                        <div class="font-medium text-gray-900">${product.nombre}</div>
                        <div class="text-sm text-gray-600">
                            ${product.category ? product.category.nombre : ''} 
                            ${product.brand ? '- ' + product.brand.nombre : ''}
                            ${product.barcode ? ' | Código: ' + product.barcode : ''}
                        </div>
                    `;
                    div.addEventListener('click', () => seleccionarProducto(product));
                    resultados.appendChild(div);
                });
            }
            
            resultados.classList.remove('hidden');
        }

        // Función para buscar presentaciones
        function buscarPresentaciones(query) {
            const resultados = document.getElementById('resultados_busqueda_presentacion');
            resultados.innerHTML = '';
            
            // Filtrar productos que tengan presentaciones
            const productosConPresentaciones = products.filter(product => 
                product.presentations && product.presentations.length > 0
            );
            
            let presentacionesFiltradas = [];
            productosConPresentaciones.forEach(product => {
                product.presentations.forEach(presentation => {
                    if (presentation.nombre.toLowerCase().includes(query.toLowerCase())) {
                        presentacionesFiltradas.push({
                            ...presentation,
                            producto: product
                        });
                    }
                });
            });
            
            if (presentacionesFiltradas.length === 0) {
                resultados.innerHTML = '<div class="p-3 text-gray-500">No se encontraron presentaciones</div>';
            } else {
                presentacionesFiltradas.forEach(presentation => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
                    div.innerHTML = `
                        <div class="font-medium text-gray-900">${presentation.nombre}</div>
                        <div class="text-sm text-gray-600">
                            ${presentation.producto.nombre} | ${presentation.unidades} unidades
                        </div>
                    `;
                    div.addEventListener('click', () => seleccionarPresentacion(presentation));
                    resultados.appendChild(div);
                });
            }
            
            resultados.classList.remove('hidden');
        }

        // Función para seleccionar producto
        function seleccionarProducto(product) {
            productoSeleccionado = {
                tipo: 'producto',
                id: product.id,
                nombre: product.nombre,
                categoria: product.category ? product.category.nombre : '',
                marca: product.brand ? product.brand.nombre : '',
                barcode: product.barcode
            };
            
            mostrarProductoInfo();
            document.getElementById('resultados_busqueda').classList.add('hidden');
            document.getElementById('buscar_producto').value = `${product.nombre} (${product.barcode})`;
        }

        // Función para seleccionar presentación
        function seleccionarPresentacion(presentation) {
            productoSeleccionado = {
                tipo: 'presentacion',
                id: presentation.id,
                nombre: presentation.nombre,
                producto_id: presentation.producto_id,
                producto_nombre: presentation.producto.nombre,
                categoria: presentation.producto.category ? presentation.producto.category.nombre : '',
                marca: presentation.producto.brand ? presentation.producto.brand.nombre : '',
                unidades: presentation.unidades
            };
            
            mostrarProductoInfo();
            document.getElementById('resultados_busqueda_presentacion').classList.add('hidden');
            document.getElementById('buscar_presentacion').value = presentation.nombre;
        }

        // Función para mostrar información del producto
        function mostrarProductoInfo() {
            const productoInfo = document.getElementById('producto_info');
            const sinProducto = document.getElementById('sin_producto');
            
            if (productoSeleccionado) {
                productoInfo.classList.remove('hidden');
                sinProducto.classList.add('hidden');
                
                document.getElementById('producto_nombre').textContent = productoSeleccionado.nombre;
                document.getElementById('producto_categoria').textContent = productoSeleccionado.categoria;
                document.getElementById('producto_marca').textContent = productoSeleccionado.marca;
                
                // Limpiar campos
                document.getElementById('cantidad_producto').value = '1';
                document.getElementById('precio_unitario').value = '';
                document.getElementById('fecha_vencimiento').value = '';
                document.getElementById('subtotal_producto').value = '0.00';
            }
        }

        // Función para calcular subtotal
        function calcularSubtotal() {
            const cantidad = parseFloat(document.getElementById('cantidad_producto').value) || 0;
            const precio = parseFloat(document.getElementById('precio_unitario').value) || 0;
            const subtotal = cantidad * precio;
            
            document.getElementById('subtotal_producto').value = subtotal.toFixed(2);
        }

        // Función para agregar producto
        document.getElementById('agregar_producto_btn').addEventListener('click', function() {
            if (!productoSeleccionado) {
                alert('Debe seleccionar un producto primero');
                return;
            }
            
            const cantidad = parseFloat(document.getElementById('cantidad_producto').value);
            const precio = parseFloat(document.getElementById('precio_unitario').value);
            const fechaVencimiento = document.getElementById('fecha_vencimiento').value;
            
            if (!cantidad || cantidad <= 0) {
                alert('Debe ingresar una cantidad válida');
                return;
            }
            
            if (!precio || precio <= 0) {
                alert('Debe ingresar un precio válido');
                return;
            }
            
            const subtotal = cantidad * precio;
            
            const producto = {
                id: contadorProductos++,
                tipo: productoSeleccionado.tipo,
                item_id: productoSeleccionado.tipo === 'producto' ? productoSeleccionado.id : productoSeleccionado.id,
                nombre: productoSeleccionado.nombre,
                cantidad: cantidad,
                precio_unitario: precio,
                subtotal: subtotal,
                fecha_vencimiento: fechaVencimiento || null
            };
            
            productosAgregados.push(producto);
            actualizarTablaProductos();
            actualizarTotal();
            
            // Limpiar selección
            productoSeleccionado = null;
            document.getElementById('producto_info').classList.add('hidden');
            document.getElementById('sin_producto').classList.remove('hidden');
            document.getElementById('buscar_producto').value = '';
            document.getElementById('buscar_presentacion').value = '';
        });

        // Función para actualizar tabla de productos
        function actualizarTablaProductos() {
            const tbody = document.getElementById('productos_agregados');
            tbody.innerHTML = '';
            
            productosAgregados.forEach((producto, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.tipo}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.nombre}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.cantidad}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">S/ ${producto.precio_unitario.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">S/ ${producto.subtotal.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.fecha_vencimiento || 'Sin fecha'}</td>
                                         <td class="px-4 py-2 text-sm text-gray-900">
                         <button type="button" onclick="eliminarProducto(${index})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                             </svg>
                         </button>
                     </td>
                `;
                tbody.appendChild(row);
            });
            
            // Actualizar campos ocultos
            actualizarCamposOcultos();
        }

        // Función para eliminar producto
        function eliminarProducto(index) {
            productosAgregados.splice(index, 1);
            actualizarTablaProductos();
            actualizarTotal();
        }

        // Función para actualizar campos ocultos
        function actualizarCamposOcultos() {
            const container = document.getElementById('productos_data');
            container.innerHTML = '';
            
            productosAgregados.forEach((producto, index) => {
                container.innerHTML += `
                    <input type="hidden" name="productos[${index}][tipo]" value="${producto.tipo}">
                    <input type="hidden" name="productos[${index}][item_id]" value="${producto.item_id}">
                    <input type="hidden" name="productos[${index}][nombre]" value="${producto.nombre}">
                    <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
                    <input type="hidden" name="productos[${index}][precio_unitario]" value="${producto.precio_unitario}">
                    <input type="hidden" name="productos[${index}][subtotal]" value="${producto.subtotal}">
                    <input type="hidden" name="productos[${index}][fecha_vencimiento]" value="${producto.fecha_vencimiento || ''}">
                `;
            });
        }

        // Función para actualizar total
        function actualizarTotal() {
            const total = productosAgregados.reduce((sum, producto) => sum + producto.subtotal, 0);
            document.getElementById('total').value = total.toFixed(2);
        }

        // Event listener para el formulario
        document.getElementById('createPurchaseForm').addEventListener('submit', function(e) {
            if (productosAgregados.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la compra.');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Creando...';
            submitSpinner.classList.remove('hidden');
        });

        // Cerrar resultados de búsqueda al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#buscar_producto') && !e.target.closest('#resultados_busqueda')) {
                document.getElementById('resultados_busqueda').classList.add('hidden');
            }
            if (!e.target.closest('#buscar_presentacion') && !e.target.closest('#resultados_busqueda_presentacion')) {
                document.getElementById('resultados_busqueda_presentacion').classList.add('hidden');
            }
        });

                 // Configurar detección automática de código de barras
         configurarLectorCodigoBarras();
         
         // Búsqueda de proveedores por documento
         let timeoutBusquedaProveedor;
         document.getElementById('proveedor_documento').addEventListener('input', function() {
             clearTimeout(timeoutBusquedaProveedor);
             const documento = this.value.trim();
             
             // Validar longitud mínima
             if (documento.length < 8) {
                 return;
             }
             
             timeoutBusquedaProveedor = setTimeout(() => {
                 buscarProveedorPorDocumento(documento);
             }, 500);
         });
         
         // Función para buscar proveedor por documento
         function buscarProveedorPorDocumento(documento) {
             const proveedorIdInput = document.getElementById('proveedor_id');
             const proveedorNombreInput = document.getElementById('proveedor_nombre');
             const proveedorSpinner = document.getElementById('proveedor_spinner');
             
             // Limpiar nombre del proveedor
             proveedorNombreInput.value = '';
             proveedorIdInput.value = '';
             
             // Mostrar spinner
             proveedorSpinner.classList.remove('hidden');
             
             // Consultar API de Perú
             fetch(`/api/consultar-proveedor/${documento}`)
                 .then(response => response.json())
                 .then(data => {
                     // Ocultar spinner
                     proveedorSpinner.classList.add('hidden');
                     
                     if (data.success) {
                         // Obtener nombre según el tipo de documento
                         let nombre = '';
                         if (data.tipo === 'DNI') {
                             nombre = data.data.nombre_completo || `${data.data.nombres} ${data.data.apellido_paterno} ${data.data.apellido_materno}`;
                         } else if (data.tipo === 'RUC') {
                             nombre = data.data.nombre_o_razon_social;
                         }
                         
                         // Mostrar nombre en el campo
                         proveedorNombreInput.value = nombre;
                         
                         // Usar el ID del proveedor de la respuesta (ya sea existente o recién creado)
                         if (data.data.proveedor_id) {
                             proveedorIdInput.value = data.data.proveedor_id;
                             
                             if (data.data.proveedor_creado) {
                                 mostrarMensajeExito(`Proveedor registrado automáticamente: ${nombre}`);
                             } else {
                                 mostrarMensajeExito(`Proveedor encontrado en base de datos: ${nombre}`);
                             }
                         } else {
                             // Fallback: buscar en la lista local de proveedores
                             const proveedor = suppliers.find(supplier => supplier.nro_documento === documento);
                             if (proveedor) {
                                 proveedorIdInput.value = proveedor.id;
                                 mostrarMensajeExito(`Proveedor encontrado en base de datos: ${proveedor.nombre_completo}`);
                             } else {
                                 // Si no existe en BD local, crear un proveedor temporal
                                 proveedorIdInput.value = 'temp_' + documento;
                                 mostrarMensajeError('Proveedor no encontrado. Se creará automáticamente al guardar la compra.');
                             }
                         }
                     } else {
                         // Error en la consulta
                         mostrarMensajeError(data.message || 'Error al consultar el documento');
                         
                         // Buscar en la lista local de proveedores como fallback
                         const proveedor = suppliers.find(supplier => supplier.nro_documento === documento);
                         if (proveedor) {
                             proveedorIdInput.value = proveedor.id;
                             proveedorNombreInput.value = proveedor.nombre_completo;
                             mostrarMensajeExito(`Proveedor encontrado en base de datos: ${proveedor.nombre_completo}`);
                         } else {
                             proveedorIdInput.value = 'temp_' + documento;
                             proveedorNombreInput.value = 'Proveedor no encontrado';
                         }
                     }
                 })
                 .catch(error => {
                     // Ocultar spinner
                     proveedorSpinner.classList.add('hidden');
                     
                     console.error('Error:', error);
                     mostrarMensajeError('Error de conexión al consultar el documento');
                     
                     // Buscar en la lista local de proveedores como fallback
                     const proveedor = suppliers.find(supplier => supplier.nro_documento === documento);
                     if (proveedor) {
                         proveedorIdInput.value = proveedor.id;
                         proveedorNombreInput.value = proveedor.nombre_completo;
                         mostrarMensajeExito(`Proveedor encontrado en base de datos: ${proveedor.nombre_completo}`);
                     } else {
                         proveedorIdInput.value = 'temp_' + documento;
                         proveedorNombreInput.value = 'Proveedor no encontrado';
                     }
                 });
         }
        
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
            // Cambiar automáticamente a la pestaña de productos si no está seleccionada
            const tipoItem = document.getElementById('tipo_item');
            if (tipoItem.value !== 'producto') {
                tipoItem.value = 'producto';
                // Disparar el evento change para actualizar la interfaz
                tipoItem.dispatchEvent(new Event('change'));
            }
            
            // Establecer el código de barras en el campo de búsqueda
            const campoBusqueda = document.getElementById('buscar_producto');
            campoBusqueda.value = codigo;
            
            // Realizar la búsqueda automáticamente
            buscarProductos(codigo);
            
            // Mostrar mensaje de confirmación
            mostrarMensajeExito(`Código de barras escaneado: ${codigo}`);
        }

        // Función para verificar si es un código de barras
        function esCodigoBarras(texto) {
            // Los códigos de barras típicamente son solo números y letras, sin espacios
            return /^[A-Za-z0-9]+$/.test(texto) && texto.length >= 8;
        }

        // Función para mostrar mensaje de error
        function mostrarMensajeError(mensaje) {
            // Crear elemento de mensaje
            const mensajeDiv = document.createElement('div');
            mensajeDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
            mensajeDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${mensaje}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(mensajeDiv);
            
            // Remover automáticamente después de 5 segundos
            setTimeout(() => {
                if (mensajeDiv.parentElement) {
                    mensajeDiv.remove();
                }
            }, 5000);
        }

        // Función para mostrar mensaje de éxito
        function mostrarMensajeExito(mensaje) {
            // Crear elemento de mensaje
            const mensajeDiv = document.createElement('div');
            mensajeDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
            mensajeDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${mensaje}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(mensajeDiv);
            
            // Remover automáticamente después de 3 segundos
            setTimeout(() => {
                if (mensajeDiv.parentElement) {
                    mensajeDiv.remove();
                }
            }, 3000);
        }
    </script>
</x-app-layout>
