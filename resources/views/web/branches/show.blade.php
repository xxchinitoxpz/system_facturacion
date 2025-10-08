<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de la Sucursal</h1>
            <div class="flex gap-2">
                <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
                @can('editar-sucursales')
                    <a href="{{ route('branches.edit', $branch->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
            </div>
        </div>

        <div class="space-y-6">
            <!-- Información Básica -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información Básica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nombre</label>
                        <p class="mt-1 text-gray-800">{{ $branch->nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Teléfono</label>
                        <p class="mt-1 text-gray-800">{{ $branch->telefono }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Dirección</label>
                        <p class="mt-1 text-gray-800">{{ $branch->direccion }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Empresa</label>
                        <p class="mt-1 text-gray-800">{{ $branch->company->razon_social ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">RUC de la Empresa</label>
                        <p class="mt-1 text-gray-800">{{ $branch->company->ruc ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Series de Comprobantes -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-indigo-800">Series de Comprobantes</h3>
                    @can('crear-series-comprobantes')
                        <button onclick="openSeriesModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Agregar Serie
                        </button>
                    @endcan
                </div>
                
                @if($branch->documentSeries->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipo Comprobante</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Serie</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Último Correlativo</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($branch->documentSeries as $series)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $series->tipo_comprobante }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $series->serie }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $series->ultimo_correlativo }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            <div class="flex gap-2">
                                                @can('editar-series-comprobantes')
                                                    <button onclick="editSeriesModal({{ $series->id }}, '{{ $series->tipo_comprobante }}', '{{ $series->serie }}', {{ $series->ultimo_correlativo }})" 
                                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs" title="Editar">
                                                        Editar
                                                    </button>
                                                @endcan
                                                @can('eliminar-series-comprobantes')
                                                    <button onclick="deleteSeries({{ $series->id }})" 
                                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs" title="Eliminar">
                                                        Eliminar
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No hay series de comprobantes registradas para esta sucursal.</p>
                @endif
            </div>

            <!-- Información del Sistema -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">ID de la Sucursal</label>
                        <p class="mt-1 text-gray-800">{{ $branch->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fecha de Creación</label>
                        <p class="mt-1 text-gray-800">{{ $branch->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Última Actualización</label>
                        <p class="mt-1 text-gray-800">{{ $branch->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tiempo Transcurrido</label>
                        <p class="mt-1 text-gray-800">{{ $branch->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Sucursal creada el {{ $branch->created_at->diffForHumans() }}
                </div>
                <div class="flex gap-2">
                    @can('eliminar-sucursales')
                        <form id="deleteBranchForm" action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" id="deleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                                <span id="deleteText">Eliminar Sucursal</span>
                                <div id="deleteSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar/editar series -->
    <div id="seriesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" id="seriesModalTitle">Agregar Serie de Comprobante</h3>
                    <form id="seriesForm" method="POST">
                        @csrf
                        <input type="hidden" name="sucursal_id" value="{{ $branch->id }}">
                        <div class="space-y-4">
                            <div>
                                <label for="tipo_comprobante" class="block text-sm font-medium text-gray-700">Tipo de Comprobante</label>
                                <input type="text" name="tipo_comprobante" id="tipo_comprobante" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="serie" class="block text-sm font-medium text-gray-700">Serie</label>
                                <input type="text" name="serie" id="serie" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="ultimo_correlativo" class="block text-sm font-medium text-gray-700">Último Correlativo</label>
                                <input type="number" name="ultimo_correlativo" id="ultimo_correlativo" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end mt-6">
                            <button type="button" onclick="closeSeriesModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSeriesModal() {
            document.getElementById('seriesModal').classList.remove('hidden');
            document.getElementById('seriesModalTitle').textContent = 'Agregar Serie de Comprobante';
            document.getElementById('seriesForm').action = '{{ route("branches.series.store", $branch->id) }}';
            document.getElementById('seriesForm').method = 'POST';
            document.getElementById('tipo_comprobante').value = '';
            document.getElementById('serie').value = '';
            document.getElementById('ultimo_correlativo').value = '0';
        }

        function editSeriesModal(id, tipoComprobante, serie, ultimoCorrelativo) {
            document.getElementById('seriesModal').classList.remove('hidden');
            document.getElementById('seriesModalTitle').textContent = 'Editar Serie de Comprobante';
            document.getElementById('seriesForm').action = `/branches/{{ $branch->id }}/series/${id}`;
            document.getElementById('seriesForm').method = 'POST';
            
            // Agregar método PUT
            let methodField = document.getElementById('seriesForm').querySelector('input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                document.getElementById('seriesForm').appendChild(methodField);
            }
            methodField.value = 'PUT';
            
            document.getElementById('tipo_comprobante').value = tipoComprobante;
            document.getElementById('serie').value = serie;
            document.getElementById('ultimo_correlativo').value = ultimoCorrelativo;
        }

        function closeSeriesModal() {
            document.getElementById('seriesModal').classList.add('hidden');
            // Remover método PUT si existe
            const methodField = document.getElementById('seriesForm').querySelector('input[name="_method"]');
            if (methodField) {
                methodField.remove();
            }
        }

        function deleteSeries(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta serie de comprobante?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/branches/{{ $branch->id }}/series/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.getElementById('deleteBranchForm').addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta sucursal? Esta acción no se puede deshacer.')) {
                e.preventDefault();
                return;
            }
            
            const deleteBtn = document.getElementById('deleteBtn');
            const deleteText = document.getElementById('deleteText');
            const deleteSpinner = document.getElementById('deleteSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            deleteBtn.disabled = true;
            deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            deleteText.textContent = 'Eliminando...';
            deleteSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('seriesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSeriesModal();
            }
        });
    </script>
</x-app-layout> 