<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('ver-marcas');

        $search = request('search');
        
        $brands = Brand::withCount('products')
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('web.brands.index', compact('brands', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-marcas');

        return view('web.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-marcas');

        $data = $this->validateBrandData($request);

        try {
            Brand::create($data);
            return redirect()->route('brands.index')
                ->with('success', 'Marca creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la marca: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $this->checkPermission('ver-marcas');

        return view('web.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $this->checkPermission('editar-marcas');

        return view('web.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $this->checkPermission('editar-marcas');

        $data = $this->validateBrandData($request, $brand->id);

        try {
            $brand->update($data);
            return redirect()->route('brands.index')
                ->with('success', 'Marca actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la marca: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->checkPermission('eliminar-marcas');

        try {
            // Verificar si la marca tiene productos asociados
            if ($brand->products()->count() > 0) {
                return redirect()->back()->with('error', 'No se puede eliminar la marca porque tiene productos asociados.');
            }

            $brand->delete();
            return redirect()->route('brands.index')
                ->with('success', 'Marca eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la marca: ' . $e->getMessage());
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
     * Validar datos de la marca
     */
    private function validateBrandData(Request $request, ?int $brandId = null): array
    {
        $rules = [
            'nombre' => 'required|string|max:255|unique:brands,nombre' . ($brandId ? ",{$brandId}" : ''),
        ];

        $messages = [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe una marca con ese nombre.',
        ];

        return $request->validate($rules, $messages);
    }
}
