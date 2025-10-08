# 🛡️ Guía del Sistema de Permisos

## 📋 Resumen
El sistema de permisos está configurado para ser flexible y fácil de mantener. Puedes agregar nuevos permisos sin afectar los existentes.

## 🔧 Comandos Disponibles

### 1. Ejecutar el Seeder Principal
```bash
php artisan db:seed --class=PermissionSeeder
```
- ✅ Crea todos los permisos básicos del sistema
- ✅ Solo registra permisos nuevos (evita duplicados)
- ✅ Crea roles y usuario administrador
- ✅ Muestra estadísticas del proceso

### 2. Agregar Permisos Nuevos
```bash
# Agregar un permiso y asignarlo al rol Administrador (por defecto)
php artisan permission:add ver-productos

# Agregar un permiso y asignarlo a un rol específico
php artisan permission:add crear-productos --role=Usuario

# Agregar múltiples permisos
php artisan permission:add editar-productos
php artisan permission:add eliminar-productos
```

### 3. Verificar Permisos
```bash
# Ver todos los permisos y roles
php artisan tinker --execute="Spatie\Permission\Models\Permission::all()->pluck('name')->each(function(\$p) { echo \$p . PHP_EOL; });"
```

## 📝 Permisos Actuales del Sistema

### Usuarios
- `ver-usuarios`
- `crear-usuarios`
- `editar-usuarios`
- `eliminar-usuarios`

### Empresas
- `ver-empresas`
- `crear-empresas`
- `editar-empresas`
- `eliminar-empresas`

### Roles
- `ver-roles`
- `crear-roles`
- `editar-roles`
- `eliminar-roles`
- `asignar-roles`

### Dashboard
- `ver-dashboard`

## 👥 Roles del Sistema

### Administrador
- ✅ Tiene **todos** los permisos del sistema
- ✅ Se actualiza automáticamente cuando agregas nuevos permisos

### Usuario
- ✅ `ver-dashboard`
- ✅ `ver-empresas`
- ⚠️ Permisos limitados para operaciones básicas

## 🔄 Cómo Agregar Nuevos Permisos

### Opción 1: Usando el Comando (Recomendado)
```bash
# Para módulo de productos
php artisan permission:add ver-productos
php artisan permission:add crear-productos
php artisan permission:add editar-productos
php artisan permission:add eliminar-productos

# Para módulo de clientes
php artisan permission:add ver-clientes
php artisan permission:add crear-clientes
php artisan permission:add editar-clientes
php artisan permission:add eliminar-clientes

# Para módulo de facturas
php artisan permission:add ver-facturas
php artisan permission:add crear-facturas
php artisan permission:add editar-facturas
php artisan permission:add eliminar-facturas
```

### Opción 2: Modificando el Seeder
1. Abre `database/seeders/PermissionSeeder.php`
2. Agrega los nuevos permisos en el array `$permissions`
3. Ejecuta: `php artisan db:seed --class=PermissionSeeder`

## 🚀 Usuario Administrador por Defecto
- **Email:** admin@admin.com
- **Password:** password
- **Rol:** Administrador (todos los permisos)

## 💡 Consejos de Uso

### 1. Convención de Nombres
Usa el formato: `{accion}-{recurso}`
- `ver-productos`
- `crear-clientes`
- `editar-facturas`
- `eliminar-empresas`

### 2. Agregar Permisos en Controladores
```php
// En tus controladores
if (!auth()->user()->can('ver-productos')) {
    abort(403, 'No tienes permisos para ver productos.');
}
```

### 3. Verificar en Vistas Blade
```php
@can('crear-productos')
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        Crear Producto
    </a>
@endcan
```

### 4. Middleware de Permisos
```php
// En routes/web.php
Route::middleware(['auth', 'can:ver-productos'])->group(function () {
    Route::resource('products', ProductController::class);
});
```

## 🔧 Mantenimiento

### Limpiar Cache de Permisos
```bash
php artisan cache:clear
php artisan config:clear
```

### Verificar Estado del Sistema
```bash
# Ver total de permisos
php artisan tinker --execute="echo 'Permisos: ' . Spatie\Permission\Models\Permission::count();"

# Ver total de roles
php artisan tinker --execute="echo 'Roles: ' . Spatie\Permission\Models\Role::count();"
```

## ⚠️ Notas Importantes

1. **Siempre ejecuta el seeder después de agregar nuevos permisos** para asegurar que se asignen correctamente
2. **El rol Administrador recibe automáticamente todos los permisos nuevos**
3. **Los permisos son case-sensitive** - usa siempre minúsculas y guiones
4. **No elimines permisos existentes** sin verificar que no estén en uso

## 🆘 Solución de Problemas

### Error: "Permission already exists"
- El permiso ya existe, no es necesario crearlo nuevamente

### Error: "Role not found"
- Verifica que el rol existe antes de asignar permisos

### Permisos no funcionan en la aplicación
- Limpia el cache: `php artisan cache:clear`
- Verifica que el usuario tenga el rol correcto asignado 