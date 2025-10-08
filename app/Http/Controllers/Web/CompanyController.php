<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('ver-empresas');

        $search = $request->input('search');
        
        $companies = Company::query()
            ->with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('razon_social', 'like', "%{$search}%")
                      ->orWhere('ruc', 'like', "%{$search}%")
                      ->orWhere('direccion', 'like', "%{$search}%");
                });
            })
            ->orderBy('razon_social')
            ->paginate(15);

        return view('web.companies.index', compact('companies', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-empresas');
        $this->checkSingleCompanyLimit();

        $users = User::orderBy('name')->get();
        return view('web.companies.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-empresas');
        $this->checkSingleCompanyLimit();

        $data = $this->validateCompanyData($request);
        $data = $this->handleFileUploads($request, $data);

        try {
            Company::create($data);
            return redirect()->route('companies.index')
                ->with('success', 'Empresa creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la empresa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->checkPermission('ver-empresas');

        $company = Company::with('user')->findOrFail($id);
        return view('web.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('editar-empresas');

        $company = Company::findOrFail($id);
        $users = User::orderBy('name')->get();

        return view('web.companies.edit', compact('company', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('editar-empresas');

        $company = Company::findOrFail($id);
        $data = $this->validateCompanyData($request, $company);
        $data = $this->handleFileUploads($request, $data, $company);

        try {
            $company->update($data);
            return redirect()->route('companies.index')
                ->with('success', 'Empresa actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la empresa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('eliminar-empresas');

        $company = Company::findOrFail($id);
        $this->checkDeleteRestriction();

        try {
            $this->deleteCompanyFiles($company);
            $company->delete();
            
            return redirect()->route('companies.index')
                ->with('success', 'Empresa eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la empresa: ' . $e->getMessage());
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
     * Verificar límite de empresa única
     */
    private function checkSingleCompanyLimit(): void
    {
        if (Company::count() > 0) {
            abort(403, 'Ya existe una empresa registrada. Solo se permite una empresa por sistema.');
        }
    }

    /**
     * Verificar restricción de eliminación
     */
    private function checkDeleteRestriction(): void
    {
        if (Company::count() <= 1) {
            abort(403, 'No se puede eliminar la empresa. Debe existir al menos una empresa en el sistema.');
        }
    }

    /**
     * Validar datos de la empresa
     */
    private function validateCompanyData(Request $request, ?Company $company = null): array
    {
        $rules = [
            'razon_social' => 'required|string|max:255',
            'ruc' => [
                'required',
                'string',
                'size:11',
                $company ? Rule::unique('companies', 'ruc')->ignore($company->id) : 'unique:companies,ruc'
            ],
            'direccion' => 'required|string|max:500',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'nullable|exists:users,id',
            'sol_user' => 'nullable|string|max:255',
            'sol_pass' => 'nullable|string|max:255',
            'cert_path' => 'nullable|file|mimes:pem,txt',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'boolean'
        ];

        $messages = [
            'razon_social.required' => 'La razón social es obligatoria.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.size' => 'El RUC debe tener 11 dígitos.',
            'ruc.unique' => 'Ya existe una empresa con este RUC.',
            'direccion.required' => 'La dirección es obligatoria.',
            'logo_path.image' => 'El archivo debe ser una imagen.',
            'logo_path.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'logo_path.max' => 'La imagen no debe superar los 2MB.',
            'cert_path.mimes' => 'El certificado debe ser de tipo: pem.',
            'cert_path.max' => 'El certificado no debe superar los 2MB.',
            'user_id.exists' => 'El usuario seleccionado no existe.'
        ];

        $data = $request->validate($rules, $messages);
        $data['production'] = $request->has('production');

        // Solo actualizar contraseñas si se proporcionan
        if (empty($data['sol_pass'])) {
            unset($data['sol_pass']);
        }
        if (empty($data['client_secret'])) {
            unset($data['client_secret']);
        }

        return $data;
    }

    /**
     * Manejar subida de archivos
     */
    private function handleFileUploads(Request $request, array $data, ?Company $company = null): array
    {
        // Manejar subida de logo
        if ($request->hasFile('logo_path')) {
            if ($company && $company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo_path')->store('companies/logos', 'public');
        }

        // Manejar subida de certificado
        if ($request->hasFile('cert_path')) {
            if ($company && $company->cert_path) {
                Storage::delete($company->cert_path);
            }
            $data['cert_path'] = $request->file('cert_path')->store('certs');
        }

        return $data;
    }

    /**
     * Eliminar archivos de la empresa
     */
    private function deleteCompanyFiles(Company $company): void
    {
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }
        if ($company->cert_path) {
            Storage::delete($company->cert_path);
        }
    }
}
