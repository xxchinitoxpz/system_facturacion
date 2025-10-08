<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Venta</h1>
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form id="editSaleForm" method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Información General y del Cliente -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General y del Cliente</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="tipo_comprobante" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Comprobante *</label>
                        <select 
                            id="tipo_comprobante" 
                            name="tipo_comprobante"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tipo_comprobante') border-red-500 @enderror"
                            required
                        >
                            <option value="">Seleccionar tipo</option>
                            <option value="boleta" {{ old('tipo_comprobante', $sale->tipo_comprobante) == 'boleta' ? 'selected' : '' }}>Boleta</option>
                            <option value="factura" {{ old('tipo_comprobante', $sale->tipo_comprobante) == 'factura' ? 'selected' : '' }}>Factura</option>
                            <option value="ticket" {{ old('tipo_comprobante', $sale->tipo_comprobante) == 'ticket' ? 'selected' : '' }}>Ticket</option>
                        </select>
                        @error('tipo_comprobante')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cliente_documento" class="block text-sm font-medium text-gray-700 mb-2">Documento del Cliente *</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="cliente_documento" 
                                name="cliente_documento"
                                value="{{ old('cliente_documento', $sale->client->nro_documento ?? '00000000') }}"
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('cliente_documento') border-red-500 @enderror"
                                placeholder="Seleccione tipo de comprobante primero"
                                required
                                readonly
                            >
                            <div id="cliente_spinner" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" id="cliente_id" name="cliente_id" value="{{ old('cliente_id', $sale->cliente_id) }}">
                        @error('cliente_documento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cliente_nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cliente</label>
                        <input 
                            type="text" 
                            id="cliente_nombre" 
                            name="cliente_nombre"
                            value="{{ old('cliente_nombre', $sale->client->nombre_completo ?? 'CLIENTE GENERAL') }}"
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
                                    <option value="{{ $branch->id }}" {{ old('sucursal_id', $sale->sucursal_id) == $branch->id ? 'selected' : '' }}>
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
                </div>
                

                
                <div class="mt-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('observaciones') border-red-500 @enderror"
                        placeholder="Observaciones adicionales sobre la venta..."
                    >{{ old('observaciones', $sale->observaciones) }}</textarea>
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
                                <span>Lector de código de barras activo</span>
                            </div>
                        </div>
                        
                        <!-- Controles para agregar productos -->
                        <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-1">
                                    <label for="tipo_item" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Item</label>
                                    <select id="tipo_item" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="producto">Producto</option>
                                        <option value="combo">Combo</option>
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
                                
                                <div id="combo_select" class="combo-field hidden md:col-span-3">
                                    <label for="buscar_combo" class="block text-sm font-medium text-gray-700 mb-2">Buscar Combo</label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="buscar_combo" 
                                            placeholder="Buscar combo por nombre..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                        <div id="resultados_busqueda_combo" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            <!-- Los resultados de combos se mostrarán aquí -->
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
                    
                    <!-- Campo oculto para el estado -->
                    <input type="hidden" name="estado" value="{{ old('estado', $sale->estado) }}">
                </div>
                
                <!-- COLUMNA DERECHA: Información del Pago y Resumen -->
                <div class="space-y-6">
                    
                    <!-- Campo oculto para el total -->
                    <input 
                        type="hidden" 
                        id="total" 
                        name="total" 
                        value="{{ old('total', $sale->total) }}"
                    >
                    
                    <!-- Información del Pago -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Pago</h2>
                        
                        <!-- Checkbox para dividir pago -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="dividir_pago" 
                                    name="dividir_pago" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    onchange="toggleDivisionPago()"
                                    {{ $sale->payments->count() > 1 ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm font-medium text-gray-700">Dividir Pago</span>
                            </label>
                        </div>
                        
                        <!-- Formulario de pago único -->
                        <div id="pago_unico" class="space-y-4" {{ $sale->payments->count() > 1 ? 'style="display: none;"' : '' }}>
                            <div>
                                <label for="tipo_pago" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pago *</label>
                                <select 
                                    id="tipo_pago" 
                                    name="tipo_pago" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                >
                                    <option value="">Seleccionar</option>
                                    <option value="1" {{ $sale->payments->first() && $sale->payments->first()->tipo_pago == 1 ? 'selected' : '' }}>Efectivo</option>
                                    <option value="2" {{ $sale->payments->first() && $sale->payments->first()->tipo_pago == 2 ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="5" {{ $sale->payments->first() && $sale->payments->first()->tipo_pago == 5 ? 'selected' : '' }}>Yape / Plin</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto de la Venta *</label>
                                <input 
                                    type="number" 
                                    id="monto" 
                                    name="monto" 
                                    step="0.01" 
                                    min="0" 
                                    value="{{ old('total', $sale->total) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                    placeholder="0.00"
                                    required
                                    readonly
                                >
                            </div>
                            
                            <div id="monto_recibido_container" class="hidden">
                                <label for="monto_recibido" class="block text-sm font-medium text-gray-700 mb-1">Monto Recibido *</label>
                                <input 
                                    type="number" 
                                    id="monto_recibido" 
                                    name="monto_recibido" 
                                    step="0.01" 
                                    min="0"
                                    value="{{ $sale->payments->first() ? $sale->payments->first()->monto_recibido : '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ingrese el monto que le da el cliente"
                                    onchange="calcularVuelto()"
                                >
                            </div>
                            
                            <div id="vuelto_container" class="hidden">
                                <label for="vuelto" class="block text-sm font-medium text-gray-700 mb-1">Vuelto</label>
                                <input 
                                    type="number" 
                                    id="vuelto" 
                                    name="vuelto" 
                                    step="0.01" 
                                    min="0"
                                    value="{{ $sale->payments->first() ? $sale->payments->first()->vuelto : '0.00' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                    placeholder="0.00"
                                    readonly
                                >
                            </div>
                        </div>
                        
                        <!-- Formulario de pago dividido -->
                        <div id="pago_dividido" class="space-y-6" {{ $sale->payments->count() <= 1 ? 'style="display: none;"' : '' }}>
                            <!-- Pago 1 -->
                            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Pago 1</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tipo_pago_1" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pago *</label>
                                        <select 
                                            id="tipo_pago_1" 
                                            name="tipo_pago_1" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            onchange="toggleCamposPago(1)"
                                        >
                                            <option value="">Seleccionar</option>
                                            <option value="1" {{ $sale->payments->count() > 1 && $sale->payments->first() && $sale->payments->first()->tipo_pago == 1 ? 'selected' : '' }}>Efectivo</option>
                                            <option value="2" {{ $sale->payments->count() > 1 && $sale->payments->first() && $sale->payments->first()->tipo_pago == 2 ? 'selected' : '' }}>Tarjeta</option>
                                            <option value="5" {{ $sale->payments->count() > 1 && $sale->payments->first() && $sale->payments->first()->tipo_pago == 5 ? 'selected' : '' }}>Yape / Plin</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="monto_pago_1" class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                                        <input 
                                            type="number" 
                                            id="monto_pago_1" 
                                            name="monto_pago_1" 
                                            step="0.01" 
                                            min="0" 
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->first() ? $sale->payments->first()->monto : '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="0.00"
                                            onchange="validarDivisionPago()"
                                        >
                                    </div>
                                    
                                    <div id="monto_recibido_container_1" class="hidden">
                                        <label for="monto_recibido_1" class="block text-sm font-medium text-gray-700 mb-1">Monto Recibido *</label>
                                        <input 
                                            type="number" 
                                            id="monto_recibido_1" 
                                            name="monto_recibido_1" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->first() ? $sale->payments->first()->monto_recibido : '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Ingrese el monto que le da el cliente"
                                            onchange="calcularVueltoDividido(1)"
                                        >
                                    </div>
                                    
                                    <div id="vuelto_container_1" class="hidden">
                                        <label for="vuelto_1" class="block text-sm font-medium text-gray-700 mb-1">Vuelto</label>
                                        <input 
                                            type="number" 
                                            id="vuelto_1" 
                                            name="vuelto_1" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->first() ? $sale->payments->first()->vuelto : '0.00' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                            placeholder="0.00"
                                            readonly
                                        >
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pago 2 -->
                            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Pago 2</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tipo_pago_2" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pago *</label>
                                        <select 
                                            id="tipo_pago_2" 
                                            name="tipo_pago_2" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            onchange="toggleCamposPago(2)"
                                        >
                                            <option value="">Seleccionar</option>
                                            <option value="1" {{ $sale->payments->count() > 1 && $sale->payments->last() && $sale->payments->last()->tipo_pago == 1 ? 'selected' : '' }}>Efectivo</option>
                                            <option value="2" {{ $sale->payments->count() > 1 && $sale->payments->last() && $sale->payments->last()->tipo_pago == 2 ? 'selected' : '' }}>Tarjeta</option>
                                            <option value="5" {{ $sale->payments->count() > 1 && $sale->payments->last() && $sale->payments->last()->tipo_pago == 5 ? 'selected' : '' }}>Yape / Plin</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="monto_pago_2" class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                                        <input 
                                            type="number" 
                                            id="monto_pago_2" 
                                            name="monto_pago_2" 
                                            step="0.01" 
                                            min="0" 
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->last() ? $sale->payments->last()->monto : '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="0.00"
                                            onchange="validarDivisionPago()"
                                        >
                                    </div>
                                    
                                    <div id="monto_recibido_container_2" class="hidden">
                                        <label for="monto_recibido_2" class="block text-sm font-medium text-gray-700 mb-1">Monto Recibido *</label>
                                        <input 
                                            type="number" 
                                            id="monto_recibido_2" 
                                            name="monto_recibido_2" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->last() ? $sale->payments->last()->monto_recibido : '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Ingrese el monto que le da el cliente"
                                            onchange="calcularVueltoDividido(2)"
                                        >
                                    </div>
                                    
                                    <div id="vuelto_container_2" class="hidden">
                                        <label for="vuelto_2" class="block text-sm font-medium text-gray-700 mb-1">Vuelto</label>
                                        <input 
                                            type="number" 
                                            id="vuelto_2" 
                                            name="vuelto_2" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $sale->payments->count() > 1 && $sale->payments->last() ? $sale->payments->last()->vuelto : '0.00' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                            placeholder="0.00"
                                            readonly
                                        >
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Resumen de división -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-blue-700">Total de la Venta:</span>
                                    <span class="text-lg font-bold text-blue-800" id="total_venta_display">S/ {{ number_format($sale->total, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm font-medium text-blue-700">Suma de Pagos:</span>
                                    <span class="text-lg font-bold" id="suma_pagos_display">S/ {{ number_format($sale->payments->sum('monto'), 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm font-medium text-blue-700">Diferencia:</span>
                                    <span class="text-lg font-bold" id="diferencia_display">S/ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('sales.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Venta</span>
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
        let combos = JSON.parse('{!! json_encode($combos ?? []) !!}');
        let clients = JSON.parse('{!! json_encode($clients) !!}');
        
        // Manejar cambio de tipo de pago
        document.getElementById('tipo_pago').addEventListener('change', function() {
            const tipoPago = this.value;
            const montoRecibidoContainer = document.getElementById('monto_recibido_container');
            const vueltoContainer = document.getElementById('vuelto_container');
            const montoRecibidoInput = document.getElementById('monto_recibido');
            const vueltoInput = document.getElementById('vuelto');
            
            if (tipoPago === '1') { // Efectivo
                montoRecibidoContainer.classList.remove('hidden');
                vueltoContainer.classList.remove('hidden');
                montoRecibidoInput.required = true;
            } else { // Tarjeta o Yape/Plin
                montoRecibidoContainer.classList.add('hidden');
                vueltoContainer.classList.add('hidden');
                montoRecibidoInput.required = false;
            }
        });
        
        // Cargar productos existentes al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarProductosExistentes();
            configurarLectorCodigoBarras();
            
            // Configurar campos de pago según el tipo existente
            const tipoPagoExistente = document.getElementById('tipo_pago').value;
            if (tipoPagoExistente === '1') { // Efectivo
                document.getElementById('monto_recibido_container').classList.remove('hidden');
                document.getElementById('vuelto_container').classList.remove('hidden');
                document.getElementById('monto_recibido').required = true;
            } else { // Tarjeta o Yape/Plin
                document.getElementById('monto_recibido_container').classList.add('hidden');
                document.getElementById('vuelto_container').classList.add('hidden');
                document.getElementById('monto_recibido').required = false;
            }
            
            // Configurar campos de pago dividido si es necesario
            const dividirPago = document.getElementById('dividir_pago').checked;
            if (dividirPago) {
                // Mostrar formulario de pago dividido
                document.getElementById('pago_unico').style.display = 'none';
                document.getElementById('pago_dividido').style.display = 'block';
                
                // Configurar campos del pago 1
                const tipoPago1 = document.getElementById('tipo_pago_1').value;
                if (tipoPago1 === '1') {
                    document.getElementById('monto_recibido_container_1').classList.remove('hidden');
                    document.getElementById('vuelto_container_1').classList.remove('hidden');
                }
                
                // Configurar campos del pago 2
                const tipoPago2 = document.getElementById('tipo_pago_2').value;
                if (tipoPago2 === '1') {
                    document.getElementById('monto_recibido_container_2').classList.remove('hidden');
                    document.getElementById('vuelto_container_2').classList.remove('hidden');
                }
                
                // Actualizar display de división
                validarDivisionPago();
            } else {
                // Asegurar que el pago dividido esté oculto si no está marcado
                document.getElementById('pago_unico').style.display = 'block';
                document.getElementById('pago_dividido').style.display = 'none';
            }
            
            // Calcular vuelto inicial
            calcularVuelto();
        });

        // Función para cargar productos existentes
        function cargarProductosExistentes() {
            // Cargar productos existentes desde los datos pasados por el controlador
            // Los datos se cargan en el script que está al final del archivo
            actualizarTabla();
            actualizarTotal();
        }
        
        // Manejar cambio de tipo de comprobante
        document.getElementById('tipo_comprobante').addEventListener('change', function() {
            const tipoComprobante = this.value;
            const clienteDocumentoInput = document.getElementById('cliente_documento');
            const clienteIdInput = document.getElementById('cliente_id');
            
            // Limpiar campos
            clienteDocumentoInput.value = '';
            clienteIdInput.value = '';
            
            if (tipoComprobante === 'ticket') {
                // Para ticket, auto-seleccionar cliente con documento 00000000
                const clienteTicket = clients.find(client => client.nro_documento === '00000000');
                if (clienteTicket) {
                    clienteDocumentoInput.value = clienteTicket.nro_documento;
                    clienteIdInput.value = clienteTicket.id;
                    clienteDocumentoInput.readOnly = true;
                    clienteDocumentoInput.placeholder = 'Cliente automático (Ticket)';
                }
            } else if (tipoComprobante === 'boleta') {
                // Para boleta, permitir ingresar DNI
                clienteDocumentoInput.readOnly = false;
                clienteDocumentoInput.placeholder = 'Digite DNI';
                clienteDocumentoInput.addEventListener('input', buscarClientePorDocumento);
            } else if (tipoComprobante === 'factura') {
                // Para factura, permitir ingresar RUC
                clienteDocumentoInput.readOnly = false;
                clienteDocumentoInput.placeholder = 'Digite RUC';
                clienteDocumentoInput.addEventListener('input', buscarClientePorDocumento);
            } else {
                // Sin selección
                clienteDocumentoInput.readOnly = true;
                clienteDocumentoInput.placeholder = 'Seleccione tipo de comprobante primero';
            }
        });
        
        // Función para buscar cliente por documento
        function buscarClientePorDocumento() {
            const documento = this.value.trim();
            const clienteIdInput = document.getElementById('cliente_id');
            const clienteNombreInput = document.getElementById('cliente_nombre');
            const clienteSpinner = document.getElementById('cliente_spinner');
            
            // Limpiar nombre del cliente
            clienteNombreInput.value = '';
            clienteIdInput.value = '';
            
            // Validar longitud mínima
            if (documento.length < 8) {
                return;
            }
            
            // Mostrar spinner
            clienteSpinner.classList.remove('hidden');
            
            // Consultar API de Perú
            fetch(`/api/consultar-documento/${documento}`)
                .then(response => response.json())
                .then(data => {
                    // Ocultar spinner
                    clienteSpinner.classList.add('hidden');
                    
                    if (data.success) {
                        // Obtener nombre según el tipo de documento
                        let nombre = '';
                        if (data.tipo === 'DNI') {
                            nombre = data.data.nombre_completo || `${data.data.nombres} ${data.data.apellido_paterno} ${data.data.apellido_materno}`;
                        } else if (data.tipo === 'RUC') {
                            nombre = data.data.nombre_o_razon_social;
                        }
                        
                        // Mostrar nombre en el campo
                        clienteNombreInput.value = nombre;
                        
                        if (data.data.cliente_existente) {
                            // Cliente existe en BD
                            clienteIdInput.value = data.data.cliente_id;
                            mostrarMensajeExito(`Cliente encontrado en base de datos: ${nombre}`);
                        } else {
                            // Cliente no existe, usar temporal
                            clienteIdInput.value = 'temp_' + documento;
                            mostrarMensajeExito(`Datos obtenidos de SUNAT (cliente temporal): ${nombre}`);
                        }
                    } else {
                        // Si no se encuentra en SUNAT, buscar en BD local
                        const cliente = clients.find(client => client.nro_documento === documento);
                        if (cliente) {
                            clienteIdInput.value = cliente.id;
                            clienteNombreInput.value = cliente.nombre_completo;
                            mostrarMensajeExito(`Cliente encontrado en base de datos: ${cliente.nombre_completo}`);
                        } else {
                            mostrarMensajeError('Cliente no encontrado en SUNAT ni en base de datos local');
                        }
                    }
                })
                .catch(error => {
                    // Ocultar spinner
                    clienteSpinner.classList.add('hidden');
                    
                    console.error('Error al consultar documento:', error);
                    
                    // Buscar en BD local como fallback
                    const cliente = clients.find(client => client.nro_documento === documento);
                    if (cliente) {
                        clienteIdInput.value = cliente.id;
                        clienteNombreInput.value = cliente.nombre_completo;
                        mostrarMensajeExito(`Cliente encontrado en base de datos: ${cliente.nombre_completo}`);
                    } else {
                        mostrarMensajeError('Error al consultar documento. Verifique la conexión.');
                    }
                });
        }
      
        // Cambiar entre producto y combo
        document.getElementById('tipo_item').addEventListener('change', function() {
            const tipo = this.value;
            const productoSelect = document.getElementById('producto_select');
            const comboSelect = document.getElementById('combo_select');
            const presentacionesProducto = document.getElementById('presentaciones_producto');
            
            if (tipo === 'producto') {
                productoSelect.classList.remove('hidden');
                comboSelect.classList.add('hidden');
                presentacionesProducto.classList.add('hidden');
                document.getElementById('buscar_combo').value = '';
                document.getElementById('resultados_busqueda_combo').classList.add('hidden');
                document.getElementById('buscar_producto').value = '';
                productoSeleccionado = null;
            } else {
                productoSelect.classList.add('hidden');
                comboSelect.classList.remove('hidden');
                presentacionesProducto.classList.add('hidden');
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
        
        // Prevenir envío del formulario con Enter en el campo de búsqueda
        document.getElementById('buscar_producto').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                // Si hay resultados de búsqueda, seleccionar el primero
                const resultados = document.getElementById('resultados_busqueda');
                if (!resultados.classList.contains('hidden')) {
                    const primerResultado = resultados.querySelector('div');
                    if (primerResultado) {
                        primerResultado.click();
                    }
                }
            }
        });
      
        // Búsqueda de combos
        let timeoutBusquedaCombo;
        document.getElementById('buscar_combo').addEventListener('input', function() {
            clearTimeout(timeoutBusquedaCombo);
            const query = this.value.trim();
            
            if (query.length < 2) {
                document.getElementById('resultados_busqueda_combo').classList.add('hidden');
                return;
            }
            
            timeoutBusquedaCombo = setTimeout(() => {
                buscarCombos(query);
            }, 300);
        });
        
        // Prevenir envío del formulario con Enter en el campo de búsqueda de combos
        document.getElementById('buscar_combo').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                // Si hay resultados de búsqueda, seleccionar el primero
                const resultados = document.getElementById('resultados_busqueda_combo');
                if (!resultados.classList.contains('hidden')) {
                    const primerResultado = resultados.querySelector('div');
                    if (primerResultado) {
                        primerResultado.click();
                    }
                }
            }
        });
        
        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            const resultados = document.getElementById('resultados_busqueda');
            const buscarInput = document.getElementById('buscar_producto');
            const resultadosCombo = document.getElementById('resultados_busqueda_combo');
            const buscarComboInput = document.getElementById('buscar_combo');
            
            if (!resultados.contains(e.target) && !buscarInput.contains(e.target)) {
                resultados.classList.add('hidden');
            }
            
            if (!resultadosCombo.contains(e.target) && !buscarComboInput.contains(e.target)) {
                resultadosCombo.classList.add('hidden');
            }
        });
      
        // Función para buscar productos
        function buscarProductos(query) {
            fetch(`/api/buscar-productos?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    // Si es un código de barras (solo números y letras, sin espacios) y hay una coincidencia exacta
                    if (esCodigoBarras(query) && data.length === 1 && data[0].barcode === query) {
                        // Seleccionar automáticamente el producto
                        seleccionarProducto(data[0].id, data[0].nombre, data[0].barcode);
                    } else {
                        mostrarResultadosBusqueda(data);
                    }
                })
                .catch(error => {
                    console.error('Error al buscar productos:', error);
                });
        }
        
        // Función para verificar si es un código de barras
        function esCodigoBarras(texto) {
            // Los códigos de barras típicamente son solo números y letras, sin espacios
            return /^[A-Za-z0-9]+$/.test(texto) && texto.length >= 8;
        }
        
        // Función para buscar combos
        function buscarCombos(query) {
            const resultados = combos.filter(combo => 
                combo.nombre.toLowerCase().includes(query.toLowerCase())
            );
            mostrarResultadosBusquedaCombo(resultados);
        }
      
        // Mostrar resultados de búsqueda
        function mostrarResultadosBusqueda(productos) {
            const resultadosDiv = document.getElementById('resultados_busqueda');
            
            if (productos.length === 0) {
                resultadosDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">No se encontraron productos</div>';
                resultadosDiv.classList.remove('hidden');
                return;
            }
            
            let html = '';
            productos.forEach(producto => {
                html += `
                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                         onclick="seleccionarProducto(${producto.id}, '${producto.nombre}', '${producto.barcode}')">
                        <div class="font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-sm text-gray-600">Código: ${producto.barcode}</div>
                        <div class="text-xs text-gray-500">${producto.category?.nombre || 'Sin categoría'}</div>
                    </div>
                `;
            });
            
            resultadosDiv.innerHTML = html;
            resultadosDiv.classList.remove('hidden');
        }
        
        // Mostrar resultados de búsqueda de combos
        function mostrarResultadosBusquedaCombo(combosResultados) {
            const resultadosDiv = document.getElementById('resultados_busqueda_combo');
            
            if (combosResultados.length === 0) {
                resultadosDiv.innerHTML = '<div class="p-3 text-gray-500 text-sm">No se encontraron combos</div>';
                resultadosDiv.classList.remove('hidden');
                return;
            }
            
            let html = '';
            combosResultados.forEach(combo => {
                html += `
                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                         onclick="seleccionarCombo(${combo.id}, '${combo.nombre}', ${combo.precio})">
                        <div class="font-medium text-gray-900">${combo.nombre}</div>
                        <div class="text-lg font-semibold text-indigo-600">S/${parseFloat(combo.precio).toFixed(2)}</div>
                    </div>
                `;
            });
            
            resultadosDiv.innerHTML = html;
            resultadosDiv.classList.remove('hidden');
        }
      
        // Seleccionar producto
        function seleccionarProducto(productoId, nombre, barcode) {
            productoSeleccionado = { id: productoId, nombre: nombre, barcode: barcode };
            document.getElementById('buscar_producto').value = `${nombre} (${barcode})`;
            document.getElementById('resultados_busqueda').classList.add('hidden');
            
            // Cargar presentaciones del producto
            cargarPresentaciones(productoId);
        }
        
        // Seleccionar combo
        function seleccionarCombo(comboId, nombreCombo, precioCombo) {
            document.getElementById('buscar_combo').value = nombreCombo;
            document.getElementById('resultados_busqueda_combo').classList.add('hidden');
            
            // Agregar combo directamente
            agregarCombo(comboId, nombreCombo, precioCombo);
        }
      
        // Cargar presentaciones del producto con stock disponible
        function cargarPresentaciones(productoId) {
            // Obtener el ID de la sucursal según el tipo de usuario
            let sucursalId;
            const selectSucursal = document.getElementById('sucursal_id');
            const hiddenSucursal = document.getElementById('sucursal_id_hidden');
            
            if (selectSucursal) {
                // Usuario administrador - usar el select
                sucursalId = selectSucursal.value;
            } else if (hiddenSucursal) {
                // Usuario empleado - usar el input hidden
                sucursalId = hiddenSucursal.value;
            } else {
                console.error('No se pudo encontrar el ID de la sucursal');
                return;
            }
            
            if (!sucursalId) {
                console.error('No se ha seleccionado una sucursal');
                return;
            }
            
            fetch(`/api/productos/stock-disponible?producto_id=${productoId}&sucursal_id=${sucursalId}`)
                .then(response => response.json())
                .then(data => {
                    mostrarPresentaciones(data.presentaciones, data.stock_disponible, data.precio_unitario);
                })
                .catch(error => {
                    console.error('Error al cargar presentaciones:', error);
                });
        }
        
        // Mostrar presentaciones con stock disponible
        function mostrarPresentaciones(presentaciones, stockDisponible, precioUnitario) {
            const presentacionesDiv = document.getElementById('presentaciones_producto');
            const listaDiv = document.getElementById('lista_presentaciones');
            
            if (presentaciones.length === 0) {
                listaDiv.innerHTML = '<div class="text-gray-500 text-sm">No hay presentaciones disponibles para este producto</div>';
                presentacionesDiv.classList.remove('hidden');
                return;
            }
            
            // 🎯 NUEVA LÓGICA: Si solo hay 1 presentación, agregar directamente
            if (presentaciones.length === 1) {
                const presentacion = presentaciones[0];
                
                // Solo agregar si tiene stock disponible
                if (presentacion.puede_vender) {
                    agregarPresentacion(
                        presentacion.id, 
                        presentacion.nombre, 
                        presentacion.precio_venta, 
                        presentacion.unidades, 
                        presentacion.maximo_presentaciones
                    );
                } else {
                    // Si no tiene stock, mostrar mensaje
                    mostrarMensajeError(`No hay stock disponible para ${presentacion.nombre}`);
                }
                
                // Limpiar búsqueda
                limpiarBusquedaProducto();
                return;
            }
            
            let html = '';
            
            // Agregar opción de producto individual si hay stock
            if (stockDisponible > 0) {
                const stockColor = 'text-green-600';
                const precioFormateado = precioUnitario > 0 ? `S/${parseFloat(precioUnitario).toFixed(2)}` : 'Precio por unidad';
                html += `
                    <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">${productoSeleccionado.nombre} (Unidad individual)</div>
                            <div class="text-sm text-gray-600">1 unidad</div>
                            <div class="text-lg font-semibold text-indigo-600">${precioFormateado}</div>
                            <div class="text-sm ${stockColor} font-medium">
                                Stock: ${stockDisponible} unidades disponibles
                            </div>
                        </div>
                        <button type="button" 
                                onclick="agregarProductoIndividual(${productoSeleccionado.id}, '${productoSeleccionado.nombre}', ${stockDisponible}, ${precioUnitario})"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Agregar Unidad
                        </button>
                    </div>
                `;
            }
            
            // Si hay múltiples presentaciones, mostrar las cards
            presentaciones.forEach(presentacion => {
                const stockColor = presentacion.puede_vender ? 'text-green-600' : 'text-red-600';
                const buttonClass = presentacion.puede_vender 
                    ? 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm'
                    : 'px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed text-sm';
                const buttonText = presentacion.puede_vender ? 'Agregar' : 'Sin stock';
                
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">${presentacion.nombre}</div>
                            <div class="text-sm text-gray-600">${presentacion.unidades} unidades</div>
                            <div class="text-lg font-semibold text-indigo-600">S/${parseFloat(presentacion.precio_venta).toFixed(2)}</div>
                            <div class="text-sm ${stockColor} font-medium">
                                Stock: ${presentacion.stock_disponible} unidades | Máximo: ${presentacion.maximo_presentaciones} ${presentacion.nombre.toLowerCase()}
                            </div>
                        </div>
                        <button type="button" 
                                onclick="${presentacion.puede_vender ? `agregarPresentacion(${presentacion.id}, '${presentacion.nombre}', ${presentacion.precio_venta}, ${presentacion.unidades}, ${presentacion.maximo_presentaciones})` : 'return false'}"
                                class="${buttonClass}">
                            ${buttonText}
                        </button>
                    </div>
                `;
            });
            
            listaDiv.innerHTML = html;
            presentacionesDiv.classList.remove('hidden');
        }
        
        // Agregar producto individual a la venta
        function agregarProductoIndividual(productoId, nombreProducto, stockDisponible, precioUnitario) {
            if (!productoSeleccionado) return;
            
            const itemIndex = contadorProductos++;
            
            // Agregar a la tabla
            const tbody = document.getElementById('productos_agregados');
            const row = document.createElement('tr');
            row.setAttribute('data-index', itemIndex);
            row.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-900">Producto</td>
                <td class="px-4 py-2 text-sm text-gray-900">${nombreProducto}</td>
                <td class="px-4 py-2 text-sm text-gray-900">
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, -1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">-</button>
                        <span id="cantidad_${itemIndex}" class="w-8 text-center font-medium">1</span>
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, 1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">+</button>
                    </div>
                </td>
                <td class="px-4 py-2 text-sm text-gray-900">S/${parseFloat(precioUnitario).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-900 font-semibold" id="subtotal_${itemIndex}">S/${parseFloat(precioUnitario).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">
                    <button type="button" onclick="eliminarProducto(${itemIndex})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                        Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            
            // Agregar a la lista de productos
            productosAgregados.push({
                tipo: 'producto',
                item_id: productoId,
                producto_id: productoId,
                nombre: nombreProducto,
                cantidad: 1,
                precio_unitario: parseFloat(precioUnitario),
                subtotal: parseFloat(precioUnitario),
                es_presentacion: false,
                stock_disponible: stockDisponible
            });
            
            // Actualizar total
            actualizarTotal();
            
            // Mostrar mensaje de confirmación
            mostrarMensajeExito('Producto agregado correctamente');
        }

        // Agregar presentación a la venta
        function agregarPresentacion(presentacionId, nombrePresentacion, precioVenta, unidades, maximoPresentaciones) {
            if (!productoSeleccionado) return;
            
            const nombreCompleto = `${productoSeleccionado.nombre} - ${nombrePresentacion}`;
            const itemIndex = contadorProductos++;
            
            // Agregar a la tabla
            const tbody = document.getElementById('productos_agregados');
            const row = document.createElement('tr');
            row.setAttribute('data-index', itemIndex);
            row.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-900">Producto</td>
                <td class="px-4 py-2 text-sm text-gray-900">${nombreCompleto}</td>
                <td class="px-4 py-2 text-sm text-gray-900">
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, -1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">-</button>
                        <span id="cantidad_${itemIndex}" class="w-8 text-center font-medium">1</span>
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, 1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">+</button>
                    </div>
                </td>
                <td class="px-4 py-2 text-sm text-gray-900">S/${parseFloat(precioVenta).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-900 font-semibold" id="subtotal_${itemIndex}">S/${parseFloat(precioVenta).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">
                    <button type="button" onclick="eliminarProducto(${itemIndex})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                        Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            
            // Agregar a la lista de productos
            productosAgregados.push({
                tipo: 'producto',
                item_id: presentacionId,
                producto_id: productoSeleccionado.id,
                nombre: nombreCompleto,
                cantidad: 1,
                precio_unitario: parseFloat(precioVenta),
                subtotal: parseFloat(precioVenta),
                es_presentacion: true,
                maximo_presentaciones: maximoPresentaciones
            });
            
            // Actualizar total
            actualizarTotal();
            
            // Mostrar mensaje de confirmación
            mostrarMensajeExito('Presentación agregada correctamente');
        }
      
        // Función para cambiar cantidad con validación de stock
        function cambiarCantidad(index, cambio) {
            const producto = productosAgregados[index];
            if (!producto) {
                return;
            }
            
            const nuevaCantidad = producto.cantidad + cambio;
            
            if (nuevaCantidad < 1) return; // No permitir cantidades menores a 1
            
            // Validar stock máximo si es una presentación
            if (producto.es_presentacion && producto.maximo_presentaciones !== undefined) {
                if (nuevaCantidad > producto.maximo_presentaciones) {
                    mostrarMensajeError(`No hay suficiente stock. Máximo disponible: ${producto.maximo_presentaciones} ${producto.nombre.split(' - ')[1].toLowerCase()}`);
                    return;
                }
            }
            
            // Validar stock máximo si es un producto individual
            if (!producto.es_presentacion && producto.stock_disponible !== undefined) {
                if (nuevaCantidad > producto.stock_disponible) {
                    mostrarMensajeError(`No hay suficiente stock. Máximo disponible: ${producto.stock_disponible} unidades`);
                    return;
                }
            }
            
            producto.cantidad = nuevaCantidad;
            producto.subtotal = parseFloat(producto.precio_unitario) * nuevaCantidad;
            
            // Actualizar la interfaz
            document.getElementById(`cantidad_${index}`).textContent = nuevaCantidad;
            document.getElementById(`subtotal_${index}`).textContent = `S/${producto.subtotal.toFixed(2)}`;
            
            // Actualizar botones + y - según stock disponible
            actualizarBotonesCantidad(index, producto);
            
            // Actualizar total
            actualizarTotal();
        }
      
        // Agregar combo a la venta
        function agregarCombo(comboId, nombreCombo, precioCombo) {
            const itemIndex = contadorProductos++;
            
            // Agregar a la tabla
            const tbody = document.getElementById('productos_agregados');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-900">Combo</td>
                <td class="px-4 py-2 text-sm text-gray-900">${nombreCombo}</td>
                <td class="px-4 py-2 text-sm text-gray-900">
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, -1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">-</button>
                        <span id="cantidad_${itemIndex}" class="w-8 text-center font-medium">1</span>
                        <button type="button" onclick="cambiarCantidad(${itemIndex}, 1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">+</button>
                    </div>
                </td>
                <td class="px-4 py-2 text-sm text-gray-900">S/${parseFloat(precioCombo).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-900 font-semibold" id="subtotal_${itemIndex}">S/${parseFloat(precioCombo).toFixed(2)}</td>
                <td class="px-4 py-2 text-sm text-gray-500">
                    <button type="button" onclick="eliminarProducto(${itemIndex})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                        Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            
            // Agregar a la lista de productos
            productosAgregados.push({
                tipo: 'combo',
                item_id: comboId,
                nombre: nombreCombo,
                cantidad: 1,
                precio_unitario: parseFloat(precioCombo),
                subtotal: parseFloat(precioCombo)
            });
            
            // Actualizar total
            actualizarTotal();
            
            // Limpiar búsqueda
            document.getElementById('buscar_combo').value = '';
            
            // Mostrar mensaje de confirmación
            mostrarMensajeExito('Combo agregado correctamente');
        }
      
        // Eliminar producto de la tabla
        function eliminarProducto(index) {
            productosAgregados.splice(index, 1);
            actualizarTabla();
            actualizarTotal();
        }
        
        // Actualizar tabla después de eliminar
        function actualizarTabla() {
            const tbody = document.getElementById('productos_agregados');
            tbody.innerHTML = '';
            
            productosAgregados.forEach((producto, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.tipo === 'producto' ? 'Producto' : 'Combo'}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">${producto.nombre}</td>
                    <td class="px-4 py-2 text-sm text-gray-900">
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="cambiarCantidad(${index}, -1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">-</button>
                            <span id="cantidad_${index}" class="w-8 text-center font-medium">${producto.cantidad}</span>
                            <button type="button" onclick="cambiarCantidad(${index}, 1)" class="w-6 h-6 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center justify-center text-sm">+</button>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900">S/${producto.precio_unitario.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-900 font-semibold" id="subtotal_${index}">S/${producto.subtotal.toFixed(2)}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">
                        <button type="button" onclick="eliminarProducto(${index})" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs">
                            Eliminar
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
      
        // Actualizar total
        function actualizarTotal() {
            // 🎯 MEJORADO: Asegurar que todos los subtotales sean números
            const total = productosAgregados.reduce((sum, producto) => {
                const subtotal = parseFloat(producto.subtotal) || 0;
                return sum + subtotal;
            }, 0);
            
            // Actualizar el campo oculto del total
            document.getElementById('total').value = total.toFixed(2);
            
            // Actualizar el monto del pago único
            const montoInput = document.getElementById('monto');
            if (montoInput) {
                montoInput.value = total.toFixed(2);
                // Recalcular vuelto si hay monto recibido
                calcularVuelto();
            }
            
            // Si el pago está dividido, actualizar el display de división
            const dividirPago = document.getElementById('dividir_pago').checked;
            if (dividirPago) {
                validarDivisionPago();
            }
        }
        
        // Calcular vuelto automáticamente
        function calcularVuelto() {
            const monto = parseFloat(document.getElementById('monto').value) || 0;
            const montoRecibido = parseFloat(document.getElementById('monto_recibido').value) || 0;
            
            const vuelto = montoRecibido - monto;
            document.getElementById('vuelto').value = vuelto >= 0 ? vuelto.toFixed(2) : '0.00';
            
            // Cambiar color del vuelto si es negativo
            const vueltoInput = document.getElementById('vuelto');
            if (vuelto < 0) {
                vueltoInput.classList.add('bg-red-50', 'text-red-600');
                vueltoInput.classList.remove('bg-gray-50', 'text-gray-600');
            } else {
                vueltoInput.classList.remove('bg-red-50', 'text-red-600');
                vueltoInput.classList.add('bg-gray-50', 'text-gray-600');
            }
        }
        
        // Función para actualizar botones de cantidad según stock disponible
        function actualizarBotonesCantidad(index, producto) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (!row) {
                return;
            }
            
            // Buscar los botones dentro de la fila
            const buttons = row.querySelectorAll('button');
            let btnPlus = null;
            let btnMinus = null;
            
            // Identificar los botones por su onclick
            buttons.forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick && onclick.includes(`cambiarCantidad(${index}, 1)`)) {
                    btnPlus = button;
                } else if (onclick && onclick.includes(`cambiarCantidad(${index}, -1)`)) {
                    btnMinus = button;
                }
            });
            
            if (!btnPlus || !btnMinus) return;
            
            if (producto.es_presentacion && producto.maximo_presentaciones !== undefined) {
                // Deshabilitar botón + si se alcanzó el máximo para presentaciones
                if (producto.cantidad >= producto.maximo_presentaciones) {
                    btnPlus.disabled = true;
                    btnPlus.classList.add('opacity-50', 'cursor-not-allowed');
                    btnPlus.classList.remove('hover:bg-gray-300');
                } else {
                    btnPlus.disabled = false;
                    btnPlus.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnPlus.classList.add('hover:bg-gray-300');
                }
            } else if (!producto.es_presentacion && producto.stock_disponible !== undefined) {
                // Deshabilitar botón + si se alcanzó el máximo para productos individuales
                if (producto.cantidad >= producto.stock_disponible) {
                    btnPlus.disabled = true;
                    btnPlus.classList.add('opacity-50', 'cursor-not-allowed');
                    btnPlus.classList.remove('hover:bg-gray-300');
                } else {
                    btnPlus.disabled = false;
                    btnPlus.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnPlus.classList.add('hover:bg-gray-300');
                }
            }
            
            // Deshabilitar botón - si la cantidad es 1
            if (producto.cantidad <= 1) {
                btnMinus.disabled = true;
                btnMinus.classList.add('opacity-50', 'cursor-not-allowed');
                btnMinus.classList.remove('hover:bg-gray-300');
            } else {
                btnMinus.disabled = false;
                btnMinus.classList.remove('opacity-50', 'cursor-not-allowed');
                btnMinus.classList.add('hover:bg-gray-300');
            }
        }
        
        // Mostrar mensaje de éxito
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
        
        // Manejar envío del formulario
        document.getElementById('editSaleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (productosAgregados.length === 0) {
                alert('Debe agregar al menos un producto a la venta');
                return;
            }
            
            // Validar información del pago
            const dividirPago = document.getElementById('dividir_pago').checked;
            const monto = parseFloat(document.getElementById('monto').value) || 0;
            
            if (dividirPago) {
                // Validar pago dividido
                const tipoPago1 = document.getElementById('tipo_pago_1').value;
                const tipoPago2 = document.getElementById('tipo_pago_2').value;
                const montoPago1 = parseFloat(document.getElementById('monto_pago_1').value) || 0;
                const montoPago2 = parseFloat(document.getElementById('monto_pago_2').value) || 0;
                
                if (!tipoPago1 || !tipoPago2) {
                    alert('Debe seleccionar el tipo de pago para ambos pagos');
                    return;
                }
                
                if (montoPago1 <= 0 || montoPago2 <= 0) {
                    alert('Ambos montos deben ser mayores a 0');
                    return;
                }
                
                const sumaPagos = montoPago1 + montoPago2;
                if (Math.abs(sumaPagos - monto) > 0.01) {
                    alert(`La suma de los pagos (S/${sumaPagos.toFixed(2)}) debe ser igual al total de la venta (S/${monto.toFixed(2)})`);
                    return;
                }
                
                // Validar montos recibidos si son efectivo
                if (tipoPago1 === '1') {
                    const montoRecibido1 = parseFloat(document.getElementById('monto_recibido_1').value) || 0;
                    if (montoRecibido1 < montoPago1) {
                        alert(`El monto recibido del Pago 1 (S/${montoRecibido1.toFixed(2)}) debe ser mayor o igual al monto del pago (S/${montoPago1.toFixed(2)})`);
                        return;
                    }
                }
                
                if (tipoPago2 === '1') {
                    const montoRecibido2 = parseFloat(document.getElementById('monto_recibido_2').value) || 0;
                    if (montoRecibido2 < montoPago2) {
                        alert(`El monto recibido del Pago 2 (S/${montoRecibido2.toFixed(2)}) debe ser mayor o igual al monto del pago (S/${montoPago2.toFixed(2)})`);
                        return;
                    }
                }
            } else {
                // Validar pago único
                const tipoPago = document.getElementById('tipo_pago').value;
                
                if (!tipoPago) {
                    alert('Debe seleccionar un tipo de pago');
                    return;
                }
                
                // Solo validar monto recibido si es efectivo
                if (tipoPago === '1') {
                    const montoRecibido = parseFloat(document.getElementById('monto_recibido').value) || 0;
                    if (montoRecibido < monto) {
                        alert(`El monto recibido (S/${montoRecibido.toFixed(2)}) debe ser mayor o igual al monto de la venta (S/${monto.toFixed(2)})`);
                        return;
                    }
                }
            }
            
            // Debug: Mostrar datos que se van a enviar
            console.log('Productos a enviar:', productosAgregados);
            
            // Agregar campos ocultos para los productos
            const productosData = document.getElementById('productos_data');
            productosData.innerHTML = '';
            
            productosAgregados.forEach((producto, index) => {
                console.log(`Producto ${index}:`, {
                    tipo: producto.tipo,
                    item_id: producto.item_id,
                    producto_id: producto.producto_id,
                    nombre: producto.nombre,
                    es_presentacion: producto.es_presentacion
                });
                
                productosData.innerHTML += `
                    <input type="hidden" name="productos[${index}][tipo]" value="${producto.tipo}">
                    <input type="hidden" name="productos[${index}][item_id]" value="${producto.item_id}">
                    <input type="hidden" name="productos[${index}][producto_id]" value="${producto.producto_id}">
                    <input type="hidden" name="productos[${index}][nombre]" value="${producto.nombre}">
                    <input type="hidden" name="productos[${index}][cantidad]" value="${producto.cantidad}">
                    <input type="hidden" name="productos[${index}][precio_unitario]" value="${producto.precio_unitario}">
                    <input type="hidden" name="productos[${index}][subtotal]" value="${producto.subtotal}">
                    <input type="hidden" name="productos[${index}][es_presentacion]" value="${producto.es_presentacion || false}">
                `;
            });
            
            // Agregar campos ocultos para la división de pagos
            if (dividirPago) {
                // Pago 1
                productosData.innerHTML += `
                    <input type="hidden" name="dividir_pago" value="1">
                    <input type="hidden" name="tipo_pago_1" value="${document.getElementById('tipo_pago_1').value}">
                    <input type="hidden" name="monto_pago_1" value="${document.getElementById('monto_pago_1').value}">
                    <input type="hidden" name="monto_recibido_1" value="${document.getElementById('monto_recibido_1').value || ''}">
                    <input type="hidden" name="vuelto_1" value="${document.getElementById('vuelto_1').value || '0.00'}">
                    
                    <!-- Pago 2 -->
                    <input type="hidden" name="tipo_pago_2" value="${document.getElementById('tipo_pago_2').value}">
                    <input type="hidden" name="monto_pago_2" value="${document.getElementById('monto_pago_2').value}">
                    <input type="hidden" name="monto_recibido_2" value="${document.getElementById('monto_recibido_2').value || ''}">
                    <input type="hidden" name="vuelto_2" value="${document.getElementById('vuelto_2').value || '0.00'}">
                `;
            } else {
                // Pago único
                productosData.innerHTML += `
                    <input type="hidden" name="dividir_pago" value="0">
                    <input type="hidden" name="tipo_pago" value="${document.getElementById('tipo_pago').value}">
                    <input type="hidden" name="monto" value="${document.getElementById('monto').value}">
                    <input type="hidden" name="monto_recibido" value="${document.getElementById('monto_recibido').value || ''}">
                    <input type="hidden" name="vuelto" value="${document.getElementById('vuelto').value || '0.00'}">
                `;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Actualizando...';
            submitSpinner.classList.remove('hidden');
            
            // Enviar formulario
            this.submit();
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
        
        // Función para alternar entre pago único y dividido
        function toggleDivisionPago() {
            const dividirPago = document.getElementById('dividir_pago').checked;
            const pagoUnico = document.getElementById('pago_unico');
            const pagoDividido = document.getElementById('pago_dividido');
            
            if (dividirPago) {
                pagoUnico.style.display = 'none';
                pagoDividido.style.display = 'block';
                
                // Limpiar campos del pago único y remover required
                document.getElementById('tipo_pago').value = '';
                document.getElementById('tipo_pago').required = false;
                document.getElementById('monto_recibido').value = '';
                document.getElementById('monto_recibido').required = false;
                document.getElementById('vuelto').value = '0.00';
                document.getElementById('monto_recibido_container').classList.add('hidden');
                document.getElementById('vuelto_container').classList.add('hidden');
                
                // Agregar required a campos del pago dividido
                document.getElementById('tipo_pago_1').required = true;
                document.getElementById('monto_pago_1').required = true;
                document.getElementById('tipo_pago_2').required = true;
                document.getElementById('monto_pago_2').required = true;
                
                // Distribuir automáticamente el total si está disponible
                const totalVenta = parseFloat(document.getElementById('total').value) || 0;
                if (totalVenta > 0) {
                    const mitad = totalVenta / 2;
                    document.getElementById('monto_pago_1').value = mitad.toFixed(2);
                    document.getElementById('monto_pago_2').value = mitad.toFixed(2);
                }
                
                // Actualizar display del total
                validarDivisionPago();
            } else {
                pagoUnico.style.display = 'block';
                pagoDividido.style.display = 'none';
                
                // Restaurar required en campos del pago único
                document.getElementById('tipo_pago').required = true;
                
                // Remover required de campos del pago dividido
                document.getElementById('tipo_pago_1').required = false;
                document.getElementById('monto_pago_1').required = false;
                document.getElementById('tipo_pago_2').required = false;
                document.getElementById('monto_pago_2').required = false;
                
                // Limpiar campos del pago dividido
                limpiarCamposPagoDividido();
            }
        }
        
        // Función para limpiar campos del pago dividido
        function limpiarCamposPagoDividido() {
            // Limpiar pago 1
            document.getElementById('tipo_pago_1').value = '';
            document.getElementById('monto_pago_1').value = '';
            document.getElementById('monto_recibido_1').value = '';
            document.getElementById('vuelto_1').value = '0.00';
            document.getElementById('monto_recibido_container_1').classList.add('hidden');
            document.getElementById('vuelto_container_1').classList.add('hidden');
            
            // Limpiar pago 2
            document.getElementById('tipo_pago_2').value = '';
            document.getElementById('monto_pago_2').value = '';
            document.getElementById('monto_recibido_2').value = '';
            document.getElementById('vuelto_2').value = '0.00';
            document.getElementById('monto_recibido_container_2').classList.add('hidden');
            document.getElementById('vuelto_container_2').classList.add('hidden');
        }
        
        // Función para alternar campos de monto recibido y vuelto según tipo de pago
        function toggleCamposPago(numeroPago) {
            const tipoPago = document.getElementById(`tipo_pago_${numeroPago}`).value;
            const montoRecibidoContainer = document.getElementById(`monto_recibido_container_${numeroPago}`);
            const vueltoContainer = document.getElementById(`vuelto_container_${numeroPago}`);
            const montoRecibidoInput = document.getElementById(`monto_recibido_${numeroPago}`);
            const vueltoInput = document.getElementById(`vuelto_${numeroPago}`);
            
            if (tipoPago === '1') { // Efectivo
                montoRecibidoContainer.classList.remove('hidden');
                vueltoContainer.classList.remove('hidden');
                montoRecibidoInput.required = true;
                montoRecibidoInput.value = '';
                vueltoInput.value = '0.00';
            } else { // Tarjeta o Yape/Plin
                montoRecibidoContainer.classList.add('hidden');
                vueltoInput.classList.add('hidden');
                montoRecibidoInput.required = false;
                montoRecibidoInput.value = '';
                vueltoInput.value = '0.00';
            }
        }
        
        // Función para calcular vuelto en pagos divididos
        function calcularVueltoDividido(numeroPago) {
            const tipoPago = document.getElementById(`tipo_pago_${numeroPago}`).value;
            
            // Solo calcular vuelto si es efectivo
            if (tipoPago !== '1') {
                return;
            }
            
            const monto = parseFloat(document.getElementById(`monto_pago_${numeroPago}`).value) || 0;
            const montoRecibido = parseFloat(document.getElementById(`monto_recibido_${numeroPago}`).value) || 0;
            
            const vuelto = montoRecibido - monto;
            document.getElementById(`vuelto_${numeroPago}`).value = vuelto >= 0 ? vuelto.toFixed(2) : '0.00';
            
            // Cambiar color del vuelto si es negativo
            const vueltoInput = document.getElementById(`vuelto_${numeroPago}`);
            if (vuelto < 0) {
                vueltoInput.classList.add('bg-red-50', 'text-red-600');
                vueltoInput.classList.remove('bg-gray-50', 'text-gray-600');
            } else {
                vueltoInput.classList.remove('bg-red-50', 'text-red-600');
                vueltoInput.classList.add('bg-gray-50', 'text-gray-600');
            }
        }
        
        // Función para validar la división de pagos
        function validarDivisionPago() {
            const totalVenta = parseFloat(document.getElementById('total').value) || 0;
            const montoPago1 = parseFloat(document.getElementById('monto_pago_1').value) || 0;
            const montoPago2 = parseFloat(document.getElementById('monto_pago_2').value) || 0;
            
            const sumaPagos = montoPago1 + montoPago2;
            const diferencia = totalVenta - sumaPagos;
            
            // Actualizar display
            document.getElementById('total_venta_display').textContent = `S/ ${totalVenta.toFixed(2)}`;
            document.getElementById('suma_pagos_display').textContent = `S/ ${sumaPagos.toFixed(2)}`;
            document.getElementById('diferencia_display').textContent = `S/ ${diferencia.toFixed(2)}`;
            
            // Cambiar color según la diferencia
            const diferenciaDisplay = document.getElementById('diferencia_display');
            if (Math.abs(diferencia) < 0.01) { // Exacto (considerando decimales)
                diferenciaDisplay.classList.remove('text-red-600', 'text-yellow-600');
                diferenciaDisplay.classList.add('text-green-600');
            } else if (diferencia > 0) { // Falta dinero
                diferenciaDisplay.classList.remove('text-green-600', 'text-yellow-600');
                diferenciaDisplay.classList.add('text-red-600');
            } else { // Sobra dinero
                diferenciaDisplay.classList.remove('text-green-600', 'text-red-600');
                diferenciaDisplay.classList.add('text-yellow-600');
            }
            
            // Si los montos están vacíos y hay un total, sugerir distribución automática
            if (montoPago1 === 0 && montoPago2 === 0 && totalVenta > 0) {
                const mitad = totalVenta / 2;
                document.getElementById('monto_pago_1').value = mitad.toFixed(2);
                document.getElementById('monto_pago_2').value = mitad.toFixed(2);
                // Recalcular la validación
                setTimeout(() => validarDivisionPago(), 100);
            }
        }
        
        // Función para actualizar display de división de pagos
        function actualizarDisplayDivisionPago() {
            const totalVenta = parseFloat(document.getElementById('total').value) || 0;
            document.getElementById('total_venta_display').textContent = `S/ ${totalVenta.toFixed(2)}`;
            document.getElementById('suma_pagos_display').textContent = `S/ 0.00`;
            document.getElementById('diferencia_display').textContent = `S/ ${totalVenta.toFixed(2)}`;
            document.getElementById('diferencia_display').classList.remove('text-green-600', 'text-yellow-600');
            document.getElementById('diferencia_display').classList.add('text-red-600');
        }
        
        // Función para limpiar campos del pago dividido
        function limpiarCamposPagoDividido() {
            // Limpiar pago 1
            document.getElementById('tipo_pago_1').value = '';
            document.getElementById('monto_pago_1').value = '';
            document.getElementById('monto_recibido_1').value = '';
            document.getElementById('monto_recibido_1').required = false;
            document.getElementById('vuelto_1').value = '0.00';
            document.getElementById('monto_recibido_container_1').classList.add('hidden');
            document.getElementById('vuelto_container_1').classList.add('hidden');
            
            // Limpiar pago 2
            document.getElementById('tipo_pago_2').value = '';
            document.getElementById('monto_pago_2').value = '';
            document.getElementById('monto_recibido_2').value = '';
            document.getElementById('monto_recibido_2').required = false;
            document.getElementById('vuelto_2').value = '0.00';
            document.getElementById('monto_recibido_container_2').classList.add('hidden');
            document.getElementById('vuelto_container_2').classList.add('hidden');
            
            // Resetear display
            document.getElementById('suma_pagos_display').textContent = 'S/ 0.00';
            document.getElementById('diferencia_display').textContent = 'S/ 0.00';
        }
        
        // Función para limpiar búsqueda de productos
        function limpiarBusquedaProducto() {
            // Limpiar campo de búsqueda
            document.getElementById('buscar_producto').value = '';
            
            // Ocultar resultados de búsqueda
            document.getElementById('resultados_busqueda').classList.add('hidden');
            
            // Ocultar sección de presentaciones
            document.getElementById('presentaciones_producto').classList.add('hidden');
            
            // Limpiar producto seleccionado
            productoSeleccionado = null;
        }
    </script>

    <!-- Script para cargar productos existentes -->
    <script>
        // Cargar productos existentes de la venta
        @if($sale->products->count() > 0)
            @foreach($sale->products as $product)
                productosAgregados.push({
                    tipo: 'producto',
                    item_id: {{ $product->pivot->presentacion_id ? $product->pivot->presentacion_id : $product->id }},
                    producto_id: {{ $product->id }},
                    nombre: '{{ addslashes($product->pivot->nombre_producto ?? $product->nombre) }}',
                    cantidad: {{ $product->pivot->cantidad }},
                    precio_unitario: {{ $product->pivot->precio_unitario }},
                    subtotal: {{ $product->pivot->subtotal }},
                    es_presentacion: {{ $product->pivot->presentacion_id ? 'true' : 'false' }}
                });
                contadorProductos++;
            @endforeach
        @endif
        
        // Cargar combos existentes de la venta
        @if($sale->combos->count() > 0)
            @foreach($sale->combos as $combo)
                productosAgregados.push({
                    tipo: 'combo',
                    item_id: {{ $combo->id }},
                    nombre: '{{ addslashes($combo->nombre) }}',
                    cantidad: {{ $combo->pivot->cantidad }},
                    precio_unitario: {{ $combo->pivot->precio_unitario }},
                    subtotal: {{ $combo->pivot->subtotal }}
                });
                contadorProductos++;
            @endforeach
        @endif
    </script>
</x-app-layout>
