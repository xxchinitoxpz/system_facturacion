@extends('layouts.app')

@section('title', 'Cuadre de Caja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Cuadre de Caja</h1>
        <div class="flex gap-2">
            <a href="{{ route('cash-boxes.session-details', $session) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                Volver
            </a>
        </div>
    </div>

    <!-- Información de la sesión -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información de la Sesión</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600">Caja</p>
                <p class="font-medium">{{ $session->cashBox->nombre }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Sucursal</p>
                <p class="font-medium">{{ $session->branch->nombre }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Usuario</p>
                <p class="font-medium">{{ $session->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Estado</p>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $session->estado === 'abierta' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($session->estado) }}
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div>
                <p class="text-sm text-gray-600">Fecha Apertura</p>
                <p class="font-medium">{{ $session->fecha_hora_apertura->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Monto Apertura</p>
                <p class="font-medium">S/ {{ number_format($session->monto_apertura, 2) }}</p>
            </div>
            @if($session->estado === 'cerrada')
            <div>
                <p class="text-sm text-gray-600">Fecha Cierre</p>
                <p class="font-medium">{{ $session->fecha_hora_cierre->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Monto Cierre</p>
                <p class="font-medium">S/ {{ number_format($session->monto_cierre, 2) }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Resumen por método de pago -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen por Método de Pago</h2>
        
        @if($totalsByMethod->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método de Pago
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ingresos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salidas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Neto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($totalsByMethod as $metodo => $movements)
                            @php
                                $ingresos = $movements->where('tipo', 'ingreso')->sum('total');
                                $salidas = $movements->where('tipo', 'salida')->sum('total');
                                $neto = $ingresos - $salidas;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $metodo)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                    S/ {{ number_format($ingresos, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                    S/ {{ number_format($salidas, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $neto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    S/ {{ number_format($neto, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No hay movimientos registrados en esta sesión.</p>
        @endif
    </div>

    <!-- Movimientos detallados -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Movimientos Detallados</h2>
        
        @if($session->movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha/Hora
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método de Pago
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Descripción
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($session->movements->sortByDesc('created_at') as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $movement->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($movement->tipo) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->metodo_pago ? ucfirst(str_replace('_', ' ', $movement->metodo_pago)) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                    S/ {{ number_format($movement->monto, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $movement->descripcion }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No hay movimientos registrados en esta sesión.</p>
        @endif
    </div>
</div>
@endsection
