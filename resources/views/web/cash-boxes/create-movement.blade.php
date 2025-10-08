@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Nuevo Movimiento</h1>
            <a href="{{ route('cash-boxes.session-details', $session->id) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="createMovementForm" action="{{ route('cash-boxes.store-movement', $session->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Contenido en 3 columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Columna 1: Información de la Sesión -->
                <div class="bg-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Sesión</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Caja</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $session->cashBox->nombre }}</p>
                            <p class="text-sm text-gray-500">{{ $session->cashBox->branch->nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sesión</label>
                            <p class="text-lg font-semibold text-gray-900">#{{ $session->id }}</p>
                            <p class="text-sm text-gray-500">Abierta por: {{ $session->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monto de Apertura</label>
                            <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($session->monto_apertura, 2) }}</p>
                        </div>
                        
                        <!-- Total General y Total en Caja -->
                        <div class="pt-4 border-t border-indigo-200">
                            <div class="space-y-3">
                                <!-- Total General -->
                                <div class="text-center p-3 bg-white border border-indigo-200 rounded-lg">
                                    <span class="block text-sm font-medium text-gray-700 mb-1">Total General:</span>
                                    <span class="text-lg font-bold text-indigo-600">S/ {{ number_format($session->saldo_actual, 2) }}</span>
                                    <p class="text-xs text-gray-500 mt-1">Todos los métodos</p>
                                </div>
                                
                                <!-- Total en Caja -->
                                <div class="text-center p-3 bg-white border border-green-200 rounded-lg">
                                    <span class="block text-sm font-medium text-gray-700 mb-1">Total en Caja:</span>
                                    <span class="text-lg font-bold text-green-600">S/ {{ number_format($montoEnCaja, 2) }}</span>
                                    <p class="text-xs text-gray-500 mt-1">Solo efectivo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna 2: Información del Movimiento -->
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">Información del Movimiento</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Movimiento <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="tipo" 
                                name="tipo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('tipo') border-red-500 @enderror"
                                required
                            >
                                <option value="">Seleccionar tipo</option>
                                <option value="ingreso" {{ old('tipo') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                Método de Pago
                            </label>
                            <select 
                                id="metodo_pago" 
                                name="metodo_pago" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('metodo_pago') border-red-500 @enderror"
                            >
                                <option value="efectivo" {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="billetera_virtual" {{ old('metodo_pago') == 'billetera_virtual' ? 'selected' : '' }}>Billetera Virtual</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            </select>
                            @error('metodo_pago')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Por defecto: Efectivo</p>
                        </div>

                        <div>
                            <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">
                                Monto <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">S/</span>
                                <input 
                                    type="number" 
                                    id="monto" 
                                    name="monto" 
                                    value="{{ old('monto') }}"
                                    step="0.01"
                                    min="0.01"
                                    class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('monto') border-red-500 @enderror"
                                    placeholder="0.00"
                                    required
                                >
                            </div>
                            @error('monto')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="descripcion" 
                                name="descripcion" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('descripcion') border-red-500 @enderror"
                                placeholder="Describe el motivo del movimiento..."
                                required
                            >{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Ej: Venta de productos, Compra de insumos, Retiro de efectivo, etc.</p>
                        </div>
                    </div>
                </div>

                <!-- Columna 3: Resumen del Movimiento -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Resumen del Movimiento</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                            <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <!-- Totales por Método de Pago -->
                        @if($totalesPorMetodo->count() > 0)
                            <div class="pt-4 border-t border-blue-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Totales por Método de Pago</label>
                                <div class="space-y-2">
                                    @foreach(['efectivo', 'tarjeta', 'transferencia', 'billetera_virtual'] as $metodo)
                                        @if($totalesPorMetodo->has($metodo))
                                            @php
                                                $ingresos = $totalesPorMetodo[$metodo]->where('tipo', 'ingreso')->first()->total ?? 0;
                                                $salidas = $totalesPorMetodo[$metodo]->where('tipo', 'salida')->first()->total ?? 0;
                                                $neto = $ingresos - $salidas;
                                            @endphp
                                            <div class="flex justify-between items-center text-sm p-2 bg-white border border-blue-200 rounded">
                                                <span class="font-medium text-gray-700">
                                                    @switch($metodo)
                                                        @case('efectivo')
                                                            💰 Efectivo
                                                            @break
                                                        @case('tarjeta')
                                                            💳 Tarjeta
                                                            @break
                                                        @case('transferencia')
                                                            🏦 Transferencia
                                                            @break
                                                        @case('billetera_virtual')
                                                            📱 Billetera Virtual
                                                            @break
                                                    @endswitch
                                                </span>
                                                <span class="font-semibold {{ $neto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    S/ {{ number_format($neto, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-600 font-bold">Nota:</span>
                            <p class="text-sm text-yellow-800">
                                El movimiento se registrará inmediatamente y afectará el saldo de la caja.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('cash-boxes.session-details', $session->id) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors flex items-center gap-2">
                    <span id="submitText">Registrar Movimiento</span>
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
        document.getElementById('createMovementForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Registrando...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });

        // Formatear el input de monto solo cuando pierde el foco
        document.getElementById('monto').addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value && !isNaN(value)) {
                // Asegurar que tenga máximo 2 decimales
                value = parseFloat(value).toFixed(2);
                e.target.value = value;
            }
        });

        // Cambiar el texto del botón según el tipo de movimiento
        document.getElementById('tipo').addEventListener('change', function(e) {
            const submitText = document.getElementById('submitText');
            const submitBtn = document.getElementById('submitBtn');
            
            if (e.target.value === 'ingreso') {
                submitText.textContent = 'Registrar Ingreso';
                submitBtn.className = 'px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2';
            } else if (e.target.value === 'salida') {
                submitText.textContent = 'Registrar Salida';
                submitBtn.className = 'px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2';
            } else {
                submitText.textContent = 'Registrar Movimiento';
                submitBtn.className = 'px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2';
            }
        });
    </script>
</x-app-layout>
