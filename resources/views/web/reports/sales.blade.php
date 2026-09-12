<x-app-layout>
    <x-slot name="header">
        Reporte de Ventas
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                    <select name="preset" id="preset" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" onchange="toggleCustomDates()">
                        <option value="this_year" {{ $preset === 'this_year' ? 'selected' : '' }}>Este año</option>
                        <option value="last_year" {{ $preset === 'last_year' ? 'selected' : '' }}>Año anterior</option>
                        <option value="last_12_months" {{ $preset === 'last_12_months' ? 'selected' : '' }}>Últimos 12 meses</option>
                        <option value="custom" {{ $preset === 'custom' ? 'selected' : '' }}>Rango personalizado</option>
                    </select>
                </div>

                <div id="custom-dates-inicio" class="{{ $preset === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div id="custom-dates-fin" class="{{ $preset === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                @if(auth()->user()->sucursal_id === null)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <select name="sucursal_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" {{ (string) $sucursalId === (string) $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        Aplicar filtros
                    </button>
                </div>
            </form>
            <p class="text-sm text-gray-500 mt-3">
                Periodo: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</span>
                —
                <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
                · Solo ventas completadas
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <p class="text-green-100 text-sm font-medium">Total vendido</p>
                <p class="text-3xl font-bold mt-1">S/ {{ number_format($totalVendido, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                <p class="text-indigo-100 text-sm font-medium">Cantidad de ventas</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($cantidadVentas) }}</p>
            </div>
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl shadow-lg p-6 text-white">
                <p class="text-cyan-100 text-sm font-medium">Ticket promedio</p>
                <p class="text-3xl font-bold mt-1">S/ {{ number_format($ticketPromedio, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br {{ $variacionPorcentaje >= 0 ? 'from-emerald-500 to-emerald-600' : 'from-rose-500 to-rose-600' }} rounded-2xl shadow-lg p-6 text-white">
                <p class="text-white/80 text-sm font-medium">Vs periodo anterior</p>
                <p class="text-3xl font-bold mt-1">{{ $variacionPorcentaje >= 0 ? '+' : '' }}{{ number_format($variacionPorcentaje, 1) }}%</p>
                <p class="text-white/70 text-xs mt-1">Anterior: S/ {{ number_format($prevTotal, 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Evolución mensual</h3>
                <canvas id="chartMensual" height="140"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comparación anual</h3>
                <canvas id="chartAnual" height="140"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Por tipo de comprobante</h3>
                <canvas id="chartComprobante" height="140"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Por sucursal</h3>
                <canvas id="chartSucursal" height="140"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow overflow-hidden xl:col-span-1">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Detalle mensual</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Mes</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">Ticket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($porMes as $row)
                                <tr>
                                    <td class="px-4 py-2">{{ $row['etiqueta'] }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($row['total'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ $row['cantidad'] }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($row['ticket_promedio'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Por sucursal</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Sucursal</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">Ticket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($porSucursal as $row)
                                <tr>
                                    <td class="px-4 py-2">{{ $row['sucursal'] }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($row['total'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ $row['cantidad'] }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($row['ticket_promedio'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Por comprobante</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">#</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($porComprobante as $row)
                                <tr>
                                    <td class="px-4 py-2">{{ $row['etiqueta'] }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($row['total'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ $row['cantidad'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Sin datos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        function toggleCustomDates() {
            const preset = document.getElementById('preset').value;
            const show = preset === 'custom';
            document.getElementById('custom-dates-inicio').classList.toggle('hidden', !show);
            document.getElementById('custom-dates-fin').classList.toggle('hidden', !show);
        }

        const money = (v) => 'S/ ' + Number(v).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const colors = ['#4f46e5', '#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'];

        const chartMensual = @json($chartMensual);
        const chartAnual = @json($chartAnual);
        const chartComprobante = @json($chartComprobante);
        const chartSucursal = @json($chartSucursal);

        new Chart(document.getElementById('chartMensual'), {
            type: 'line',
            data: {
                labels: chartMensual.labels,
                datasets: [{
                    label: 'Total vendido',
                    data: chartMensual.totales,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.15)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: (v) => money(v) } } }
            }
        });

        new Chart(document.getElementById('chartAnual'), {
            type: 'bar',
            data: {
                labels: chartAnual.labels,
                datasets: [{
                    label: 'Total anual',
                    data: chartAnual.totales,
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: (v) => money(v) } } }
            }
        });

        new Chart(document.getElementById('chartComprobante'), {
            type: 'doughnut',
            data: {
                labels: chartComprobante.labels,
                datasets: [{
                    data: chartComprobante.totales,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${money(ctx.raw)}`
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartSucursal'), {
            type: 'bar',
            data: {
                labels: chartSucursal.labels,
                datasets: [{
                    label: 'Total',
                    data: chartSucursal.totales,
                    backgroundColor: '#06b6d4'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { ticks: { callback: (v) => money(v) } } }
            }
        });
    </script>
    @endpush
</x-app-layout>
