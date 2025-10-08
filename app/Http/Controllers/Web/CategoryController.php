<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('ver-categorias');

        $search = request('search');
        
        $categories = Category::withCount('products')
            ->when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('web.categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-categorias');

        return view('web.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-categorias');

        $data = $this->validateCategoryData($request);

        try {
            Category::create($data);
            return redirect()->route('categories.index')
                ->with('success', 'Categoría creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->checkPermission('ver-categorias');

        return view('web.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->checkPermission('editar-categorias');

        return view('web.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->checkPermission('editar-categorias');

        $data = $this->validateCategoryData($request, $category->id);

        try {
            $category->update($data);
            return redirect()->route('categories.index')
                ->with('success', 'Categoría actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->checkPermission('eliminar-categorias');

        try {
            // Verificar si la categoría tiene productos asociados
            if ($category->products()->count() > 0) {
                return redirect()->back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
            }

            $category->delete();
            return redirect()->route('categories.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
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
     * Validar datos de la categoría
     */
    private function validateCategoryData(Request $request, ?int $categoryId = null): array
    {
        $rules = [
            'nombre' => 'required|string|max:255|unique:categories,nombre' . ($categoryId ? ",{$categoryId}" : ''),
        ];

        $messages = [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ];

        return $request->validate($rules, $messages);
    }
}
