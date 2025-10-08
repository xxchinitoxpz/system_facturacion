<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar permiso para ver usuarios
        if (!auth()->user()->can('ver-usuarios')) {
            abort(403, 'No tienes permisos para ver usuarios.');
        }

        $search = $request->input('search');
        
        $users = User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('web.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar permiso para crear usuarios
        if (!auth()->user()->can('crear-usuarios')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }

        $roles = Role::all();
        return view('web.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar permiso para crear usuarios
        if (!auth()->user()->can('crear-usuarios')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Ya existe un usuario con este email.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'roles.array' => 'Los roles deben ser una lista.',
            'roles.*.exists' => 'Uno de los roles seleccionados no existe.'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->has('roles') && !empty($request->roles)) {
                // Asegurar que roles sea un array
                $roleIds = is_array($request->roles) ? $request->roles : [$request->roles];
                
                // Obtener los roles por ID y asignarlos
                $roles = Role::whereIn('id', $roleIds)->get();
                $user->syncRoles($roles);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuario creado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Verificar permiso para ver usuarios
        if (!auth()->user()->can('ver-usuarios')) {
            abort(403, 'No tienes permisos para ver usuarios.');
        }

        $user = User::with('roles')->findOrFail($id);
        return view('web.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Verificar permiso para editar usuarios
        if (!auth()->user()->can('editar-usuarios')) {
            abort(403, 'No tienes permisos para editar usuarios.');
        }

        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('web.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Verificar permiso para editar usuarios
        if (!auth()->user()->can('editar-usuarios')) {
            abort(403, 'No tienes permisos para editar usuarios.');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Ya existe un usuario con este email.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'roles.array' => 'Los roles deben ser una lista.',
            'roles.*.exists' => 'Uno de los roles seleccionados no existe.'
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            if ($request->has('roles') && !empty($request->roles)) {
                // Asegurar que roles sea un array
                $roleIds = is_array($request->roles) ? $request->roles : [$request->roles];
                
                // Obtener los roles por ID y asignarlos
                $roles = Role::whereIn('id', $roleIds)->get();
                $user->syncRoles($roles);
            } else {
                $user->syncRoles([]);
            }

            return redirect()->route('users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Verificar permiso para eliminar usuarios
        if (!auth()->user()->can('eliminar-usuarios')) {
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }

        try {
            $user = User::findOrFail($id);
            
            // No permitir eliminar el usuario actual
            if ($user->id === auth()->id()) {
                return back()->with('error', 'No puedes eliminar tu propia cuenta.');
            }

            $user->delete();
            
            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
} 