<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'ver-comprobantes', 'guard_name' => 'web']);

        $adminRole = Role::where('name', 'Administrador')->first();
        if ($adminRole && !$adminRole->hasPermissionTo($permission)) {
            $adminRole->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'ver-comprobantes')->where('guard_name', 'web')->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
