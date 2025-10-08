<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verificar permiso para ver usuarios (gestión de roles)
        if (!auth()->user()->can('ver-usuarios')) {
            abort(403, 'No tienes permisos para ver roles y permisos.');
        }

        $roles = Role::with('permissions')->get();
        return view('web.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar permiso para crear usuarios (gestión de roles)
        if (!auth()->user()->can('crear-usuarios')) {
            abort(403, 'No tienes permisos para crear roles.');
        }

        $permissions = Permission::all();
        return view('web.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar permiso para crear usuarios (gestión de roles)
        if (!auth()->user()->can('crear-usuarios')) {
            abort(403, 'No tienes permisos para crear roles.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Verificar permiso para ver usuarios (gestión de roles)
        if (!auth()->user()->can('ver-usuarios')) {
            abort(403, 'No tienes permisos para ver roles y permisos.');
        }

        $role = Role::with('permissions')->findOrFail($id);
        $users = User::role($role->name)->get();
        return view('web.roles.show', compact('role', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Verificar permiso para editar usuarios (gestión de roles)
        if (!auth()->user()->can('editar-usuarios')) {
            abort(403, 'No tienes permisos para editar roles.');
        }

        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        return view('web.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verificar permiso para editar usuarios (gestión de roles)
        if (!auth()->user()->can('editar-usuarios')) {
            abort(403, 'No tienes permisos para editar roles.');
        }

        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'array',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Verificar permiso para eliminar usuarios (gestión de roles)
        if (!auth()->user()->can('eliminar-usuarios')) {
            abort(403, 'No tienes permisos para eliminar roles.');
        }

        $role = Role::findOrFail($id);

        // Verificar si hay usuarios con este rol
        $usersWithRole = User::role($role->name)->count();
        
        if ($usersWithRole > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el rol porque hay usuarios asignados a él.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(Request $request)
    {
        // Verificar permiso para asignar roles
        if (!auth()->user()->can('asignar-roles')) {
            abort(403, 'No tienes permisos para asignar roles.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return redirect()->back()
            ->with('success', 'Rol asignado exitosamente.');
    }

    /**
     * Remover rol de usuario
     */
    public function removeRole(Request $request)
    {
        // Verificar permiso para asignar roles
        if (!auth()->user()->can('asignar-roles')) {
            abort(403, 'No tienes permisos para remover roles.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->removeRole($request->role);

        return redirect()->back()
            ->with('success', 'Rol removido exitosamente.');
    }
}
