<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Detalles del Combo</h1>
            <div class="flex space-x-2">
                @can('editar-combos')
                    <a href="{{ route('combos.edit', $combo) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('combos.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
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

        <!-- Información del combo -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Combo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Combo</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $combo->nombre }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                    <p class="text-lg font-semibold text-green-600">S/ {{ number_format($combo->precio, 2) }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="mt-1">
                        @if($combo->estado)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                    <p class="text-gray-900">{{ $combo->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                    <p class="text-gray-900">{{ $combo->updated_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total de Productos</label>
                    <p class="text-lg font-semibold text-indigo-600">{{ $combo->products->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Productos del combo -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Productos del Combo</h2>
            
            @if($combo->products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-indigo-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($combo->products as $index => $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $product->nombre }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->barcode }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $product->category->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $product->brand->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <span class="font-semibold text-indigo-600">{{ $product->pivot->cantidad }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($product->presentations->count() > 0)
                                            <span class="text-green-600">S/ {{ number_format($product->presentations->first()->precio_venta, 2) }}</span>
                                        @else
                                            <span class="text-gray-400">Sin precio</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($product->presentations->count() > 0)
                                            <span class="font-semibold text-green-600">
                                                S/ {{ number_format($product->presentations->first()->precio_venta * $product->pivot->cantidad, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Precio del Combo:
                                </td>
                                <td class="px-4 py-3 text-lg font-bold text-green-600">
                                    S/ {{ number_format($combo->precio, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No hay productos en este combo</p>
                    <p class="text-sm text-gray-400">Este combo no tiene productos asignados.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
