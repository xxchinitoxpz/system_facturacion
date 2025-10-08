<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Branch;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('ver-almacenes');

        $search = $request->get('search', '');
        
        $warehouses = Warehouse::with('branch')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%")
                      ->orWhereHas('branch', function ($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = $this->getWarehouseStats();
        return view('web.warehouses.index', compact('warehouses', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-almacenes');

        $availableBranches = $this->getAvailableBranches();
        
        if ($availableBranches->isEmpty()) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Todas las sucursales ya tienen un almacén asignado. Solo se permite un almacén por sucursal.');
        }

        return view('web.warehouses.create', compact('availableBranches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-almacenes');

        $data = $this->validateWarehouseData($request);
        $this->checkWarehouseUnique($request->sucursal_id);

        try {
            Warehouse::create($data);
            return redirect()->route('warehouses.index')
                ->with('success', 'Almacén creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el almacén: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        $this->checkPermission('ver-almacenes');

        $warehouse->load('branch');
        return view('web.warehouses.show', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $this->checkPermission('editar-almacenes');

        $branches = Branch::orderBy('nombre')->get();
        return view('web.warehouses.edit', compact('warehouse', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $this->checkPermission('editar-almacenes');

        $data = $this->validateWarehouseData($request);
        
        // Verificar que la nueva sucursal no tenga ya un almacén (si se está cambiando)
        if ($request->sucursal_id != $warehouse->sucursal_id) {
            $this->checkWarehouseUnique($request->sucursal_id);
        }

        try {
            $warehouse->update($data);
            return redirect()->route('warehouses.index')
                ->with('success', 'Almacén actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el almacén: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $this->checkPermission('eliminar-almacenes');

        try {
            // Verificar si el almacén tiene productos asociados (cuando se implemente)
            // if ($warehouse->products()->count() > 0) {
            //     return redirect()->back()->with('error', 'No se puede eliminar el almacén porque tiene productos asociados.');
            // }
            
            $warehouse->delete();
            return redirect()->back()->with('success', 'Almacén eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el almacén: ' . $e->getMessage());
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
     * Validar datos del almacén
     */
    private function validateWarehouseData(Request $request): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'sucursal_id' => 'required|exists:branches,id',
        ];

        $messages = [
            'nombre.required' => 'El nombre del almacén es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Verificar que la sucursal no tenga ya un almacén
     */
    private function checkWarehouseUnique(int $sucursalId): void
    {
        $existingWarehouse = Warehouse::where('sucursal_id', $sucursalId)->first();
        if ($existingWarehouse) {
            abort(403, 'Esta sucursal ya tiene un almacén asignado. Solo se permite un almacén por sucursal.');
        }
    }

    /**
     * Obtener sucursales disponibles para crear almacén
     */
    private function getAvailableBranches()
    {
        $branchesWithWarehouse = Warehouse::pluck('sucursal_id')->toArray();
        
        return Branch::whereNotIn('id', $branchesWithWarehouse)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtener estadísticas de almacenes
     */
    private function getWarehouseStats(): array
    {
        $totalBranches = Branch::count();
        $branchesWithWarehouse = Warehouse::count();
        $availableBranches = $totalBranches - $branchesWithWarehouse;
        $canCreate = $availableBranches > 0;

        return [
            'totalBranches' => $totalBranches,
            'branchesWithWarehouse' => $branchesWithWarehouse,
            'availableBranches' => $availableBranches,
            'canCreate' => $canCreate
        ];
    }
}