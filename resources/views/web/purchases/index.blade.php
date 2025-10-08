<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Compras</h1>
            @can('crear-compras')
                <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Crear Compra
                </a>
            @endcan
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

        <!-- Barra de búsqueda y filtros -->
        <div class="mb-6">
            <form method="GET" action="{{ route('purchases.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Campo de búsqueda -->
                    <div class="lg:col-span-2">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Buscar por observaciones o proveedor..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Select de proveedor -->
                    <div>
                        <select name="proveedor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los proveedores</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $proveedor_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Select de sucursal -->
                    <div>
                        @if(auth()->user()->sucursal_id === null)
                            <select name="sucursal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas las sucursales</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $sucursal_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="text" 
                                value="{{ auth()->user()->branch->nombre ?? 'Sucursal no asignada' }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly
                                placeholder="Sucursal asignada"
                            >
                            <input type="hidden" name="sucursal_id" value="{{ auth()->user()->sucursal_id }}">
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Fecha inicio -->
                    <div>
                        <input 
                            type="date" 
                            name="fecha_inicio" 
                            value="{{ $fecha_inicio }}" 
                            placeholder="Fecha inicio"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                    
                    <!-- Fecha fin -->
                    <div>
                        <input 
                            type="date" 
                            name="fecha_fin" 
                            value="{{ $fecha_fin }}" 
                            placeholder="Fecha fin"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>
                
                <!-- Botones de filtro -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Filtrar
                    </button>
                    <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de compras -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Proveedor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sucursal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Comprobante
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $purchase->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->fecha_compra->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->supplier->nombre_completo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->branch->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                S/ {{ number_format($purchase->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $purchase->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($purchase->comprobante_path)
                                    <a href="{{ route('purchases.descargar-comprobante', $purchase) }}" 
                                       class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors text-xs inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Descargar
                                    </a>
                                @else
                                    <span class="text-gray-400">Sin comprobante</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    @can('ver-compras')
                                        <a href="{{ route('purchases.show', $purchase) }}" 
                                           class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                           title="Ver detalles">
                                            Ver
                                        </a>
                                    @endcan
                                    
                                    @can('editar-compras')
                                        <a href="{{ route('purchases.edit', $purchase) }}" 
                                           class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                                           title="Editar compra">
                                            Editar
                                        </a>
                                    @endcan
                                    
                                    @can('eliminar-compras')
                                        <form action="{{ route('purchases.destroy', $purchase) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta compra?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                                    title="Eliminar compra">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron compras
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $purchases->links() }}
        </div>
    </div>
</x-app-layout>
