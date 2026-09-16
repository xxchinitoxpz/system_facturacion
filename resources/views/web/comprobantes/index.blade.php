<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-indigo-800">Comprobantes</h1>
                <p class="text-sm text-gray-500 mt-1">Boletas y facturas electrónicas</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <form method="GET" action="{{ route('comprobantes.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Buscar por serie, correlativo, cliente o documento..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <div>
                        <select name="tipo_comprobante" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Boleta y factura</option>
                            @foreach($tipos as $tipoOption)
                                <option value="{{ $tipoOption }}" {{ $tipo_comprobante == $tipoOption ? 'selected' : '' }}>
                                    {{ ucfirst($tipoOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $estadoOption)
                                <option value="{{ $estadoOption }}" {{ $estado == $estadoOption ? 'selected' : '' }}>
                                    {{ ucfirst($estadoOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <select name="cliente_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los clientes</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $cliente_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(auth()->user()->sucursal_id === null)
                        <div>
                            <select name="sucursal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas las sucursales</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $sucursal_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Buscar
                    </button>
                    @if($search || $estado || $tipo_comprobante || $cliente_id || $sucursal_id || $fecha_inicio || $fecha_fin)
                        <a href="{{ route('comprobantes.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-indigo-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprobante</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SUNAT</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        @php
                            $sunat = $sale->sunatResponses->first();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $sale->fecha_venta->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div class="font-medium">{{ $sale->client->nombre_completo ?? '—' }}</div>
                                <div class="text-gray-500 text-xs">
                                    {{ $sale->client->tipo_documento ?? '' }}: {{ $sale->client->nro_documento ?? '' }}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <div class="font-medium capitalize">{{ $sale->tipo_comprobante }}</div>
                                @if($sale->serie && $sale->correlativo)
                                    <div class="text-gray-500 text-xs">{{ $sale->serie }}-{{ $sale->correlativo }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $sale->branch->nombre ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold">
                                S/{{ number_format($sale->total, 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if($sale->estado === 'completada')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Completada</span>
                                @elseif($sale->estado === 'anulada')
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Anulada</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if(!$sunat)
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full" title="Sin respuesta registrada">Sin envío</span>
                                @elseif($sunat->exitoso)
                                    <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full" title="{{ $sunat->descripcion_respuesta }}">Aceptado</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-rose-100 text-rose-800 rounded-full" title="{{ $sunat->descripcion_respuesta }}">Error</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $sale->user->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('sales.show', $sale) }}"
                                       class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                       title="Ver detalles">
                                        Ver
                                    </a>
                                    <a href="{{ route('sales.ticket', $sale) }}"
                                       target="_blank"
                                       class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors text-xs"
                                       title="Ver PDF">
                                        PDF
                                    </a>
                                    @can('anular-ventas')
                                        @if($sale->estado !== 'anulada')
                                            @php
                                                $puedeAnular = true;
                                                $mensajeAnular = 'Anular venta';
                                                $fechaVenta = $sale->fecha_venta->copy()->startOfDay();
                                                $diasTranscurridos = $fechaVenta->diffInDays(now()->startOfDay());
                                                if ($diasTranscurridos >= 2) {
                                                    $puedeAnular = false;
                                                    $mensajeAnular = 'No se pueden anular boletas y facturas después de 2 días';
                                                }
                                            @endphp
                                            @if($puedeAnular)
                                                <button
                                                    onclick="confirmAnular(this)"
                                                    data-id="{{ $sale->id }}"
                                                    data-total="{{ $sale->total }}"
                                                    data-sale="{{ $sale->id }}"
                                                    class="px-3 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors text-xs"
                                                    title="Anular venta y enviar nota de crédito a SUNAT">
                                                    Anular
                                                </button>
                                            @else
                                                <span class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs cursor-not-allowed" title="{{ $mensajeAnular }}">
                                                    Anular
                                                </span>
                                            @endif
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron comprobantes con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    <div id="anularModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar anulación</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            ¿Anular el comprobante con total S/<span id="anularTotal" class="font-semibold"></span>?
                            <br>
                            <span class="text-orange-600">Se enviará nota de crédito a SUNAT.</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button id="confirmAnular" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                                <span id="anularText">Sí, anular</span>
                                <div id="anularSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                            <button id="cancelAnular" onclick="closeAnularModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="anularForm" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

    <script>
        function confirmAnular(button) {
            const id = button.getAttribute('data-id');
            const total = button.getAttribute('data-total');
            const saleId = button.getAttribute('data-sale');
            document.getElementById('anularTotal').textContent = total;
            document.getElementById('anularModal').classList.remove('hidden');

            document.getElementById('confirmAnular').onclick = function() {
                const confirmBtn = document.getElementById('confirmAnular');
                const anularText = document.getElementById('anularText');
                const anularSpinner = document.getElementById('anularSpinner');
                const cancelBtn = document.getElementById('cancelAnular');

                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                cancelBtn.disabled = true;
                cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                anularText.textContent = 'Anulando y enviando a SUNAT...';
                anularSpinner.classList.remove('hidden');

                const form = document.getElementById('anularForm');
                form.action = `/sales/${id}/anular`;
                form.submit();

                setTimeout(() => {
                    enviarNotaASunatDespuesDeAnular(saleId);
                }, 2000);
            };
        }

        function closeAnularModal() {
            document.getElementById('anularModal').classList.add('hidden');
            resetAnularModal();
        }

        function resetAnularModal() {
            const confirmBtn = document.getElementById('confirmAnular');
            const anularText = document.getElementById('anularText');
            const anularSpinner = document.getElementById('anularSpinner');
            const cancelBtn = document.getElementById('cancelAnular');

            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            anularText.textContent = 'Sí, anular';
            anularSpinner.classList.add('hidden');
        }

        document.getElementById('anularModal').addEventListener('click', function(e) {
            if (e.target === this) closeAnularModal();
        });

        function enviarNotaASunatDespuesDeAnular(saleId) {
            fetch(`/sales/${saleId}/enviar-nota-sunat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error al enviar nota de crédito a SUNAT: ' + data.error);
                }
            })
            .catch(() => alert('Error al enviar la nota de crédito a SUNAT'));
        }
    </script>
</x-app-layout>
