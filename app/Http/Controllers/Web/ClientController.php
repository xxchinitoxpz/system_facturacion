<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private const TIPOS_DOCUMENTO = ['DNI', 'RUC', 'CE', 'PASAPORTE'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('ver-clientes');

        $search = request('search');
        $tipo_documento = request('tipo_documento');
        $activo = request('activo');
        
        $clients = Client::query()
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

        return view('web.clients.index', compact('clients', 'search', 'tipo_documento', 'activo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('crear-clientes');

        return view('web.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('crear-clientes');

        $data = $this->validateClientData($request);

        try {
            $client = Client::create($data);
            
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente creado exitosamente.',
                    'cliente' => $client
                ]);
            }
            
            // Si no es AJAX, redirigir como antes
            return redirect()->route('clients.index')
                ->with('success', 'Cliente creado exitosamente.');
        } catch (\Exception $e) {
            // Si es una petición AJAX, devolver JSON con error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el cliente: ' . $e->getMessage()
                ], 400);
            }
            
            // Si no es AJAX, redirigir como antes
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $this->checkPermission('ver-clientes');

        return view('web.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $this->checkPermission('editar-clientes');

        return view('web.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $this->checkPermission('editar-clientes');

        $data = $this->validateClientData($request, $client->id);

        try {
            $client->update($data);
            return redirect()->route('clients.index')
                ->with('success', 'Cliente actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $this->checkPermission('eliminar-clientes');

        try {
            $client->delete();
            return redirect()->route('clients.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
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
     * Validar datos del cliente
     */
    private function validateClientData(Request $request, ?int $clientId = null): array
    {
        $rules = [
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento' => 'required|in:' . implode(',', self::TIPOS_DOCUMENTO),
            'nro_documento' => 'required|string|max:20|unique:clients,nro_documento' . ($clientId ? ",{$clientId}" : ''),
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
            'nro_documento.unique' => 'Ya existe un cliente con ese número de documento.',
            'email.email' => 'El email debe tener un formato válido.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $validatedData['activo'] = $request->has('activo');

        return $validatedData;
    }
}
