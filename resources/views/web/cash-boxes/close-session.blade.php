@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Cerrar Sesión de Caja</h1>
            <a href="{{ route('cash-boxes.session-details', $session->id) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        <!-- Información de la Sesión -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Sesión</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Caja</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $session->cashBox->nombre }}</p>
                    <p class="text-sm text-gray-500">{{ $session->cashBox->branch->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Abierta por</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $session->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $session->user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Apertura</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $session->fecha_hora_apertura->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $session->fecha_hora_apertura->format('H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto de Apertura</label>
                    <p class="text-2xl font-bold text-green-600">S/ {{ number_format($session->monto_apertura, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Layout de dos columnas: Resumen Financiero | Información de Cierre -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Resumen Financiero -->
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-800 mb-4">Resumen Financiero</h3>
                
                <!-- Monto de Apertura -->
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Monto de Apertura:</span>
                        <span class="text-lg font-bold text-blue-600">S/ {{ number_format($session->monto_apertura, 2) }}</span>
                    </div>
                </div>
                
                <!-- Totales por Método de Pago -->
                @if($totalesPorMetodo->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Movimientos por Método de Pago:</h4>
                        <div class="space-y-2">
                            @foreach(['efectivo', 'tarjeta', 'transferencia', 'billetera_virtual'] as $metodo)
                                @if($totalesPorMetodo->has($metodo))
                                    @php
                                        $ingresos = $totalesPorMetodo[$metodo]->where('tipo', 'ingreso')->first()->total ?? 0;
                                        $salidas = $totalesPorMetodo[$metodo]->where('tipo', 'salida')->first()->total ?? 0;
                                        $neto = $ingresos - $salidas;
                                    @endphp
                                    <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-700">
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
                                            <span class="text-xs text-gray-500">
                                                (Ingresos: S/{{ number_format($ingresos, 2) }} | Salidas: S/{{ number_format($salidas, 2) }})
                                            </span>
                                        </div>
                                        <span class="font-semibold {{ $neto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/ {{ number_format($neto, 2) }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Total General y Total en Caja -->
                <div class="pt-4 border-t border-green-200">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Total General -->
                        <div class="text-center p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Total General:</span>
                            <span class="text-xl font-bold text-indigo-600">S/ {{ number_format($session->saldo_actual, 2) }}</span>
                            <p class="text-xs text-gray-500 mt-1">
                                Apertura + Ingresos - Salidas
                            </p>
                        </div>
                        
                        <!-- Total en Caja (Efectivo) -->
                        <div class="text-center p-3 bg-green-50 border border-green-200 rounded-lg">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Total en Caja:</span>
                            <span class="text-xl font-bold text-green-600">S/ {{ number_format($montoCierreEfectivo, 2) }}</span>
                            <p class="text-xs text-gray-500 mt-1">
                                Solo efectivo físico
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Cálculo Total: Apertura (S/{{ number_format($session->monto_apertura, 2) }}) + Ingresos (S/{{ number_format($session->total_ingresos, 2) }}) - Salidas (S/{{ number_format($session->total_salidas, 2) }})
                    </p>
                </div>
            </div>

            <!-- Información de Cierre -->
            <div class="bg-red-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-4">Información de Cierre</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="monto_cierre_display" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto de Cierre (Efectivo) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">S/</span>
                            <input 
                                type="text" 
                                id="monto_cierre_display" 
                                value="{{ number_format($montoCierreEfectivo, 2) }}"
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                                readonly
                                disabled
                            >
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Monto calculado automáticamente: Apertura + Ingresos efectivo - Salidas efectivo</p>
                    </div>

                    <!-- Checkbox para descuadre -->
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="tiene_descuadre" 
                                name="tiene_descuadre" 
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                onchange="toggleDescuadreField()"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">Hay descuadre en caja</span>
                        </label>
                    </div>

                    <!-- Campo de descripción de descuadre (oculto por defecto) -->
                    <div id="descuadre_field" class="hidden">
                        <label for="descuadre_descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción del Descuadre <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="descuadre_descripcion" 
                            name="descuadre_descripcion" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('descuadre_descripcion') border-red-500 @enderror"
                            placeholder="Describe el descuadre encontrado en caja..."
                        >{{ old('descuadre_descripcion') }}</textarea>
                        @error('descuadre_descripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Explica qué pasó con el dinero faltante o sobrante</p>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <span class="text-yellow-600 font-bold">Importante:</span>
                        <p class="text-sm text-yellow-800">
                            Al cerrar la caja se creará automáticamente un movimiento de cierre. 
                            Asegúrate de que el monto de cierre coincida con el dinero físico en la caja.
                        </p>
                    </div>
                </div>
            </div>
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

        <form id="closeSessionForm" action="{{ route('cash-boxes.update-session', $session->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Campo hidden para el monto de cierre -->
            <input type="hidden" name="monto_cierre" value="{{ $montoCierreEfectivo }}">

            <!-- Información Adicional -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Adicional</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora</label>
                        <p class="text-sm text-gray-600">{{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                            Cerrada
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                        <p class="text-sm text-gray-600">{{ $session->fecha_hora_apertura->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('cash-boxes.session-details', $session->id) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors flex items-center gap-2">
                    <span id="submitText">Cerrar Caja</span>
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
        document.getElementById('closeSessionForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Cerrando...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });

        // El campo monto_cierre ahora es de solo lectura, no necesita formateo

        // Función para mostrar/ocultar campo de descuadre
        function toggleDescuadreField() {
            const checkbox = document.getElementById('tiene_descuadre');
            const descuadreField = document.getElementById('descuadre_field');
            const descuadreTextarea = document.getElementById('descuadre_descripcion');
            
            if (checkbox.checked) {
                descuadreField.classList.remove('hidden');
                descuadreTextarea.required = true;
            } else {
                descuadreField.classList.add('hidden');
                descuadreTextarea.required = false;
                descuadreTextarea.value = '';
            }
        }
    </script>
</x-app-layout>
