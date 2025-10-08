<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add {permission} {--role=Administrador : Rol al que asignar el permiso}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega un nuevo permiso al sistema y lo asigna a un rol';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissionName = $this->argument('permission');
        $roleName = $this->option('role');

        // Verificar si el permiso ya existe
        if (Permission::where('name', $permissionName)->exists()) {
            $this->error("❌ El permiso '{$permissionName}' ya existe.");
            return Command::FAILURE;
        }

        // Crear el nuevo permiso
        $permission = Permission::create(['name' => $permissionName]);
        $this->info("✅ Permiso '{$permissionName}' creado exitosamente.");

        // Buscar el rol especificado
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->warn("⚠️  El rol '{$roleName}' no existe. El permiso se creó pero no se asignó a ningún rol.");
            return Command::SUCCESS;
        }

        // Asignar el permiso al rol
        $role->givePermissionTo($permission);
        $this->info("✅ Permiso '{$permissionName}' asignado al rol '{$roleName}'.");

        // Mostrar estadísticas actualizadas
        $this->info("📊 Total de permisos en el sistema: " . Permission::count());

        return Command::SUCCESS;
    }
}
