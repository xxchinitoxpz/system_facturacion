<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Editar Compra #{{ $purchase->id }}</h1>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="editPurchaseForm" action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Información de la compra -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información de la Compra</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Proveedor -->
                    <div>
                        <label for="proveedor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Proveedor <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="proveedor_id" 
                            name="proveedor_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('proveedor_id') border-red-500 @enderror"
                        >
                            <option value="">Seleccionar proveedor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('proveedor_id', $purchase->proveedor_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nombre_completo }} - {{ $supplier->tipo_documento }}: {{ $supplier->nro_documento }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sucursal -->
                    <div>
                        <label for="sucursal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sucursal <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->sucursal_id === null)
                            <select 
                                id="sucursal_id" 
                                name="sucursal_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sucursal_id') border-red-500 @enderror"
                            >
                                <option value="">Seleccionar sucursal</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('sucursal_id', $purchase->sucursal_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="text" 
                                value="{{ auth()->user()->branch->nombre ?? 'Sucursal no asignada' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly
                            >
                            <input type="hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id }}">
                        @endif
                        @error('sucursal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total -->
                    <div>
                        <label for="total" class="block text-sm font-medium text-gray-700 mb-2">
                            Total <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="total" 
                            name="total" 
                            value="{{ old('total', $purchase->total) }}"
                            min="0" 
                            step="0.01"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('total') border-red-500 @enderror"
                        >
                        @error('total')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea 
                            id="observaciones" 
                            name="observaciones" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('observaciones') border-red-500 @enderror"
                            placeholder="Observaciones adicionales sobre la compra..."
                        >{{ old('observaciones', $purchase->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comprobante -->
                    <div class="md:col-span-2">
                        <label for="comprobante_path" class="block text-sm font-medium text-gray-700 mb-2">
                            Comprobante (opcional)
                        </label>
                        @if($purchase->comprobante_path)
                            <div class="mb-2 p-2 bg-green-50 border border-green-200 rounded">
                                <p class="text-sm text-green-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Comprobante actual: 
                                    <a href="{{ route('purchases.descargar-comprobante', $purchase) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Descargar
                                    </a>
                                </p>
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="comprobante_path" 
                            name="comprobante_path"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('comprobante_path') border-red-500 @enderror"
                        >
                        <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, JPEG, PNG. Máximo 2MB. Dejar vacío para mantener el actual.</p>
                        @error('comprobante_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Productos de la compra (solo lectura) -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos de la Compra</h3>
                <p class="text-sm text-gray-600 mb-4">Los productos no se pueden editar una vez creada la compra.</p>
                
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

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('purchases.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Actualizar Compra</span>
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
        document.getElementById('editPurchaseForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Actualizando...';
            submitSpinner.classList.remove('hidden');
        });
    </script>
</x-app-layout>
