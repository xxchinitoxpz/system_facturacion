<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private const TIPOS_DOCUMENTO = ['DNI', 'RUC', 'CE', 'PASAPORTE'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('ver-proveedores');

        $search = request('search');
        $tipo_documento = request('tipo_documento');
        $activo = request('activo');
        
        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_completo', 'like', "%{$search}%")
                      ->orWhere('nro_documento', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($tipo_documento, function ($query, $tipo_documento) {
                $query->where('tipo_documento', $tipo_documento);
            })
            ->when($activo !== null, function ($query) use ($activo) {
                $query->where('activo', $activo);
            })
            ->orderBy('nombre_completo')
            ->paginate(10);

        return view('web.suppliers.index', compact('suppliers', 'search', 'tipo_documento', 'activo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-proveedores');

        return view('web.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-proveedores');

        $data = $this->validateSupplierData($request);

        try {
            Supplier::create($data);
            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->checkPermission('ver-proveedores');

        return view('web.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $this->checkPermission('editar-proveedores');

        return view('web.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->checkPermission('editar-proveedores');

        $data = $this->validateSupplierData($request, $supplier->id);

        try {
            $supplier->update($data);
            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->checkPermission('eliminar-proveedores');

        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
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
     * Validar datos del proveedor
     */
    private function validateSupplierData(Request $request, ?int $supplierId = null): array
    {
        $rules = [
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:' . implode(',', self::TIPOS_DOCUMENTO),
            'nro_documento' => 'required|string|max:20|unique:suppliers,nro_documento' . ($supplierId ? ",{$supplierId}" : ''),
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ];

        $messages = [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI, RUC, CE o PASAPORTE.',
            'nro_documento.required' => 'El número de documento es obligatorio.',
            'nro_documento.unique' => 'Ya existe un proveedor con ese número de documento.',
            'email.email' => 'El email debe tener un formato válido.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $validatedData['activo'] = $request->has('activo');

        return $validatedData;
    }
}
