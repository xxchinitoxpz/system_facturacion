<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalle de Compra #{{ $purchase->id }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
                @can('editar-compras')
                    <a href="{{ route('purchases.edit', $purchase) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                @can('eliminar-compras')
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta compra?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Eliminar
                        </button>
                    </form>
                @endcan
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

        <!-- Información de la compra -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Información general -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información General</h3>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-gray-700">ID de Compra:</span>
                        <span class="ml-2 text-gray-900">#{{ $purchase->id }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Fecha de Compra:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->fecha_compra->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Total:</span>
                        <span class="ml-2 text-gray-900 font-semibold">S/ {{ number_format($purchase->total, 2) }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Observaciones:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->observaciones ?: 'Sin observaciones' }}</span>
                    </div>
                </div>
            </div>

            <!-- Información del proveedor y sucursal -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Proveedor y Sucursal</h3>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-gray-700">Proveedor:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->supplier->nombre_completo }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Documento:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->supplier->tipo_documento }}: {{ $purchase->supplier->nro_documento }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Sucursal:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->branch->nombre }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Usuario:</span>
                        <span class="ml-2 text-gray-900">{{ $purchase->user->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comprobante -->
        @if($purchase->comprobante_path)
            <div class="mb-6 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comprobante</h3>
                <div class="flex items-center gap-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700">Comprobante disponible</p>
                        <a href="{{ route('purchases.descargar-comprobante', $purchase) }}" 
                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                            Descargar comprobante
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Productos de la compra -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos de la Compra</h3>
            
            @if($purchase->products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Unitario
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtotal
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha Vencimiento
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchase->products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>
                                            <div class="font-medium">{{ $product->nombre }}</div>
                                            <div class="text-gray-500">
                                                @if($product->category)
                                                    {{ $product->category->nombre }}
                                                @endif
                                                @if($product->brand)
                                                    - {{ $product->brand->nombre }}
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->pivot->cantidad }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        S/ {{ number_format($product->pivot->precio_unitario, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        S/ {{ number_format($product->pivot->subtotal, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($product->pivot->fecha_vencimiento)
                                            {{ \Carbon\Carbon::parse($product->pivot->fecha_vencimiento)->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Sin fecha</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-medium text-gray-700">
                                    Total:
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    S/ {{ number_format($purchase->total, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay productos registrados en esta compra.</p>
            @endif
        </div>
    </div>
</x-app-layout>
