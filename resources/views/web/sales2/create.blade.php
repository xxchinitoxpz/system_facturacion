<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Venta 2</h1>
            <a href="{{ route('sales2.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Layout de dos columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6" x-data="{ 
            documentType: 'ticket', 
            clientName: 'CLIENTE GENERAL', 
            cartItems: [],
            paymentMethods: [
                {
                    id: 1,
                    method: 'Efectivo',
                    amount: 0
                }
            ],
            vuelto: 0,
            total: 0,
            addToCart(presentation) {
                // Buscar si el producto ya existe en el carrito
                const existingItem = this.cartItems.find(item => item.id === presentation.id);
                
                if (existingItem) {
                    // Si existe, aumentar la cantidad
                    existingItem.cantidad += presentation.cantidad;
                } else {
                    // Si no existe, agregar nuevo item
                    this.cartItems.push({
                        id: presentation.id,
                        product_id: presentation.product_id,
                        product_name: presentation.product_name,
                        presentation_id: presentation.presentation_id,
                        presentation_name: presentation.presentation_name,
                        unidades: presentation.unidades,
                        precio: presentation.precio,
                        cantidad: presentation.cantidad
                    });
                }
                
                // Resetear cantidad del producto
                presentation.cantidad = 1;
                
                // Recalcular totales
                this.calculateTotals();
            },
            removeFromCart(itemId) {
                this.cartItems = this.cartItems.filter(item => item.id !== itemId);
                
                // Recalcular totales
                this.calculateTotals();
            },
            
            addPayment(method, amount) {
                this.paymentMethods.push({
                    id: Date.now(),
                    method: method,
                    amount: parseFloat(amount)
                });
                this.calculateTotals();
            },
            
            removePayment(paymentId) {
                this.paymentMethods = this.paymentMethods.filter(payment => payment.id !== paymentId);
                this.calculateTotals();
            },
            
            calculateTotals() {
                // Calcular subtotal de productos
                const subtotal = this.cartItems.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
                
                // Actualizar el monto de efectivo con el total
                if (this.paymentMethods.length > 0) {
                    this.paymentMethods[0].amount = subtotal;
                }
                
                // Calcular total de pagos
                const totalPayments = this.paymentMethods.reduce((sum, payment) => sum + payment.amount, 0);
                
                // Calcular vuelto
                this.vuelto = Math.max(0, totalPayments - subtotal);
                
                // El total es el subtotal
                this.total = subtotal;
            }
        }">
            <!-- Columna izquierda (más grande) -->
            <div class="lg:col-span-3">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <!-- Tipo de comprobante y Cliente -->
                    <div class="flex gap-4 mb-6">
                        <!-- Tipo de comprobante -->
                        <div class="w-32">
                            <select 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700"
                                x-model="documentType"
                                @change="if (documentType === 'ticket') { clientName = 'CLIENTE GENERAL' } else { clientName = '' }"
                            >
                                <option value="ticket">Ticket</option>
                                <option value="boleta">Boleta</option>
                                <option value="factura">Factura</option>
                            </select>
                        </div>
                        
                        <!-- Cliente -->
                        <div class="flex-1">
                            <input 
                                type="text" 
                                placeholder="Buscar cliente..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700"
                                x-model="clientName"
                                :readonly="documentType === 'ticket'"
                                :class="{ 'bg-gray-100': documentType === 'ticket' }"
                            >
                        </div>
                    </div>
                    
                    <!-- Tabla de productos agregados -->
                    <div class="bg-white border border-gray-200 rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Aquí se mostrarán los productos agregados -->
                                    <tr x-show="cartItems.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <p class="text-sm">No hay productos agregados</p>
                                        </td>
                                    </tr>
                                    
                                    <!-- Productos en el carrito -->
                                    <template x-for="item in cartItems" :key="item.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                <div>
                                                    <div class="font-medium" x-text="item.product_name"></div>
                                                    <div class="text-gray-500 text-xs" x-text="item.presentation_name + ' (' + item.unidades + ' unidades)'"></div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="item.cantidad"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="'S/. ' + item.precio"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="'S/. ' + (item.cantidad * item.precio).toFixed(2)"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                                <button class="text-red-600 hover:text-red-800" @click="removeFromCart(item.id)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Sección de pagos y totales -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <!-- Tabla de pagos -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Métodos de Pago</h3>
                            <div class="bg-white border border-gray-200 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <button class="bg-green-500 hover:bg-green-600 text-white rounded-full p-1 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <!-- Aquí se mostrarán los métodos de pago -->
                                            <tr x-show="paymentMethods.length === 0">
                                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                                    <p class="text-sm">No hay métodos de pago agregados</p>
                                                </td>
                                            </tr>
                                            
                                            <!-- Métodos de pago -->
                                            <template x-for="payment in paymentMethods" :key="payment.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="payment.method"></td>
                                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="'S/. ' + payment.amount"></td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                                        <button class="text-red-600 hover:text-red-800" @click="removePayment(payment.id)">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Totales -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Totales</h3>
                            <div class="flex gap-4">
                                <!-- Input Vuelto -->
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Vuelto</label>
                                    <input 
                                        type="number" 
                                        step="0.01"
                                        placeholder="0.00"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700"
                                        x-model="vuelto"
                                        readonly
                                    >
                                </div>
                                
                                <!-- Input Total -->
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                                    <input 
                                        type="number" 
                                        step="0.01"
                                        placeholder="0.00"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700"
                                        x-model="total"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha (más pequeña) -->
            <div class="lg:col-span-2" x-data="categoryCarousel()">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <!-- Buscador -->
                    <div class="mb-4">
                        <input 
                            type="text" 
                            placeholder="Productos..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700"
                            x-model="searchTerm"
                            @input="filterProducts()"
                        >
                    </div>
                    
                    <!-- Categorías -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2">
                            <!-- Flecha izquierda -->
                            <button @click="previousPage()" :disabled="currentPage === 0" 
                                    class="w-8 h-8 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            
                            <!-- Contenedor de categorías -->
                            <div class="flex-1">
                                <div class="grid grid-cols-3 gap-2" style="grid-template-rows: repeat(2, 1fr);">
                                    <template x-for="(category, index) in visibleCategories" :key="index">
                                        <button class="font-medium py-3 px-2 rounded-lg transition-colors text-xs uppercase"
                                                :class="selectedCategory && selectedCategory.id === category.id ? 'bg-gray-700 hover:bg-gray-800 text-white' : 'bg-green-500 hover:bg-green-600 text-white'"
                                                @click="toggleCategory(category)">
                                            <span x-text="category.nombre"></span>
                                        </button>
                                    </template>
                                    
                                    <!-- Rellenar espacios vacíos -->
                                    <template x-for="i in emptySlots" :key="'empty-' + i">
                                        <div class="bg-gray-200 rounded-lg"></div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Flecha derecha -->
                            <button @click="nextPage()" :disabled="currentPage >= totalPages - 1"
                                    class="w-8 h-8 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Lista de productos -->
                    <div class="max-h-96 overflow-y-auto" x-show="selectedCategory || searchTerm.trim() !== ''">
                        <div class="grid grid-cols-3 gap-3" x-show="filteredProducts.length > 0">
                            <template x-for="presentation in filteredProducts" :key="presentation.id">
                                <!-- Card del producto -->
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col h-full">
                                    <!-- Información de la presentación -->
                                    <div class="flex justify-between items-start p-3 pb-2">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-sm" x-text="presentation.product_name"></h4>
                                            <p class="text-xs text-gray-500" x-text="presentation.presentation_name + ' (' + presentation.unidades + ' unidades)'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900 text-sm" x-text="'S/. ' + presentation.precio"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenedor fijo abajo -->
                                    <div class="mt-auto flex flex-col gap-2 px-3 pb-3">
                                        <!-- Selector de cantidad -->
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold"
                                                    @click="decreaseQuantity(presentation)">
                                                -
                                            </button>
                                            <div class="flex-1 text-center">
                                                <span class="text-lg font-semibold" x-text="presentation.cantidad || 1"></span>
                                            </div>
                                            <button class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold"
                                                    @click="increaseQuantity(presentation)">
                                                +
                                            </button>
                                        </div>

                                        <!-- Botón de acción -->
                                        <button class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-3 rounded flex items-center justify-center gap-1 text-sm font-medium"
                                                @click="addToCart(presentation)">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M 4.4160156 1.9960938 L 1.0039062 2.0136719 L 1.0136719 4.0136719 L 3.0839844 4.0039062 L 6.3789062 11.908203 L 5.1816406 13.822266 C 4.3432852 15.161017 5.3626785 17 6.9414062 17 L 19 17 L 19 15 L 6.9414062 15 C 6.8301342 15 6.8173041 14.978071 6.8769531 14.882812 L 8.0527344 13 L 15.521484 13 C 16.247484 13 16.917531 12.605703 17.269531 11.970703 L 20.871094 5.484375 C 21.242094 4.818375 20.760047 4 19.998047 4 L 5.25 4 L 4.4160156 1.9960938 z M 7 18 C 5.8954305003384135 18 5 18.895430500338414 5 20 C 5 21.104569499661586 5.8954305003384135 22 7 22 C 8.104569499661586 22 9 21.104569499661586 9 20 C 9 18.895430500338414 8.104569499661586 18 7 18 z M 17 18 C 15.895430500338414 18 15 18.895430500338414 15 20 C 15 21.104569499661586 15.895430500338414 22 17 22 C 18.104569499661586 22 19 21.104569499661586 19 20 C 19 18.895430500338414 18.104569499661586 18 17 18 z" />
                                            </svg>
                                            <span>Agregar</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Estado vacío -->
                        <div class="text-center py-8 text-gray-500" x-show="filteredProducts.length === 0">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                            <p class="text-sm">No hay presentaciones disponibles en esta categoría</p>
                        </div>
                    </div>
                    
                    
                    <!-- Input de observaciones -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:outline-none text-gray-700 resize-none"
                            rows="3"
                            placeholder="Agregar observaciones sobre la venta..."
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryCarousel() {
            return {
                currentPage: 0,
                itemsPerPage: 6,
                categories: @json($categories),
                products: @json($products),
                selectedCategory: null,
                filteredProducts: [],
                searchTerm: '',
                
                get totalPages() {
                    return Math.ceil(this.categories.length / this.itemsPerPage);
                },

                get visibleCategories() {
                    const start = this.currentPage * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.categories.slice(start, end);
                },

                get emptySlots() {
                    const visibleCount = this.visibleCategories.length;
                    return Array.from({length: Math.max(0, this.itemsPerPage - visibleCount)}, (_, i) => i);
                },

                nextPage() {
                    if (this.currentPage < this.totalPages - 1) {
                        this.currentPage++;
                    }
                },

                previousPage() {
                    if (this.currentPage > 0) {
                        this.currentPage--;
                    }
                },

                toggleCategory(category) {
                    // Si la categoría ya está seleccionada, deseleccionarla
                    if (this.selectedCategory && this.selectedCategory.id === category.id) {
                        this.selectedCategory = null;
                        this.filteredProducts = [];
                    } else {
                        // Si no está seleccionada, seleccionarla
                        this.selectedCategory = category;
                        this.filterProducts();
                    }
                },

                filterProducts() {
                    let filteredProducts = this.products;
                    
                    // Filtrar por categoría si está seleccionada
                    if (this.selectedCategory) {
                        filteredProducts = filteredProducts.filter(product => {
                            return product.categoria_id == this.selectedCategory.id;
                        });
                    }
                    
                    // Filtrar por término de búsqueda
                    if (this.searchTerm.trim() !== '') {
                        filteredProducts = filteredProducts.filter(product => {
                            const searchTerm = this.searchTerm.toLowerCase();
                            return product.nombre.toLowerCase().includes(searchTerm) || 
                                   (product.barcode && product.barcode.toLowerCase().includes(searchTerm));
                        });
                    }
                    
                    // Crear un array de presentaciones de todos los productos filtrados
                    this.filteredProducts = [];
                    filteredProducts.forEach(product => {
                        if (product.presentations && product.presentations.length > 0) {
                            product.presentations.forEach(presentation => {
                                this.filteredProducts.push({
                                    id: `${product.id}_${presentation.id}`,
                                    product_id: product.id,
                                    product_name: product.nombre,
                                    presentation_id: presentation.id,
                                    presentation_name: presentation.nombre || 'Unidad',
                                    unidades: presentation.unidades,
                                    precio: presentation.precio_venta,
                                    cantidad: 1
                                });
                            });
                        }
                    });
                },

                increaseQuantity(presentation) {
                    presentation.cantidad = (presentation.cantidad || 1) + 1;
                },

                decreaseQuantity(presentation) {
                    if (presentation.cantidad > 1) {
                        presentation.cantidad = presentation.cantidad - 1;
                    }
                },

            }
        }
    </script>
</x-app-layout>
