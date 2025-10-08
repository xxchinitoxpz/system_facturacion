@php
use Illuminate\Support\Facades\Auth;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        
        <!-- jQuery (requerido para Select2) -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Estilos personalizados para Select2 -->
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                min-height: 42px;
                padding: 0.5rem 0.75rem;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single {
                background-color: #fff;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
                color: #374151;
                line-height: 1.5;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
                color: #9ca3af;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
                height: 40px;
            }
            
            .select2-container--bootstrap-5 .select2-dropdown {
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
            
            .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                padding: 0.5rem 0.75rem;
            }
            
            .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
                background-color: #4f46e5;
            }
            
            .select2-container--bootstrap-5.select2-container--focus .select2-selection {
                border-color: #4f46e5;
                box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex" x-data="{ 
            sidebarOpen: false, 
            adminOpen: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }},
            inventoryOpen: {{ request()->routeIs('inventory.*') || request()->routeIs('products.*') || request()->routeIs('combos.*') || request()->routeIs('defective-products.*') || request()->routeIs('brands.*') || request()->routeIs('categories.*') ? 'true' : 'false' }}
        }">
            <!-- Overlay para móvil -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>
            
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-800 to-indigo-900 text-white flex flex-col shadow-lg transform transition-transform duration-300 ease-in-out" 
                   :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                <!-- Header del sidebar -->
                <div class="flex-shrink-0 p-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-extrabold text-2xl tracking-tight">POS-360</span>
                    </div>
                </div>
                
                <!-- Navegación con scroll -->
                <nav class="flex-1 overflow-y-auto px-2">
                        <ul class="space-y-2">
                            <!-- Dashboard -->
                            <li>
                                <a href="{{ route('dashboard') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>

                            <!-- Empresas -->
                            @can('ver-empresas')
                            <li>
                                <a href="{{ route('companies.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('companies.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-6.75 4.5h.75m-1.5-1.5h.75M21 21V9.75M21 21h-3.375c-.621 0-1.125-.504-1.125-1.125V21M21 21h-3.375c-.621 0-1.125-.504-1.125-1.125V9.75" />
                                    </svg>
                                    Empresa
                                </a>
                            </li>
                            @endcan

                            <!-- Sucursales -->
                            @can('ver-sucursales')
                            <li>
                                <a href="{{ route('branches.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('branches.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                    </svg>
                                    Sucursales/Series
                                </a>
                            </li>
                            @endcan

                            <!-- Almacenes -->
                            @can('ver-almacenes')
                            <li>
                                <a href="{{ route('warehouses.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('warehouses.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Almacenes
                                </a>
                            </li>
                            @endcan

                            <!-- Empleados -->
                            @can('ver-empleados')
                            <li>
                                <a href="{{ route('employees.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('employees.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3 3 0 1 1-6.75 0 3 3 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Empleados
                                </a>
                            </li>
                            @endcan

                            <!-- Clientes -->
                            @can('ver-clientes')
                            <li>
                                <a href="{{ route('clients.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('clients.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Clientes
                                </a>
                            </li>
                            @endcan

                            <!-- Proveedores -->
                            @can('ver-proveedores')
                            <li>
                                <a href="{{ route('suppliers.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('suppliers.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Proveedores
                                </a>
                            </li>
                            @endcan

                            <!-- Cajas -->
                            @can('ver-cajas')
                            <li>
                                <a href="{{ route('cash-boxes.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('cash-boxes.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Cajas
                                </a>
                            </li>
                            @endcan

                            <!-- Ventas -->
                            @can('ver-ventas')
                            <li>
                                <a href="{{ route('sales.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('sales.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    Ventas
                                </a>
                            </li>
                            @endcan

                            {{-- Venta 2 - Comentado temporalmente
                            @can('ver-ventas')
                            <li>
                                <a href="{{ route('sales2.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('sales2.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                    </svg>
                                    Venta 2
                                </a>
                            </li>
                            @endcan
                            --}}


                            <!-- Compras -->
                            {{-- @can('ver-compras')
                            <li>
                                <a href="{{ route('purchases.index') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('purchases.*') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                    </svg>
                                    Compras
                                </a>
                            </li>
                            @endcan --}}



                            <!-- Inventario y Productos (Desplegable) -->
                            <li>
                                <button @click="inventoryOpen = !inventoryOpen" 
                                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-indigo-700 transition font-medium {{ request()->routeIs('inventory.*') || request()->routeIs('products.*') || request()->routeIs('combos.*') || request()->routeIs('defective-products.*') || request()->routeIs('brands.*') || request()->routeIs('categories.*') ? 'bg-indigo-700 shadow text-white' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                        </svg>
                                        Control de inventario
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': inventoryOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="inventoryOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 mt-2 space-y-1">
                                    @can('ver-inventario')
                                        <a href="{{ route('inventory.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('inventory.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                            </svg>
                                            Inventario
                                        </a>
                                    @endcan
                                    @can('ver-productos')
                                        <a href="{{ route('products.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.824.699 2.523 0l4.318-4.318c.699-.699.699-1.824 0-2.523L12.659 3.659A2.25 2.25 0 0011.068 3H9.568zM12 6.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                            </svg>
                                            Productos
                                        </a>
                                    @endcan
                                    @can('ver-combos')
                                        <a href="{{ route('combos.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('combos.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            Combos
                                        </a>
                                    @endcan
                                    @can('ver-productos-defectuosos')
                                        <a href="{{ route('defective-products.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('defective-products.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            Productos Defectuosos
                                        </a>
                                    @endcan
                                    @can('ver-marcas')
                                        <a href="{{ route('brands.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('brands.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            Marcas
                                        </a>
                                    @endcan
                                    @can('ver-categorias')
                                        <a href="{{ route('categories.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            Categorías
                                        </a>
                                    @endcan
                                </div>
                            </li>

                            <!-- Seguridad -->
                            <li>
                                <button @click="adminOpen = !adminOpen" 
                                        class="flex items-center justify-between w-full px-4 py-3 rounded-lg hover:bg-indigo-700 transition font-medium {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'bg-indigo-700 shadow text-white' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                        Seguridad
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': adminOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="adminOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="ml-4 mt-2 space-y-1">
                                    @can('ver-usuarios')
                                        <a href="{{ route('users.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3 3 0 1 1-6.75 0 3 3 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                            Usuarios
                                        </a>
                                    @endcan
                                    @can('ver-usuarios')
                                        <a href="{{ route('roles.index') }}" 
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg transition font-medium text-sm {{ request()->routeIs('roles.*') ? 'bg-indigo-600 text-white' : 'hover:bg-indigo-700' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                            </svg>
                                            Roles y Permisos
                                        </a>
                                    @endcan
                                </div>
                            </li>

                            <!-- Perfil -->
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition font-medium {{ request()->routeIs('profile.edit') ? 'bg-indigo-700 shadow text-white' : 'hover:bg-indigo-700' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Perfil
                                </a>
                            </li>
                        </ul>
                    </nav>
                </nav>
                
                <!-- Footer del sidebar (fijo) -->
                <div class="flex-shrink-0 p-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition font-medium w-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                            </svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </aside>
            <!-- Contenido principal -->
            <div class="flex-1 bg-gray-100 min-h-screen overflow-y-auto">
                <!-- Navbar superior -->
                <nav class="bg-gradient-to-r from-indigo-800 to-indigo-900 text-white shadow-lg">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <!-- Lado izquierdo -->
                        <div class="flex items-center gap-4">
                            <!-- Botón de menú -->
                            <button @click="sidebarOpen = !sidebarOpen" class="flex items-center justify-center w-10 h-10 bg-indigo-700 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </button>
                            
                            <!-- Logo y nombre del sistema -->
                            <div class="flex items-center gap-2">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-extrabold text-xl tracking-tight">POS-360</span>
                            </div>
                        </div>

                        <!-- Lado derecho -->
                        <div class="flex items-center gap-4">
                            <!-- Notificaciones -->
                            <button class="p-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>

                            <!-- Perfil del usuario -->
                            <div class="flex items-center gap-3">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-indigo-200">{{ Auth::user()->email }}</div>
                                </div>
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Contenido con padding -->
                <div class="p-8">


                @isset($header)
                    <header class="mb-6">
                        <div class="text-2xl font-bold text-gray-800">{{ $header }}</div>
                    </header>
                @endisset
                <main>
                    {{ $slot }}
                </main>
                </div>
            </div>
        </div>
        
        <!-- Script para inicializar Select2 -->
        <script>
            $(document).ready(function() {
                // Inicializar Select2 en todos los elementos con clase 'select2'
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Seleccionar...',
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "No se encontraron resultados";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
            });
        </script>
    </body>
</html>
