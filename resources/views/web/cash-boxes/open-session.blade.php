@php use Illuminate\Support\Facades\Auth; @endphp
<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Abrir Sesión de Caja</h1>
            <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
        </div>

        <!-- Información de la Caja -->
        <div class="bg-indigo-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Caja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Caja</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cashBox->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cashBox->branch->nombre }}</p>
                </div>
            </div>
            @if($cashBox->descripcion)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <p class="text-gray-600">{{ $cashBox->descripcion }}</p>
                </div>
            @endif
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

        <form id="openSessionForm" action="{{ route('cash-boxes.store-session', $cashBox->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Información de Apertura -->
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-800 mb-4">Información de Apertura</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="monto_apertura" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto de Apertura <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">S/</span>
                            <input 
                                type="number" 
                                id="monto_apertura" 
                                name="monto_apertura" 
                                value="{{ old('monto_apertura', $montoSugerido) }}"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed @error('monto_apertura') border-red-500 @enderror"
                                placeholder="0.00"
                                required
                                readonly
                            >
                        </div>
                        @error('monto_apertura')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($esPrimeraVez)
                            <p class="mt-1 text-sm text-gray-500">Ingresa el monto inicial que tendrá la caja (primera apertura)</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Efectivo disponible calculado automáticamente: S/ {{ number_format($montoSugerido, 2) }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                @if(!$esPrimeraVez)
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-green-600 font-bold">Información:</span>
                            <div class="text-sm text-green-800">
                                <p><strong>Última sesión cerrada:</strong> {{ $ultimaSesion->fecha_hora_cierre->format('d/m/Y H:i') }}</p>
                                <p><strong>Efectivo disponible:</strong> S/ {{ number_format($montoSugerido, 2) }}</p>
                                <p><strong>Cálculo:</strong> Monto apertura + Ingresos efectivo - Salidas efectivo</p>
                                <p><em>Nota: Solo se considera efectivo físico, no incluye tarjetas, transferencias ni billeteras virtuales.</em></p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600 font-bold">Nota:</span>
                            <p class="text-sm text-blue-800">
                                Esta es la primera vez que se abre esta caja. Ingresa el monto inicial que tendrá la caja.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <span class="text-blue-600 font-bold">Nota:</span>
                        <p class="text-sm text-blue-800">
                            Al abrir la caja se creará automáticamente un movimiento de apertura con el monto especificado.
                        </p>
                    </div>
                </div>
            </div>

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
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            Abierta
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sesión</label>
                        <p class="text-sm text-gray-600">Nueva sesión</p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('cash-boxes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors flex items-center gap-2">
                    <span id="submitText">Abrir Caja</span>
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
        document.getElementById('openSessionForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Abriendo...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });

        // Formatear el input de monto solo cuando pierde el foco
        document.getElementById('monto_apertura').addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value && !isNaN(value)) {
                // Asegurar que tenga máximo 2 decimales
                value = parseFloat(value).toFixed(2);
                e.target.value = value;
            }
        });
    </script>
</x-app-layout>
