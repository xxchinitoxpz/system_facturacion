<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PeruApiService
{
    private $token;
    private $baseUrl;

    public function __construct()
    {
        $this->token = 'a5582300981c75d61f0f304ac9c684bca67f712f7456ad4a9eb8a2a8bfc23bb5';
        $this->baseUrl = 'https://apiperu.dev/api';
    }

    /**
     * Consulta datos de una persona por DNI
     *
     * @param string $dni
     * @return array|null
     */
    public function consultarDNI(string $dni): ?array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->baseUrl . '/dni', [
                'dni' => $dni
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return $data['data'] ?? null;
                }
                
                Log::warning('API Perú DNI - Respuesta no exitosa', [
                    'dni' => $dni,
                    'response' => $data
                ]);
                
                return null;
            }

            Log::error('API Perú DNI - Error HTTP', [
                'dni' => $dni,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('API Perú DNI - Excepción', [
                'dni' => $dni,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Consulta datos de una empresa por RUC
     *
     * @param string $ruc
     * @return array|null
     */
    public function consultarRUC(string $ruc): ?array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->baseUrl . '/ruc', [
                'ruc' => $ruc
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return $data['data'] ?? null;
                }
                
                Log::warning('API Perú RUC - Respuesta no exitosa', [
                    'ruc' => $ruc,
                    'response' => $data
                ]);
                
                return null;
            }

            Log::error('API Perú RUC - Error HTTP', [
                'ruc' => $ruc,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('API Perú RUC - Excepción', [
                'ruc' => $ruc,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Valida si un DNI tiene formato válido
     *
     * @param string $dni
     * @return bool
     */
    public function validarFormatoDNI(string $dni): bool
    {
        return preg_match('/^\d{8}$/', $dni);
    }

    /**
     * Valida si un RUC tiene formato válido
     *
     * @param string $ruc
     * @return bool
     */
    public function validarFormatoRUC(string $ruc): bool
    {
        return preg_match('/^\d{11}$/', $ruc);
    }

    /**
     * Obtiene información formateada de una persona por DNI
     *
     * @param string $dni
     * @return array|null
     */
    public function obtenerInformacionPersona(string $dni): ?array
    {
        if (!$this->validarFormatoDNI($dni)) {
            return null;
        }

        $datos = $this->consultarDNI($dni);
        
        if (!$datos) {
            return null;
        }

        return [
            'nombre_completo' => $datos['nombre_completo'] ?? '',
            'apellido_paterno' => $datos['apellido_paterno'] ?? '',
            'apellido_materno' => $datos['apellido_materno'] ?? '',
            'nombres' => $datos['nombres'] ?? '',
            'direccion' => $datos['direccion'] ?? '',
            'departamento' => $datos['departamento'] ?? '',
            'provincia' => $datos['provincia'] ?? '',
            'distrito' => $datos['distrito'] ?? '',
            'ubigeo' => $datos['ubigeo'] ?? '',
            'estado' => $datos['estado'] ?? '',
        ];
    }

    /**
     * Obtiene información formateada de una empresa por RUC
     *
     * @param string $ruc
     * @return array|null
     */
    public function obtenerInformacionEmpresa(string $ruc): ?array
    {
        if (!$this->validarFormatoRUC($ruc)) {
            return null;
        }

        $datos = $this->consultarRUC($ruc);
        
        if (!$datos) {
            return null;
        }

        return [
            'nombre_o_razon_social' => $datos['nombre_o_razon_social'] ?? '',
            'direccion' => $datos['direccion'] ?? '',
            'direccion_completa' => $datos['direccion_completa'] ?? '',
            'departamento' => $datos['departamento'] ?? '',
            'provincia' => $datos['provincia'] ?? '',
            'distrito' => $datos['distrito'] ?? '',
            'ubigeo_sunat' => $datos['ubigeo_sunat'] ?? '',
            'estado' => $datos['estado'] ?? '',
            'condicion' => $datos['condicion'] ?? '',
            'es_agente_de_retencion' => $datos['es_agente_de_retencion'] ?? 'NO',
            'es_agente_de_percepcion' => $datos['es_agente_de_percepcion'] ?? 'NO',
            'es_buen_contribuyente' => $datos['es_buen_contribuyente'] ?? 'NO',
        ];
    }
} 