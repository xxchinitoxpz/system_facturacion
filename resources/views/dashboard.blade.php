<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="space-y-8">
        <!-- Selector de Sucursal -->
        @if(auth()->user()->sucursal_id === null)
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-100 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Seleccionar Sucursal</h2>
                            <p class="text-sm text-gray-600">Elige la sucursal para ver sus datos</p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-4">
                        <select 
                            name="sucursal_id" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm"
                        >
                            <option value="">Seleccionar sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ $sucursalSeleccionada == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-800 font-medium">
                            Mostrando datos de tu sucursal: <span class="font-semibold">{{ auth()->user()->branch->nombre ?? 'Sucursal asignada' }}</span>
                        </p>
                        <p class="text-blue-600 text-sm">Los datos se filtran automáticamente por tu sucursal asignada</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tarjetas de resumen principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Ventas de la sesión activa -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Ventas Sesión</p>
                        <p class="text-3xl font-bold">S/ {{ number_format($totalVentasSesion, 2) }}</p>
                        <p class="text-green-100 text-xs mt-1">{{ $cantidadVentasSesion }} ventas</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                </div>
            </div>


            <!-- Productos con bajo stock -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Bajo Stock</p>
                            <p class="text-3xl font-bold">{{ $cantidadProductosBajoStock }}</p>
                        <p class="text-orange-100 text-xs mt-1">{{ $cantidadProductosBajoStock }} productos</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Saldo de la sesión activa (general) -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Saldo Sesión General</p>
                        <p class="text-3xl font-bold">S/ {{ number_format($saldoSesionActiva, 2) }}</p>
                        <p class="text-purple-100 text-xs mt-1">
                            @if($sesionActiva)
                                Sesión activa
                            @else
                                Sin sesión activa
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Saldo de efectivo de la sesión activa -->
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium">Saldo Sesión Efectivo</p>
                        <p class="text-3xl font-bold">S/ {{ number_format($saldoEfectivoSesion, 2) }}</p>
                        <p class="text-indigo-100 text-xs mt-1">
                            @if($sesionActiva)
                                Efectivo en caja
                            @else
                                Sin sesión activa
                            @endif
                        </p>
                    </div>
                    <div class="bg-indigo-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de gráficos y estadísticas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Gráfico de ventas -->
            <div class="bg-white rounded-2xl shadow-lg p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Ventas de los Últimos 7 Días</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Ventas</span>
                    </div>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        <p class="text-lg font-medium">Gráfico de Ventas</p>
                        <p class="text-sm">Aquí se mostrará el gráfico de ventas</p>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="bg-white rounded-2xl shadow-lg p-6 w-full">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Acciones Rápidas</h3>
                <div class="space-y-4">
                    @can('crear-productos')
                        <a href="{{ route('products.create') }}" class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <div class="bg-indigo-500 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Crear Producto</p>
                                <p class="text-sm text-gray-600">Agregar nuevo producto</p>
                            </div>
                        </a>
                    @endcan
                    
                    @can('crear-combos')
                        <a href="{{ route('combos.create') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="bg-green-500 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Crear Combo</p>
                                <p class="text-sm text-gray-600">Agregar nueva promoción</p>
                            </div>
                        </a>
                    @endcan
                    
                    @can('crear-productos-defectuosos')
                        <a href="{{ route('defective-products.create') }}" class="flex items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                            <div class="bg-red-500 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Registrar Producto Defectuoso</p>
                                <p class="text-sm text-gray-600">Reportar producto con defectos</p>
                            </div>
                        </a>
                    @endcan
                    
                    @can('ver-inventario')
                        <a href="{{ route('inventory.index') }}" class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                            <div class="bg-orange-500 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Gestionar Inventario</p>
                                <p class="text-sm text-gray-600">Ver y ajustar stock</p>
                            </div>
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Sección de últimas actividades -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Últimas ventas -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Últimas Ventas</h3>
                    <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Ver todas</a>
                </div>
                <div class="space-y-4">
                    @forelse($ultimasVentas as $venta)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $venta->client ? $venta->client->nombre : 'Cliente no registrado' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600">S/ {{ number_format($venta->total, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $venta->branch->nombre ?? 'Sin sucursal' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">No hay ventas recientes</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Productos con bajo stock -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Productos con Bajo Stock</h3>
                    <a href="{{ route('inventory.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Ver todos</a>
                </div>
                <div class="space-y-4">
                    @forelse($productosBajoStock as $producto)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $producto->producto_nombre }}</p>
                                    <p class="text-sm text-gray-600">{{ $producto->almacen_nombre }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-orange-600">{{ $producto->stock }} unidades</p>
                                @if($producto->fecha_vencimiento)
                                    <p class="text-xs text-gray-500">Vence: {{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                            </svg>
                            <p class="text-gray-500">No hay productos con bajo stock</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
