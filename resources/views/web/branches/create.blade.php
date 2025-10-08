<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Sucursal y Series</h1>
            <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="createBranchForm" action="{{ route('branches.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Información Básica de la Sucursal -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica de la Sucursal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                               placeholder="Ingrese el nombre de la sucursal" required autofocus>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('telefono') border-red-500 @enderror"
                               placeholder="Ingrese el teléfono de la sucursal" required>
                        @error('telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <textarea name="direccion" id="direccion" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('direccion') border-red-500 @enderror"
                                  placeholder="Ingrese la dirección completa de la sucursal" required>{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="empresa_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Empresa <span class="text-red-500">*</span>
                        </label>
                        <select name="empresa_id" id="empresa_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('empresa_id') border-red-500 @enderror"
                                required>
                            <option value="">Seleccionar empresa</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('empresa_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->razon_social }} ({{ $company->ruc }})
                                </option>
                            @endforeach
                        </select>
                        @error('empresa_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Series de Comprobantes -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-indigo-800">Series de Comprobantes</h3>
                    @can('crear-series-comprobantes')
                        <button type="button" onclick="addSeriesRow()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Agregar Serie
                        </button>
                    @endcan
                </div>
                
                <div id="seriesContainer" class="space-y-4">
                    <!-- Las series se agregarán dinámicamente aquí -->
                </div>
                
                <div class="text-sm text-gray-600 mt-4">
                    <p>💡 <strong>Tip:</strong> Puedes agregar múltiples series de comprobantes para esta sucursal. Cada serie debe tener un tipo de comprobante único.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('branches.index') }}" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Crear Sucursal y Series</span>
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
        let seriesCounter = 0;
        const canCreateSeries = @json(auth()->user()->can('crear-series-comprobantes'));

        function addSeriesRow() {
            seriesCounter++;
            const container = document.getElementById('seriesContainer');
            
            const seriesRow = document.createElement('div');
            seriesRow.className = 'bg-white rounded-lg p-4 border border-gray-200';
            seriesRow.id = `series-row-${seriesCounter}`;
            
            seriesRow.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-800">Serie ${seriesCounter}</h4>
                    <button type="button" onclick="removeSeriesRow(${seriesCounter})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="series_${seriesCounter}_tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Comprobante <span class="text-red-500">*</span>
                        </label>
                        <select name="series[${seriesCounter}][tipo_comprobante]" id="series_${seriesCounter}_tipo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Factura">Factura</option>
                            <option value="Boleta">Boleta</option>
                            <option value="Nota de Crédito - Factura">Nota de Crédito - Factura</option>
                            <option value="Nota de Crédito - Boleta">Nota de Crédito - Boleta</option>
                        </select>
                    </div>
                    <div>
                        <label for="series_${seriesCounter}_serie" class="block text-sm font-medium text-gray-700 mb-1">
                            Serie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="series[${seriesCounter}][serie]" id="series_${seriesCounter}_serie" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ej: F001" required>
                    </div>
                    <div>
                        <label for="series_${seriesCounter}_correlativo" class="block text-sm font-medium text-gray-700 mb-1">
                            Último Correlativo <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="series[${seriesCounter}][ultimo_correlativo]" id="series_${seriesCounter}_correlativo" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="0" min="0" value="0" required>
                    </div>
                </div>
            `;
            
            container.appendChild(seriesRow);
        }

        function removeSeriesRow(counter) {
            const seriesRow = document.getElementById(`series-row-${counter}`);
            if (seriesRow) {
                seriesRow.remove();
            }
        }

        document.getElementById('createBranchForm').addEventListener('submit', function(e) {
            // Validar que al menos hay una serie si el usuario tiene permisos para crear series
            const seriesRows = document.querySelectorAll('[id^="series-row-"]');
            if (seriesRows.length === 0 && canCreateSeries) {
                if (!confirm('No has agregado ninguna serie de comprobante. ¿Deseas continuar sin series?')) {
                    e.preventDefault();
                    return;
                }
            }

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