<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir todos los permisos del sistema
        $permissions = [
            // Permisos de usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            
            // Permisos de empresas
            'ver-empresas',
            'crear-empresas',
            'editar-empresas',
            'eliminar-empresas',
            
            // Permisos de roles
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',
            'asignar-roles',
            
            // Permisos de sucursales
            'ver-sucursales',
            'crear-sucursales',
            'editar-sucursales',
            'eliminar-sucursales',
            
            // Permisos de series de comprobantes
            'ver-series-comprobantes',
            'crear-series-comprobantes',
            'editar-series-comprobantes',
            'eliminar-series-comprobantes',
            
            // Permisos de empleados
            'ver-empleados',
            'crear-empleados',
            'editar-empleados',
            'eliminar-empleados',
            
            // Permisos de categorías
            'ver-categorias',
            'crear-categorias',
            'editar-categorias',
            'eliminar-categorias',
            
            // Permisos de marcas
            'ver-marcas',
            'crear-marcas',
            'editar-marcas',
            'eliminar-marcas',
            
            // Permisos de almacenes
            'ver-almacenes',
            'crear-almacenes',
            'editar-almacenes',
            'eliminar-almacenes',
            
            // Permisos de productos
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'eliminar-productos',
            
            // Permisos de presentaciones
            'ver-presentaciones',
            'crear-presentaciones',
            'editar-presentaciones',
            'eliminar-presentaciones',
            
            // Permisos de inventario
            'ver-inventario',
            'crear-inventario',
            'editar-inventario',
            'eliminar-inventario',
            'ajustar-stock',
            'ver-movimientos-inventario',
            
            // Permisos de combos
            'ver-combos',
            'crear-combos',
            'editar-combos',
            'eliminar-combos',
            
            // Permisos de productos defectuosos
            'ver-productos-defectuosos',
            'crear-productos-defectuosos',
            'editar-productos-defectuosos',
            'eliminar-productos-defectuosos',
            
            // Permisos de clientes
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'eliminar-clientes',
            
            // Permisos de proveedores
            'ver-proveedores',
            'crear-proveedores',
            'editar-proveedores',
            'eliminar-proveedores',
            
            // Permisos de cajas
            'ver-cajas',
            'crear-cajas',
            'editar-cajas',
            'eliminar-cajas',
            
            // Permisos de sesiones de caja
            'ver-sesiones-caja',
            'crear-sesiones-caja',
            'editar-sesiones-caja',
            'eliminar-sesiones-caja',
            'abrir-caja',
            'cerrar-caja',
            'ver-cuadre-caja',
            
            // Permisos de movimientos de caja
            'ver-movimientos-caja',
            'crear-movimientos-caja',
            'editar-movimientos-caja',
            'eliminar-movimientos-caja',
            'registrar-ingreso-caja',
            'registrar-salida-caja',
            
            // Permisos de ventas
            'ver-ventas',
            'crear-ventas',
            'editar-ventas',
            'eliminar-ventas',
            'anular-ventas',
            'ver-detalle-ventas',
            'generar-comprobante-venta',
            
            // Permisos de pagos de ventas
            'ver-pagos-ventas',
            'crear-pagos-ventas',
            'editar-pagos-ventas',
            'eliminar-pagos-ventas',
            'ver-detalle-pagos-ventas',
            'registrar-pago-venta',
            'anular-pago-venta',
            
            // Permisos de compras
            'ver-compras',
            'crear-compras',
            'editar-compras',
            'eliminar-compras',
            'anular-compras',
            'ver-detalle-compras',
            'subir-comprobante-compra',
            
            // Permisos del dashboard
            'ver-dashboard',
            
            
            // ===== AQUÍ PUEDES AGREGAR NUEVOS PERMISOS =====
            // Ejemplo de nuevos permisos que podrías agregar:
            // 'ver-facturas',
            // 'crear-facturas',
            // 'editar-facturas',
            // 'eliminar-facturas',
            // ===============================================
        ];

        // Crear solo los permisos que no existen
        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $permissionModel = Permission::firstOrCreate(['name' => $permission]);
            if ($permissionModel->wasRecentlyCreated) {
                $createdPermissions[] = $permission;
            }
        }

        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $userRole = Role::firstOrCreate(['name' => 'Usuario']);
        $cajeroRole = Role::firstOrCreate(['name' => 'Cajero']);
        $almacenRole = Role::firstOrCreate(['name' => 'Almacén']);

        // Asignar todos los permisos al rol de administrador
        $adminRole->syncPermissions(Permission::all());

        // Asignar permisos básicos al rol de usuario
        $userRole->syncPermissions([
            'ver-dashboard',
            'ver-empresas',
        ]);

        // Asignar permisos al rol de cajero
        $cajeroRole->syncPermissions([
            'ver-dashboard',
            'ver-empresas',
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'ver-productos',
            'ver-cajas',
            'ver-sesiones-caja',
            'abrir-caja',
            'cerrar-caja',
            'ver-cuadre-caja',
            'ver-movimientos-caja',
            'crear-movimientos-caja',
            'registrar-ingreso-caja',
            'registrar-salida-caja',
            'ver-ventas',
            'crear-ventas',
            'editar-ventas',
            'ver-detalle-ventas',
            'generar-comprobante-venta',
            'ver-pagos-ventas',
            'crear-pagos-ventas',
            'registrar-pago-venta',
            'ver-inventario',
            'ver-movimientos-inventario',
        ]);

        // Asignar permisos al rol de almacén
        $almacenRole->syncPermissions([
            'ver-dashboard',
            'ver-empresas',
            'ver-categorias',
            'ver-marcas',
            'ver-almacenes',
            'ver-productos',
            'ver-presentaciones',
            'ver-inventario',
            'crear-inventario',
            'editar-inventario',
            'ajustar-stock',
            'ver-movimientos-inventario',
            'ver-combos',
            'crear-combos',
            'editar-combos',
            'ver-productos-defectuosos',
            'crear-productos-defectuosos',
            'editar-productos-defectuosos',
            'ver-proveedores',
            'crear-proveedores',
            'editar-proveedores',
            'ver-compras',
            'crear-compras',
            'editar-compras',
            'ver-detalle-compras',
            'subir-comprobante-compra',
        ]);

        // Crear usuario administrador por defecto si no existe
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol de administrador al usuario por defecto
        $adminUser->assignRole($adminRole);

        // Crear usuario cajero de prueba
        $cajeroUser = User::firstOrCreate(
            ['email' => 'cajero@test.com'],
            [
                'name' => 'Cajero Test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $cajeroUser->assignRole($cajeroRole);

        // Crear usuario almacén de prueba
        $almacenUser = User::firstOrCreate(
            ['email' => 'almacen@test.com'],
            [
                'name' => 'Almacén Test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $almacenUser->assignRole($almacenRole);

        // Mostrar información del proceso
        $this->command->info('✅ Seeder de permisos ejecutado exitosamente!');
        
        if (!empty($createdPermissions)) {
            $this->command->info('🆕 Permisos nuevos creados:');
            foreach ($createdPermissions as $permission) {
                $this->command->line("   - {$permission}");
            }
        } else {
            $this->command->info('ℹ️  No se crearon permisos nuevos (todos ya existían)');
        }

        $this->command->info('👤 Usuarios de prueba creados:');
        $this->command->info('   🔧 Administrador:');
        $this->command->info('      Email: admin@admin.com');
        $this->command->info('      Password: password');
        $this->command->info('      Rol: Administrador (todos los permisos)');
        $this->command->info('');
        $this->command->info('   💰 Cajero:');
        $this->command->info('      Email: cajero@test.com');
        $this->command->info('      Password: password');
        $this->command->info('      Rol: Cajero (ventas, cajas, clientes)');
        $this->command->info('');
        $this->command->info('   📦 Almacén:');
        $this->command->info('      Email: almacen@test.com');
        $this->command->info('      Password: password');
        $this->command->info('      Rol: Almacén (inventario, compras, productos)');
        
        $this->command->info('📊 Estadísticas:');
        $this->command->info('   Total permisos: ' . Permission::count());
        $this->command->info('   Total roles: ' . Role::count());
        $this->command->info('   Total usuarios: ' . User::count());
    }
}