<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de Venta</h1>
            <div class="flex gap-2">
                <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
                <button onclick="imprimirTicket()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    @if(strtolower($sale->tipo_comprobante) === 'ticket')
                        Imprimir Ticket
                    @else
                        Imprimir {{ ucfirst($sale->tipo_comprobante) }}
                    @endif
                </button>
                @if(auth()->user()->can('editar-ventas') && $sale->estado !== 'anulada')
                    <a href="{{ route('sales.edit', $sale) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Información General de la Venta -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Información Principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Estado de la Venta -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Estado de la Venta</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($sale->estado === 'completada') bg-green-100 text-green-800
                            @elseif($sale->estado === 'anulada') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($sale->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Información del Comprobante -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Comprobante</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Comprobante</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($sale->tipo_comprobante) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Serie</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->serie ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Correlativo</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->correlativo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Venta</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->fecha_venta ? $sale->fecha_venta->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información de SUNAT (solo para boletas y facturas) -->
                @if(in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura']))
                    @php
                        $sunatResponse = \App\Models\SunatResponse::where('venta_id', $sale->id)->first();
                    @endphp
                    @if($sunatResponse)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de SUNAT</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <p class="mt-1 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($sunatResponse->exitoso) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            @if($sunatResponse->exitoso) Exitoso @else Error @endif
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Número de Documento</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $sunatResponse->numero_documento ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Código de Respuesta</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $sunatResponse->codigo_respuesta ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $sunatResponse->descripcion_respuesta ?? 'N/A' }}</p>
                                </div>
                                @if($sunatResponse->hash_documento)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Hash del Documento</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono text-xs break-all">{{ $sunatResponse->hash_documento }}</p>
                                </div>
                                @endif
                                @if($sunatResponse->xml_path)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Archivo XML</label>
                                    <a href="{{ asset($sunatResponse->xml_path) }}" target="_blank" class="mt-1 text-sm text-blue-600 hover:text-blue-800 underline">
                                        Descargar XML
                                    </a>
                                </div>
                                @endif
                                @if($sunatResponse->cdr_path)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Archivo CDR</label>
                                    <a href="{{ asset($sunatResponse->cdr_path) }}" target="_blank" class="mt-1 text-sm text-blue-600 hover:text-blue-800 underline">
                                        Descargar CDR
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h2 class="text-lg font-semibold text-yellow-800 mb-4">Información de SUNAT</h2>
                            <p class="text-sm text-yellow-700">No se encontró información de envío a SUNAT para esta {{ $sale->tipo_comprobante }}.</p>
                        </div>
                    @endif
                @endif

                <!-- Información del Cliente -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Cliente</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre del Cliente</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->client->nombre_completo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Documento</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->client->nro_documento ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->client->tipo_documento ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->client->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Productos de la Venta -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Productos de la Venta</h2>
                    
                    @if($sale->products->count() > 0 || $sale->combos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-indigo-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($sale->products as $product)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">Producto</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $product->pivot->nombre_producto ?? $product->nombre }}
                                                @if($product->pivot->presentacion_id)
                                                    <span class="text-xs text-gray-500">(Presentación)</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $product->pivot->cantidad }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($product->pivot->precio_unitario, 2) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold">S/{{ number_format($product->pivot->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    @foreach($sale->combos as $combo)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">Combo</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $combo->pivot->nombre ?? $combo->nombre }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $combo->pivot->cantidad }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($combo->pivot->precio_unitario, 2) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold">S/{{ number_format($combo->pivot->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay productos registrados en esta venta.</p>
                    @endif
                </div>

                @if($sale->observaciones)
                    <!-- Observaciones -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Observaciones</h2>
                        <p class="text-sm text-gray-900">{{ $sale->observaciones }}</p>
                    </div>
                @endif

                <!-- Información del Pago -->
                @if($sale->payments->count() > 0)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Pago</h2>
                        @foreach($sale->payments as $payment)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo de Pago</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @switch($payment->tipo_pago)
                                            @case(1)
                                                Efectivo
                                                @break
                                            @case(2)
                                                Tarjeta
                                                @break
                                            @case(3)
                                                Tarjeta
                                                @break
                                            @case(4)
                                                Transferencia
                                                @break
                                            @case(5)
                                                Billetera Virtual
                                                @break
                                            @case(6)
                                                Billetera Virtual
                                                @break
                                            @case(7)
                                                Otros
                                                @break
                                            @default
                                                N/A
                                        @endswitch
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Monto de la Venta</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">S/{{ number_format($payment->monto, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Monto Recibido</label>
                                    <p class="mt-1 text-sm text-gray-900">S/{{ number_format($payment->monto_recibido, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Vuelto</label>
                                    <p class="mt-1 text-sm text-gray-900">S/{{ number_format($payment->vuelto, 2) }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $payment->fecha_pago ? $payment->fecha_pago->format('d/m/Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Columna Derecha: Resumen y Información Adicional -->
            <div class="space-y-6">
                
                <!-- Resumen de la Venta -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Resumen de la Venta</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                            <span class="font-medium text-gray-700">Total Productos:</span>
                            <span class="font-semibold text-gray-900">{{ $sale->products->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                            <span class="font-medium text-gray-700">Total Combos:</span>
                            <span class="font-semibold text-gray-900">{{ $sale->combos->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                            <span class="font-semibold text-indigo-800 text-lg">Total General:</span>
                            <span class="font-bold text-indigo-800 text-xl">S/{{ number_format($sale->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Adicional</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->branch->nombre ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vendedor</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->user->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->updated_at ? $sale->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                @if($sale->estado !== 'anulada' && auth()->user()->can('anular-ventas'))
                    @php
                        $puedeAnular = true;
                        $mensajeAnular = '¿Estás seguro de que deseas anular esta venta? Esta acción no se puede deshacer.';
                        
                        // Validar tiempo límite para boletas y facturas
                        if (in_array(strtolower($sale->tipo_comprobante), ['boleta', 'factura'])) {
                            $fechaVenta = $sale->fecha_venta->startOfDay();
                            $fechaActual = now()->startOfDay();
                            $diasTranscurridos = $fechaVenta->diffInDays($fechaActual);
                            if ($diasTranscurridos >= 2) {
                                $puedeAnular = false;
                                $mensajeAnular = 'No se pueden anular boletas y facturas después de 2 días de la venta.';
                            }
                        }
                    @endphp
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Acciones</h2>
                        @if($puedeAnular)
                            <form action="{{ route('sales.anular', $sale) }}" method="POST" onsubmit="return confirm('{{ $mensajeAnular }}')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Anular Venta
                                </button>
                            </form>
                        @else
                            <div class="text-center">
                                <button disabled class="w-full px-4 py-2 bg-gray-400 text-gray-600 rounded-lg cursor-not-allowed" title="{{ $mensajeAnular }}">
                                    Anular Venta
                                </button>
                                <p class="mt-2 text-sm text-gray-500">{{ $mensajeAnular }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para mostrar el PDF -->
    <div id="pdfModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 h-5/6 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if(strtolower($sale->tipo_comprobante) === 'ticket')
                        Ticket de Venta
                    @else
                        {{ ucfirst($sale->tipo_comprobante) }} Electrónica
                    @endif
                </h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="pdfContainer" class="w-full h-full">
                <!-- El PDF se cargará aquí -->
            </div>
        </div>
    </div>

    <script>
        function imprimirTicket() {
            const modal = document.getElementById('pdfModal');
            const container = document.getElementById('pdfContainer');
            
            // Mostrar modal
            modal.classList.remove('hidden');
            
            // Mostrar loading
            container.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div><p class="mt-2 text-gray-600">Generando @if(strtolower($sale->tipo_comprobante) === "ticket") ticket @else {{ strtolower($sale->tipo_comprobante) }} @endif...</p></div></div>';
            
            // Cargar PDF
            fetch('{{ route("sales.ticket", $sale) }}')
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    container.innerHTML = `<iframe src="${url}" class="w-full h-full border-0"></iframe>`;
                })
                .catch(error => {
                    console.error('Error al cargar el PDF:', error);
                    container.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-red-600">Error al cargar el @if(strtolower($sale->tipo_comprobante) === "ticket") ticket @else {{ strtolower($sale->tipo_comprobante) }} @endif</p></div>';
                });
        }
        
        function cerrarModal() {
            document.getElementById('pdfModal').classList.add('hidden');
        }
        
        // Cerrar modal al hacer clic fuera de él
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    </script>
</x-app-layout>
