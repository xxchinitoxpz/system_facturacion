<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-indigo-800">Sistema de Cajas</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Cajas creadas: {{ $branchesWithCashBox }} de {{ $totalBranches }} sucursales
                    @if($availableBranches > 0)
                        <span class="text-green-600">({{ $availableBranches }} sucursal{{ $availableBranches > 1 ? 'es' : '' }} disponible{{ $availableBranches > 1 ? 's' : '' }})</span>
                    @else
                        <span class="text-red-600">(Todas las sucursales tienen caja)</span>
                    @endif
                </p>
            </div>
            @can('crear-cajas')
                @if($canCreate)
                    <a href="{{ route('cash-boxes.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12" />
                        </svg>
                        Nueva Caja
                    </a>
                @else
                    <div class="px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12" />
                        </svg>
                        Límite alcanzado
                    </div>
                @endif
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($cashBoxes as $cashBox)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Header de la caja -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $cashBox->nombre }}</h3>
                                    <p class="text-sm text-gray-500">{{ $cashBox->branch->nombre }}</p>
                                </div>
                            </div>
                            @if($cashBox->activeSession)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Abierta
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                    Cerrada
                                </span>
                            @endif
                        </div>

                        <!-- Descripción -->
                        @if($cashBox->descripcion)
                            <p class="text-sm text-gray-600 mb-4">{{ $cashBox->descripcion }}</p>
                        @endif

                        <!-- Estado de sesión -->
                        @if($cashBox->activeSession)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-green-800">Sesión Activa</p>
                                    <p class="text-xs text-green-600">
                                        Abierta por: {{ $cashBox->activeSession->user->name }}
                                    </p>
                                    <p class="text-xs text-green-600">
                                        {{ $cashBox->activeSession->fecha_hora_apertura->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                
                                <!-- Saldos por método de pago -->
                                @if(isset($cashBox->activeSession->totalesPorMetodo) && $cashBox->activeSession->totalesPorMetodo->count() > 0)
                                    <div class="border-t border-green-200 pt-3">
                                        <p class="text-xs font-medium text-green-800 mb-2">Saldos por método de pago:</p>
                                        <div class="space-y-1">
                                            @foreach(['efectivo', 'tarjeta', 'transferencia', 'billetera_virtual'] as $metodo)
                                                @if($cashBox->activeSession->totalesPorMetodo->has($metodo))
                                                    @php
                                                        $ingresos = $cashBox->activeSession->totalesPorMetodo[$metodo]->where('tipo', 'ingreso')->first()->total ?? 0;
                                                        $salidas = $cashBox->activeSession->totalesPorMetodo[$metodo]->where('tipo', 'salida')->first()->total ?? 0;
                                                        $neto = $ingresos - $salidas;
                                                    @endphp
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-gray-700">
                                                            @switch($metodo)
                                                                @case('efectivo')
                                                                    💰 Efectivo
                                                                    @break
                                                                @case('tarjeta')
                                                                    💳 Tarjeta
                                                                    @break
                                                                @case('transferencia')
                                                                    🏦 Transferencia
                                                                    @break
                                                                @case('billetera_virtual')
                                                                    📱 Billetera Virtual
                                                                    @break
                                                            @endswitch
                                                        </span>
                                                        <span class="font-semibold {{ $neto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                            S/ {{ number_format($neto, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Saldo total y Total en Caja -->
                                <div class="border-t border-green-200 pt-3 mt-3">
                                    <div class="grid grid-cols-2 gap-2">
                                        <!-- Total General -->
                                        <div class="text-center">
                                            <span class="block text-xs font-medium text-green-800">Total General:</span>
                                            <span class="text-sm font-bold text-green-800">
                                                S/ {{ number_format($cashBox->activeSession->saldo_actual, 2) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Total en Caja -->
                                        <div class="text-center">
                                            <span class="block text-xs font-medium text-indigo-800">Total en Caja:</span>
                                            <span class="text-sm font-bold text-indigo-800">
                                                S/ {{ number_format($cashBox->activeSession->montoEnCaja, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Acciones -->
                        <div class="flex flex-wrap gap-2">
                            @can('ver-sesiones-caja')
                            <a href="{{ route('cash-boxes.sessions', $cashBox) }}" 
                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                               title="Ver sesiones">
                                Sesiones
                            </a>
                            @endcan

                            @if($cashBox->activeSession)
                                @can('cerrar-caja')
                                <a href="{{ route('cash-boxes.close-session', $cashBox->activeSession) }}" 
                                   class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                   title="Cerrar sesión">
                                    Cerrar
                                </a>
                                @endcan
                                @can('crear-movimientos-caja')
                                <a href="{{ route('cash-boxes.create-movement', $cashBox->activeSession) }}" 
                                   class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                   title="Nuevo movimiento">
                                    Movimiento
                                </a>
                                @endcan
                            @else
                                @can('abrir-caja')
                                <a href="{{ route('cash-boxes.open-session', $cashBox) }}" 
                                   class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-xs"
                                   title="Abrir sesión">
                                    Abrir
                                </a>
                                @endcan
                            @endif

                            @can('editar-cajas')
                            <a href="{{ route('cash-boxes.edit', $cashBox) }}" 
                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-xs"
                               title="Editar caja">
                                Editar
                            </a>
                            @endcan

                            @can('eliminar-cajas')
                                @if($cashBox->sessions->count() == 0)
                                    <button onclick="confirmDelete(this)" 
                                            data-id="{{ $cashBox->id }}" 
                                            data-name="{{ $cashBox->nombre }}"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-xs"
                                            title="Eliminar caja">
                                        Eliminar
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay cajas</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera caja.</p>
                        @can('crear-cajas')
                        <div class="mt-6">
                            <a href="{{ route('cash-boxes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Nueva Caja
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar eliminación</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            ¿Estás seguro de que quieres eliminar la caja "<span id="cashBoxName" class="font-semibold"></span>"?
                            <br>
                            <span class="text-red-600">Esta acción no se puede deshacer.</span>
                        </p>
                        <div class="flex gap-3 justify-center">
                            <button
                                id="confirmDelete"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2"
                            >
                                <span id="confirmText">Sí, eliminar</span>
                                <div id="confirmSpinner" class="hidden">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                            <button
                                id="cancelDelete"
                                onclick="closeDeleteModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para eliminar -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            document.getElementById('cashBoxName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');

            document.getElementById('confirmDelete').onclick = function() {
                const confirmBtn = document.getElementById('confirmDelete');
                const confirmText = document.getElementById('confirmText');
                const confirmSpinner = document.getElementById('confirmSpinner');
                const cancelBtn = document.getElementById('cancelDelete');

                // Deshabilitar botones y mostrar spinner
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                cancelBtn.disabled = true;
                cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                confirmText.textContent = 'Eliminando...';
                confirmSpinner.classList.remove('hidden');

                // Enviar formulario
                const form = document.getElementById('deleteForm');
                form.action = `/cash-boxes/${id}`;
                form.submit();
            };
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');

            // Resetear estado del botón
            const confirmBtn = document.getElementById('confirmDelete');
            const confirmText = document.getElementById('confirmText');
            const confirmSpinner = document.getElementById('confirmSpinner');
            const cancelBtn = document.getElementById('cancelDelete');

            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            confirmText.textContent = 'Sí, eliminar';
            confirmSpinner.classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>
