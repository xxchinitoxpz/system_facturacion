<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Sucursal y Series</h1>
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

        <form id="editBranchForm" action="{{ route('branches.update', $branch->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Información Básica de la Sucursal -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica de la Sucursal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $branch->nombre) }}" 
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
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $branch->telefono) }}" 
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
                                  placeholder="Ingrese la dirección completa de la sucursal" required>{{ old('direccion', $branch->direccion) }}</textarea>
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
                                <option value="{{ $company->id }}" {{ old('empresa_id', $branch->empresa_id) == $company->id ? 'selected' : '' }}>
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
                
                <!-- Aviso importante -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800">Aviso Importante</h4>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p><strong>⚠️ Atención:</strong> Modificar las series de comprobantes puede afectar la facturación existente. Los cambios en las series pueden causar problemas con documentos ya emitidos.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="seriesContainer" class="space-y-4">
                    @if($branch->documentSeries->count() > 0)
                        @foreach($branch->documentSeries as $index => $series)
                            <div class="bg-white rounded-lg p-4 border border-gray-200" id="series-row-{{ $index + 1 }}">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-800">Serie {{ $index + 1 }}</h4>
                                    @can('eliminar-series-comprobantes')
                                        <button type="button" onclick="removeSeriesRow({{ $index + 1 }})" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="series_{{ $index + 1 }}_tipo" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tipo de Comprobante <span class="text-red-500">*</span>
                                        </label>
                                        <select name="series[{{ $index + 1 }}][tipo_comprobante]" id="series_{{ $index + 1 }}_tipo" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                            <option value="">Seleccionar tipo</option>
                                            <option value="Factura" {{ $series->tipo_comprobante == 'Factura' ? 'selected' : '' }}>Factura</option>
                                            <option value="Boleta" {{ $series->tipo_comprobante == 'Boleta' ? 'selected' : '' }}>Boleta</option>
                                            <option value="Nota de Crédito - Factura" {{ $series->tipo_comprobante == 'Nota de Crédito - Factura' ? 'selected' : '' }}>Nota de Crédito - Factura</option>
                                            <option value="Nota de Crédito - Boleta" {{ $series->tipo_comprobante == 'Nota de Crédito - Boleta' ? 'selected' : '' }}>Nota de Crédito - Boleta</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="series_{{ $index + 1 }}_serie" class="block text-sm font-medium text-gray-700 mb-1">
                                            Serie <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="series[{{ $index + 1 }}][serie]" id="series_{{ $index + 1 }}_serie" 
                                               value="{{ $series->serie }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="Ej: F001" required>
                                    </div>
                                    <div>
                                        <label for="series_{{ $index + 1 }}_correlativo" class="block text-sm font-medium text-gray-700 mb-1">
                                            Último Correlativo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="series[{{ $index + 1 }}][ultimo_correlativo]" id="series_{{ $index + 1 }}_correlativo" 
                                               value="{{ $series->ultimo_correlativo }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="0" min="0" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
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
                    <span id="submitText">Actualizar Sucursal y Series</span>
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
        let seriesCounter = {{ $branch->documentSeries->count() }};
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

        document.getElementById('editBranchForm').addEventListener('submit', function(e) {
            // Mostrar confirmación por el aviso
            if (!confirm('⚠️ ADVERTENCIA: Modificar las series de comprobantes puede afectar la facturación existente. ¿Estás seguro de que quieres continuar?')) {
                e.preventDefault();
                return;
            }

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