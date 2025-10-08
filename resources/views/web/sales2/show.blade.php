<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles de Venta #{{ $sale->id }}</h1>
            <a href="{{ route('sales2.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver
            </a>
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

        <!-- Información General -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Datos de la Venta -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información de la Venta</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID:</span>
                        <span class="font-medium">#{{ $sale->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha:</span>
                        <span class="font-medium">{{ $sale->fecha_venta->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipo de Comprobante:</span>
                        <span class="font-medium">{{ ucfirst($sale->tipo_comprobante) }}</span>
                    </div>
                    @if($sale->serie && $sale->correlativo)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Serie/Correlativo:</span>
                            <span class="font-medium">{{ $sale->serie }}-{{ $sale->correlativo }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        @if($sale->estado === 'completada')
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Completada
                            </span>
                        @elseif($sale->estado === 'anulada')
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                Anulada
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                Pendiente
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-lg text-indigo-600">S/{{ number_format($sale->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos del Cliente -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Cliente</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-medium">{{ $sale->client->nombre_completo }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Documento:</span>
                        <span class="font-medium">{{ $sale->client->tipo_documento }}: {{ $sale->client->nro_documento }}</span>
                    </div>
                    @if($sale->client->telefono)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Teléfono:</span>
                            <span class="font-medium">{{ $sale->client->telefono }}</span>
                        </div>
                    @endif
                    @if($sale->client->email)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $sale->client->email }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos de la Venta -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-indigo-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sale->products as $product)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    <div>
                                        <div class="font-medium">{{ $product->pivot->nombre_producto }}</div>
                                        @if($product->pivot->fecha_vencimiento)
                                            @php
                                                $fechaVencimiento = is_string($product->pivot->fecha_vencimiento) 
                                                    ? json_decode($product->pivot->fecha_vencimiento, true) 
                                                    : $product->pivot->fecha_vencimiento;
                                                
                                                if (is_array($fechaVencimiento) && isset($fechaVencimiento[0]['fecha_vencimiento'])) {
                                                    $fecha = $fechaVencimiento[0]['fecha_vencimiento'];
                                                } else {
                                                    $fecha = null;
                                                }
                                            @endphp
                                            @if($fecha)
                                                <div class="text-gray-500 text-xs">Vence: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $product->pivot->cantidad }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($product->pivot->precio_unitario, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 font-semibold">S/{{ number_format($product->pivot->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagos de la Venta -->
        @if($sale->payments->count() > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pagos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-indigo-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Pago</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recibido</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vuelto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sale->payments as $payment)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        @switch($payment->tipo_pago)
                                            @case(1)
                                                Efectivo
                                                @break
                                            @case(2)
                                                Tarjeta
                                                @break
                                            @case(5)
                                                Yape / Plin
                                                @break
                                            @default
                                                {{ $payment->tipo_pago }}
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($payment->monto, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($payment->monto_recibido, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">S/{{ number_format($payment->vuelto, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $payment->fecha_pago->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Información Adicional -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Sucursal y Usuario -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Adicional</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sucursal:</span>
                        <span class="font-medium">{{ $sale->branch->nombre }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usuario:</span>
                        <span class="font-medium">{{ $sale->user->name }}</span>
                    </div>
                    @if($sale->observaciones)
                        <div class="mt-4">
                            <span class="text-gray-600 block mb-2">Observaciones:</span>
                            <p class="text-sm text-gray-800 bg-white p-3 rounded border">{{ $sale->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
