<x-app-layout>
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Importar Inventario desde Excel</h1>
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                Volver al Inventario
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Instrucciones -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Instrucciones para el archivo Excel:</h3>
            <div class="text-sm text-blue-700 space-y-1">
                <p><strong>Formato requerido:</strong> El archivo debe tener las siguientes columnas en el orden especificado:</p>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    <li><strong>Código de barras:</strong> El código de barras del producto (debe existir en la base de datos)</li>
                    <li><strong>Stock:</strong> Cantidad de unidades (número entero positivo)</li>
                    <li><strong>Fecha de vencimiento:</strong> Fecha en formato DD/MM/YYYY (opcional)</li>
                </ul>
                <p class="mt-2"><strong>Ejemplo:</strong></p>
                <div class="bg-white p-2 rounded border font-mono text-xs">
                    codigo_barras | stock | fecha_vencimiento<br>
                    7759109000758 | 10 | 20/09/2026<br>
                    1234567890123 | 5 | 15/12/2025
                </div>
                <p class="mt-2"><strong>Nota:</strong> La primera fila debe contener los nombres de las columnas.</p>
            </div>
        </div>

        <!-- Formulario de importación -->
        <form method="POST" action="{{ route('inventory.import') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Selector de almacén -->
            <div>
                <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Almacén de destino <span class="text-red-500">*</span>
                </label>
                <select 
                    name="warehouse_id" 
                    id="warehouse_id" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">Selecciona un almacén</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('warehouse_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selector de archivo -->
            <div>
                <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                    Archivo Excel <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="excel_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Haz clic para subir</span> o arrastra y suelta
                            </p>
                            <p class="text-xs text-gray-500">Excel (.xlsx, .xls) o CSV (Máx. 10MB)</p>
                        </div>
                        <input 
                            id="excel_file" 
                            name="excel_file" 
                            type="file" 
                            accept=".xlsx,.xls,.csv"
                            class="hidden" 
                            required
                        />
                    </label>
                </div>
                @error('excel_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información del archivo seleccionado -->
            <div id="file-info" class="hidden p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-green-700">
                        Archivo seleccionado: <span id="file-name" class="font-semibold"></span>
                        (<span id="file-size"></span>)
                    </span>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                    id="submit-btn"
                    disabled
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Importar Inventario
                </button>
                <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>

        <!-- Plantilla de ejemplo -->
        <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Descargar plantilla de ejemplo</h3>
            <p class="text-sm text-gray-600 mb-3">Descarga esta plantilla para ver el formato correcto del archivo Excel:</p>
            <a href="{{ route('inventory.import.template') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Descargar Plantilla
            </a>
        </div>
    </div>

    <script>
        // Mostrar información del archivo seleccionado
        document.getElementById('excel_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const submitBtn = document.getElementById('submit-btn');
            
            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                fileInfo.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const warehouseId = document.getElementById('warehouse_id').value;
            const file = document.getElementById('excel_file').files[0];
            
            if (!warehouseId) {
                e.preventDefault();
                alert('Por favor selecciona un almacén');
                return;
            }
            
            if (!file) {
                e.preventDefault();
                alert('Por favor selecciona un archivo Excel');
                return;
            }
        });
    </script>
</x-app-layout>
