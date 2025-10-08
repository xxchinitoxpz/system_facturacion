<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PeruApiService;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PeruApiController extends Controller
{
    private $peruApiService;

    public function __construct(PeruApiService $peruApiService)
    {
        $this->peruApiService = $peruApiService;
    }

    /**
     * Consulta datos de una persona por DNI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function consultarDNI(Request $request): JsonResponse
    {
        $request->validate([
            'dni' => 'required|string|size:8'
        ]);

        $dni = $request->input('dni');
        
        // Validar formato
        if (!$this->peruApiService->validarFormatoDNI($dni)) {
            return response()->json([
                'success' => false,
                'message' => 'El formato del DNI no es válido. Debe tener 8 dígitos.',
                'data' => null
            ], 400);
        }

        // Consultar API
        $datos = $this->peruApiService->consultarDNI($dni);
        
        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos para el DNI proporcionado.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $datos
        ]);
    }

    /**
     * Consulta datos de una empresa por RUC
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function consultarRUC(Request $request): JsonResponse
    {
        $request->validate([
            'ruc' => 'required|string|size:11'
        ]);

        $ruc = $request->input('ruc');
        
        // Validar formato
        if (!$this->peruApiService->validarFormatoRUC($ruc)) {
            return response()->json([
                'success' => false,
                'message' => 'El formato del RUC no es válido. Debe tener 11 dígitos.',
                'data' => null
            ], 400);
        }

        // Consultar API
        $datos = $this->peruApiService->consultarRUC($ruc);
        
        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos para el RUC proporcionado.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $datos
        ]);
    }

    /**
     * Obtiene información formateada de una persona por DNI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerInformacionPersona(Request $request): JsonResponse
    {
        $request->validate([
            'dni' => 'required|string|size:8'
        ]);

        $dni = $request->input('dni');
        $datos = $this->peruApiService->obtenerInformacionPersona($dni);
        
        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener la información de la persona.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Información obtenida correctamente',
            'data' => $datos
        ]);
    }

    /**
     * Obtiene información formateada de una empresa por RUC
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerInformacionEmpresa(Request $request): JsonResponse
    {
        $request->validate([
            'ruc' => 'required|string|size:11'
        ]);

        $ruc = $request->input('ruc');
        $datos = $this->peruApiService->obtenerInformacionEmpresa($ruc);
        
        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener la información de la empresa.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Información obtenida correctamente',
            'data' => $datos
        ]);
    }

    /**
     * Valida el formato de un DNI
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validarDNI(Request $request): JsonResponse
    {
        $request->validate([
            'dni' => 'required|string'
        ]);

        $dni = $request->input('dni');
        $esValido = $this->peruApiService->validarFormatoDNI($dni);

        return response()->json([
            'success' => true,
            'data' => [
                'dni' => $dni,
                'es_valido' => $esValido,
                'mensaje' => $esValido ? 'Formato válido' : 'El DNI debe tener 8 dígitos'
            ]
        ]);
    }

    /**
     * Valida el formato de un RUC
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validarRUC(Request $request): JsonResponse
    {
        $request->validate([
            'ruc' => 'required|string'
        ]);

        $ruc = $request->input('ruc');
        $esValido = $this->peruApiService->validarFormatoRUC($ruc);

        return response()->json([
            'success' => true,
            'data' => [
                'ruc' => $ruc,
                'es_valido' => $esValido,
                'mensaje' => $esValido ? 'Formato válido' : 'El RUC debe tener 11 dígitos'
            ]
        ]);
    }

    /**
     * Consulta datos de una persona o empresa por documento (DNI o RUC)
     *
     * @param string $documento
     * @return JsonResponse
     */
    public function consultarDocumento(string $documento): JsonResponse
    {
        // Determinar si es DNI (8 dígitos) o RUC (11 dígitos)
        if (strlen($documento) === 8) {
            // Es DNI
            if (!$this->peruApiService->validarFormatoDNI($documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El formato del DNI no es válido.',
                    'data' => null
                ], 400);
            }

            $datos = $this->peruApiService->obtenerInformacionPersona($documento);
            $tipo = 'DNI';
        } elseif (strlen($documento) === 11) {
            // Es RUC
            if (!$this->peruApiService->validarFormatoRUC($documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El formato del RUC no es válido.',
                    'data' => null
                ], 400);
            }

            $datos = $this->peruApiService->obtenerInformacionEmpresa($documento);
            $tipo = 'RUC';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC).',
                'data' => null
            ], 400);
        }

        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => "No se encontraron datos para el {$tipo} proporcionado.",
                'data' => null
            ], 404);
        }

        // Verificar si el cliente ya existe en la base de datos
        $clienteExistente = Client::where('nro_documento', $documento)->first();
        
        if ($clienteExistente) {
            // El cliente ya existe en BD
            $datos['cliente_id'] = $clienteExistente->id;
            $datos['cliente_existente'] = true;
            $datos['cliente_creado'] = false;
        } else {
            // Cliente no existe, devolver datos para creación temporal
            $datos['cliente_id'] = null;
            $datos['cliente_existente'] = false;
            $datos['cliente_creado'] = false;
            $datos['datos_cliente'] = $this->prepararDatosCliente($datos, $tipo, $documento);
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $datos,
            'tipo' => $tipo
        ]);
    }

    /**
     * Prepara los datos del cliente para su creación
     *
     * @param array $datos
     * @param string $tipo
     * @param string $documento
     * @return array
     */
    private function prepararDatosCliente(array $datos, string $tipo, string $documento): array
    {
        if ($tipo === 'DNI') {
            // Datos de persona
            $nombreCompleto = $datos['nombre_completo'] ?? 
                trim($datos['nombres'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno']);
            
            return [
                'nombre_completo' => $nombreCompleto,
                'tipo_documento' => 'DNI',
                'nro_documento' => $documento,
                'telefono' => null,
                'email' => null,
                'direccion' => $datos['direccion'] ?? null,
                'activo' => true
            ];
        } else {
            // Datos de empresa
            return [
                'nombre_completo' => $datos['nombre_o_razon_social'] ?? 'Empresa',
                'tipo_documento' => 'RUC',
                'nro_documento' => $documento,
                'telefono' => null,
                'email' => null,
                'direccion' => $datos['direccion'] ?? $datos['direccion_completa'] ?? null,
                'activo' => true
            ];
        }
    }

    /**
     * Consulta datos de un proveedor por documento (DNI o RUC)
     *
     * @param string $documento
     * @return JsonResponse
     */
    public function consultarProveedor(string $documento): JsonResponse
    {
        // Determinar si es DNI (8 dígitos) o RUC (11 dígitos)
        if (strlen($documento) === 8) {
            // Es DNI
            if (!$this->peruApiService->validarFormatoDNI($documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El formato del DNI no es válido.',
                    'data' => null
                ], 400);
            }

            $datos = $this->peruApiService->obtenerInformacionPersona($documento);
            $tipo = 'DNI';
        } elseif (strlen($documento) === 11) {
            // Es RUC
            if (!$this->peruApiService->validarFormatoRUC($documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El formato del RUC no es válido.',
                    'data' => null
                ], 400);
            }

            $datos = $this->peruApiService->obtenerInformacionEmpresa($documento);
            $tipo = 'RUC';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El documento debe tener 8 dígitos (DNI) o 11 dígitos (RUC).',
                'data' => null
            ], 400);
        }

        if (!$datos) {
            return response()->json([
                'success' => false,
                'message' => "No se encontraron datos para el {$tipo} proporcionado.",
                'data' => null
            ], 404);
        }

        // Verificar si el proveedor ya existe en la base de datos
        $proveedorExistente = Supplier::where('nro_documento', $documento)->first();
        
        if (!$proveedorExistente) {
            // Crear el proveedor automáticamente
            try {
                DB::beginTransaction();
                
                $proveedorData = $this->prepararDatosProveedor($datos, $tipo, $documento);
                $proveedorNuevo = Supplier::create($proveedorData);
                
                DB::commit();
                
                // Agregar el ID del proveedor creado a la respuesta
                $datos['proveedor_id'] = $proveedorNuevo->id;
                $datos['proveedor_creado'] = true;
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el proveedor automáticamente: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }
        } else {
            // El proveedor ya existe
            $datos['proveedor_id'] = $proveedorExistente->id;
            $datos['proveedor_creado'] = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'data' => $datos,
            'tipo' => $tipo
        ]);
    }

    /**
     * Prepara los datos del proveedor para su creación
     *
     * @param array $datos
     * @param string $tipo
     * @param string $documento
     * @return array
     */
    private function prepararDatosProveedor(array $datos, string $tipo, string $documento): array
    {
        if ($tipo === 'DNI') {
            // Datos de persona
            $nombreCompleto = $datos['nombre_completo'] ?? 
                trim($datos['nombres'] . ' ' . $datos['apellido_paterno'] . ' ' . $datos['apellido_materno']);
            
            return [
                'nombre_completo' => $nombreCompleto,
                'tipo_documento' => 'DNI',
                'nro_documento' => $documento,
                'telefono' => null,
                'email' => null,
                'direccion' => $datos['direccion'] ?? null,
                'activo' => true
            ];
        } else {
            // Datos de empresa
            return [
                'nombre_completo' => $datos['nombre_o_razon_social'] ?? 'Empresa',
                'tipo_documento' => 'RUC',
                'nro_documento' => $documento,
                'telefono' => null,
                'email' => null,
                'direccion' => $datos['direccion'] ?? $datos['direccion_completa'] ?? null,
                'activo' => true
            ];
        }
    }
} 