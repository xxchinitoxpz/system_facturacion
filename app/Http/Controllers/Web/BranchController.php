<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\DocumentSeries;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    private const MAX_BRANCHES = 2;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('ver-sucursales');

        $search = $request->input('search');
        
        $branches = Branch::query()
            ->with(['company', 'documentSeries'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('direccion', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15);

        $totalBranches = Branch::count();
        $canCreate = $totalBranches < self::MAX_BRANCHES;

        return view('web.branches.index', compact('branches', 'search', 'totalBranches', 'canCreate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-sucursales');
        $this->checkBranchLimit();

        $companies = Company::orderBy('razon_social')->get();
        return view('web.branches.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-sucursales');
        $this->checkBranchLimit();

        $data = $this->validateBranchData($request);
        $seriesData = $this->extractSeriesData($request);

        try {
            $branch = Branch::create($data);
            $this->createDocumentSeries($branch, $seriesData);
            
            $message = $this->buildSuccessMessage($seriesData);
            return redirect()->route('branches.index')->with('success', $message);
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->checkPermission('ver-sucursales');

        $branch = Branch::with(['company', 'documentSeries'])->findOrFail($id);
        return view('web.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('editar-sucursales');

        $branch = Branch::with('documentSeries')->findOrFail($id);
        $companies = Company::orderBy('razon_social')->get();

        return view('web.branches.edit', compact('branch', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('editar-sucursales');

        $branch = Branch::findOrFail($id);
        $data = $this->validateBranchData($request);
        $seriesData = $this->extractSeriesData($request);

        try {
            $branch->update($data);
            $this->updateDocumentSeries($branch, $seriesData);
            
            $message = $this->buildSuccessMessage($seriesData, 'actualizada');
            return redirect()->route('branches.index')->with('success', $message);
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la sucursal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('eliminar-sucursales');

        $branch = Branch::findOrFail($id);

        try {
            $branch->delete();
            return redirect()->route('branches.index')
                ->with('success', 'Sucursal eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la sucursal: ' . $e->getMessage());
        }
    }

    // ========== MÉTODOS DE SERIES DE DOCUMENTOS ==========

    /**
     * Store a new document series for a branch.
     */
    public function storeSeries(Request $request, string $branchId)
    {
        $this->checkPermission('crear-series-comprobantes');

        $data = $this->validateSeriesData($request);

        try {
            DocumentSeries::create($data);
            return back()->with('success', 'Serie de comprobante creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la serie de comprobante: ' . $e->getMessage());
        }
    }

    /**
     * Update a document series.
     */
    public function updateSeries(Request $request, string $branchId, string $seriesId)
    {
        $this->checkPermission('editar-series-comprobantes');

        $series = DocumentSeries::findOrFail($seriesId);
        $data = $this->validateSeriesData($request, false);

        try {
            $series->update($data);
            return back()->with('success', 'Serie de comprobante actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la serie de comprobante: ' . $e->getMessage());
        }
    }

    /**
     * Remove a document series.
     */
    public function destroySeries(string $branchId, string $seriesId)
    {
        $this->checkPermission('eliminar-series-comprobantes');

        $series = DocumentSeries::findOrFail($seriesId);

        try {
            $series->delete();
            return back()->with('success', 'Serie de comprobante eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la serie de comprobante: ' . $e->getMessage());
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
     * Verificar límite de sucursales
     */
    private function checkBranchLimit(): void
    {
        if (Branch::count() >= self::MAX_BRANCHES) {
            abort(403, "Ya se ha alcanzado el límite máximo de " . self::MAX_BRANCHES . " sucursales permitidas.");
        }
    }

    /**
     * Validar datos de la sucursal
     */
    private function validateBranchData(Request $request): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'required|string|max:20',
            'empresa_id' => 'required|exists:companies,id',
            'series' => 'sometimes|array',
            'series.*.tipo_comprobante' => 'required_with:series|string|max:255',
            'series.*.serie' => 'required_with:series|string|max:20',
            'series.*.ultimo_correlativo' => 'required_with:series|integer|min:0'
        ];

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'empresa_id.required' => 'Debe seleccionar una empresa.',
            'empresa_id.exists' => 'La empresa seleccionada no existe.',
            'series.*.tipo_comprobante.required_with' => 'El tipo de comprobante es obligatorio.',
            'series.*.serie.required_with' => 'La serie es obligatoria.',
            'series.*.ultimo_correlativo.required_with' => 'El último correlativo es obligatorio.',
            'series.*.ultimo_correlativo.integer' => 'El último correlativo debe ser un número entero.',
            'series.*.ultimo_correlativo.min' => 'El último correlativo debe ser mayor o igual a 0.'
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Validar datos de series de documentos
     */
    private function validateSeriesData(Request $request, bool $includeSucursalId = true): array
    {
        $rules = [
            'tipo_comprobante' => 'required|string|max:255',
            'serie' => 'required|string|max:20',
            'ultimo_correlativo' => 'required|integer|min:0',
        ];

        if ($includeSucursalId) {
            $rules['sucursal_id'] = 'required|exists:branches,id';
        }

        $messages = [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'serie.required' => 'La serie es obligatoria.',
            'ultimo_correlativo.required' => 'El último correlativo es obligatorio.',
            'ultimo_correlativo.integer' => 'El último correlativo debe ser un número entero.',
            'ultimo_correlativo.min' => 'El último correlativo debe ser mayor o igual a 0.',
        ];

        if ($includeSucursalId) {
            $messages['sucursal_id.required'] = 'Debe seleccionar una sucursal.';
            $messages['sucursal_id.exists'] = 'La sucursal seleccionada no existe.';
        }

        return $request->validate($rules, $messages);
    }

    /**
     * Extraer datos de series del request
     */
    private function extractSeriesData(Request $request): array
    {
        if (!$request->has('series') || !is_array($request->series)) {
            return [];
        }

        return array_filter($request->series, function($series) {
            return !empty($series['tipo_comprobante']) && !empty($series['serie']);
        });
    }

    /**
     * Crear series de documentos
     */
    private function createDocumentSeries(Branch $branch, array $seriesData): void
    {
        foreach ($seriesData as $series) {
            DocumentSeries::create([
                'tipo_comprobante' => $series['tipo_comprobante'],
                'serie' => $series['serie'],
                'ultimo_correlativo' => $series['ultimo_correlativo'] ?? 0,
                'sucursal_id' => $branch->id
            ]);
        }
    }

    /**
     * Actualizar series de documentos
     */
    private function updateDocumentSeries(Branch $branch, array $seriesData): void
    {
        // Eliminar todas las series existentes
        $branch->documentSeries()->delete();
        
        // Crear las nuevas series
        $this->createDocumentSeries($branch, $seriesData);
    }

    /**
     * Construir mensaje de éxito
     */
    private function buildSuccessMessage(array $seriesData, string $action = 'creada'): string
    {
        $message = "Sucursal {$action} exitosamente.";
        
        if (!empty($seriesData)) {
            $seriesCount = count($seriesData);
            $seriesText = $seriesCount == 1 ? 'serie' : 'series';
            $actionText = $action === 'actualizada' ? 'actualizaron' : 'crearon';
            $message .= " Se {$actionText} {$seriesCount} {$seriesText} de comprobantes.";
        }
        
        return $message;
    }
}
