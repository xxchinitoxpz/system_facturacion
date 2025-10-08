<x-app-layout>
    <div class="max-w-full mx-auto bg-white rounded-xl shadow p-4 mt-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-indigo-800">Crear Rol</h1>
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
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

        <form id="createRoleForm" action="{{ route('roles.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Información del Rol -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Información del Rol</h3>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Rol <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                        placeholder="Ej: Editor, Supervisor, etc."
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permisos -->
            <div class="bg-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">Permisos</h3>
                
                <!-- Dashboard -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Dashboard</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-dashboard" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Dashboard</span>
                        </label>
                    </div>
                </div>

                <!-- Usuarios -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Usuarios</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-usuarios" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Usuarios</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-usuarios" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Usuarios</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-usuarios" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Usuarios</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-usuarios" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Usuarios</span>
                        </label>
                    </div>
                </div>

                <!-- Empresas -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Empresas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-empresas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Empresas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-empresas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Empresas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-empresas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Empresas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-empresas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Empresas</span>
                        </label>
                    </div>
                </div>

                <!-- Sucursales -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Sucursales</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-sucursales" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Sucursales</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-sucursales" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Sucursales</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-sucursales" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Sucursales</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-sucursales" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Sucursales</span>
                        </label>
                    </div>
                </div>

                <!-- Series de Comprobantes -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Series de Comprobantes</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-series-comprobantes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Series</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-series-comprobantes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Series</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-series-comprobantes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Series</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-series-comprobantes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Series</span>
                        </label>
                    </div>
                </div>

                <!-- Empleados -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Empleados</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-empleados" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Empleados</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-empleados" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Empleados</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-empleados" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Empleados</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-empleados" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Empleados</span>
                        </label>
                    </div>
                </div>

                <!-- Categorías -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Categorías</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-categorias" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Categorías</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-categorias" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Categorías</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-categorias" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Categorías</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-categorias" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Categorías</span>
                        </label>
                    </div>
                </div>

                <!-- Marcas -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Marcas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-marcas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Marcas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-marcas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Marcas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-marcas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Marcas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-marcas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Marcas</span>
                        </label>
                    </div>
                </div>

                <!-- Almacenes -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Almacenes</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-almacenes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Almacenes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-almacenes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Almacenes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-almacenes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Almacenes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-almacenes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Almacenes</span>
                        </label>
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Productos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-productos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Productos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-productos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Productos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-productos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Productos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-productos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Productos</span>
                        </label>
                    </div>
                </div>

                <!-- Presentaciones -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Presentaciones</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-presentaciones" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Presentaciones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-presentaciones" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Presentaciones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-presentaciones" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Presentaciones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-presentaciones" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Presentaciones</span>
                        </label>
                    </div>
                </div>

                <!-- Inventario -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Inventario</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-inventario" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Inventario</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-inventario" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Inventario</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-inventario" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Inventario</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-inventario" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Inventario</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ajustar-stock" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ajustar Stock</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-movimientos-inventario" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Movimientos</span>
                        </label>
                    </div>
                </div>

                <!-- Combos -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Combos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-combos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Combos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-combos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Combos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-combos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Combos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-combos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Combos</span>
                        </label>
                    </div>
                </div>

                <!-- Productos Defectuosos -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Productos Defectuosos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-productos-defectuosos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Productos Defectuosos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-productos-defectuosos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Productos Defectuosos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-productos-defectuosos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Productos Defectuosos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-productos-defectuosos" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Productos Defectuosos</span>
                        </label>
                    </div>
                </div>

                <!-- Clientes -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Clientes</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-clientes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Clientes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-clientes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Clientes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-clientes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Clientes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-clientes" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Clientes</span>
                        </label>
                    </div>
                </div>

                <!-- Proveedores -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Proveedores</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-proveedores" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Proveedores</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-proveedores" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Proveedores</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-proveedores" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Proveedores</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-proveedores" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Proveedores</span>
                        </label>
                    </div>
                </div>

                <!-- Cajas -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Cajas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-cajas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Cajas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-cajas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Cajas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-cajas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Cajas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-cajas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Cajas</span>
                        </label>
                    </div>
                </div>

                <!-- Sesiones de Caja -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Sesiones de Caja</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-sesiones-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Sesiones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-sesiones-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Sesiones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-sesiones-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Sesiones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-sesiones-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Sesiones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="abrir-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Abrir Caja</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="cerrar-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Cerrar Caja</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-cuadre-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Cuadre de Caja</span>
                        </label>
                    </div>
                </div>

                <!-- Movimientos de Caja -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Movimientos de Caja</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-movimientos-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Movimientos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-movimientos-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Movimientos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-movimientos-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Movimientos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-movimientos-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Movimientos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="registrar-ingreso-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Registrar Ingresos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="registrar-salida-caja" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Registrar Salidas</span>
                        </label>
                    </div>
                </div>

                <!-- Ventas -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Ventas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="anular-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Anular Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-detalle-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Detalle de Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="generar-comprobante-venta" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Generar Comprobante</span>
                        </label>
                    </div>
                </div>


                <!-- Pagos de Ventas -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Pagos de Ventas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-pagos-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Pagos de Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-pagos-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Pagos de Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-pagos-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Pagos de Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-pagos-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Pagos de Ventas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-detalle-pagos-ventas" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Detalle de Pagos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="registrar-pago-venta" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Registrar Pago</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="anular-pago-venta" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Anular Pago</span>
                        </label>
                    </div>
                </div>

                <!-- Compras -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Compras</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="anular-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Anular Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-detalle-compras" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Detalle de Compras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="subir-comprobante-compra" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Subir Comprobante</span>
                        </label>
                    </div>
                </div>

                <!-- Roles -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-3 border-b border-indigo-200 pb-2">Roles</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="ver-roles" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Ver Roles</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="crear-roles" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Crear Roles</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="editar-roles" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Editar Roles</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="eliminar-roles" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Eliminar Roles</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="asignar-roles" 
                                   class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-700">Asignar Roles</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-6 border-t border-gray-200 justify-end">
                <a href="{{ route('roles.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <span id="submitText">Crear Rol</span>
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
        document.getElementById('createRoleForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Deshabilitar el botón y mostrar spinner
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Creando...';
            submitSpinner.classList.remove('hidden');
            
            // El formulario se enviará normalmente
        });
    </script>
</x-app-layout>