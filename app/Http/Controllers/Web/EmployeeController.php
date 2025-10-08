<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('ver-empleados');

        $search = $request->input('search');
        
        $employees = Employee::query()
            ->with(['branch.company'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_completo', 'like', "%{$search}%")
                      ->orWhere('nro_documento', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('cargo', 'like', "%{$search}%");
                });
            })
            ->orderBy('nombre_completo')
            ->paginate(15);

        return view('web.employees.index', compact('employees', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-empleados');

        $branches = Branch::with('company')->orderBy('nombre')->get();
        return view('web.employees.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-empleados');

        $data = $this->validateEmployeeData($request);
        $createUser = $request->boolean('crear_usuario');

        try {
            $employee = Employee::create($data);

            if ($createUser) {
                $this->createUserForEmployee($employee, $request);
                $message = 'Empleado y usuario creados exitosamente.';
            } else {
                $message = 'Empleado creado exitosamente.';
            }

            return redirect()->route('employees.index')->with('success', $message);
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el empleado: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->checkPermission('ver-empleados');

        $employee = Employee::with(['branch.company'])->findOrFail($id);
        return view('web.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('editar-empleados');

        $employee = Employee::findOrFail($id);
        $branches = Branch::with('company')->orderBy('nombre')->get();
        
        return view('web.employees.edit', compact('employee', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('editar-empleados');

        $employee = Employee::findOrFail($id);
        $data = $this->validateEmployeeData($request, $id);

        try {
            $employee->update($data);
            
            return redirect()->route('employees.index')
                ->with('success', 'Empleado actualizado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el empleado: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('eliminar-empleados');

        $employee = Employee::findOrFail($id);

        try {
            $employee->delete();
            
            return redirect()->route('employees.index')
                ->with('success', 'Empleado eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el empleado: ' . $e->getMessage());
        }
    }

    // ========== MÉTODOS PRIVADOS OPTIMIZADOS ==========

    /**
     * Verificar permisos del usuario
     */
    private function checkPermission(string $permission): void
    {
        if (!auth()->user()->can($permission)) {
            abort(403, "No tienes permisos para {$permission}.");
        }
    }

    /**
     * Validar datos del empleado
     */
    private function validateEmployeeData(Request $request, ?string $employeeId = null): array
    {
        $rules = [
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|string|max:50',
            'nro_documento' => 'required|string|max:50|unique:employees,nro_documento' . ($employeeId ? ",{$employeeId}" : ''),
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:employees,email' . ($employeeId ? ",{$employeeId}" : ''),
            'direccion' => 'required|string|max:500',
            'cargo' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'activo' => 'boolean',
            'sucursal_id' => 'required|exists:branches,id',
        ];

        // Validaciones condicionales para usuario (solo en store)
        if (!$employeeId && $request->boolean('crear_usuario')) {
            $rules['username'] = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'nro_documento.required' => 'El número de documento es obligatorio.',
            'nro_documento.unique' => 'Este número de documento ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'direccion.required' => 'La dirección es obligatoria.',
            'cargo.required' => 'El cargo es obligatorio.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'username.required' => 'El email de usuario es obligatorio cuando se crea un usuario.',
            'username.email' => 'El email de usuario debe tener un formato válido.',
            'username.unique' => 'Este email de usuario ya está registrado.',
            'password.required' => 'La contraseña es obligatoria cuando se crea un usuario.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Crear usuario para el empleado
     */
    private function createUserForEmployee(Employee $employee, Request $request): void
    {
        if (!$request->filled('username') || !$request->filled('password')) {
            return;
        }

        $user = User::create([
            'name' => $request->nombre_completo,
            'email' => $request->username,
            'password' => Hash::make($request->password),
            'sucursal_id' => $request->sucursal_id,
            'empleado_id' => $employee->id,
        ]);

        // Asignar rol por defecto si existe
        $defaultRole = \Spatie\Permission\Models\Role::where('name', 'Usuario')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }
    }
}
