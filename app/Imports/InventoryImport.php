<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class InventoryImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $warehouseId;
    protected $errors = [];
    protected $successCount = 0;

    public function __construct($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $this->processRow($row);
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2, // +2 porque el índice empieza en 0 y hay header
                    'error' => $e->getMessage(),
                    'data' => $row->toArray()
                ];
            }
        }
    }

    protected function processRow($row)
    {
        // Validar que existan las columnas necesarias
        if (!isset($row['codigo_barras']) || !isset($row['stock'])) {
            throw new \Exception('Faltan columnas requeridas: código de barras, stock');
        }

        // Convertir código de barras a string y limpiar
        $barcode = trim((string) $row['codigo_barras']);
        
        // Convertir stock a entero
        $stock = (int) $row['stock'];
        
        // Fecha de vencimiento es opcional
        $fechaVencimiento = isset($row['fecha_vencimiento']) ? $row['fecha_vencimiento'] : null;

        // Validar código de barras
        if (empty($barcode)) {
            throw new \Exception('El código de barras no puede estar vacío');
        }

        // Buscar el producto por código de barras
        $product = Product::where('barcode', $barcode)->first();
        if (!$product) {
            throw new \Exception("No se encontró un producto con el código de barras: {$barcode}");
        }

        // Validar stock
        if ($stock < 0) {
            throw new \Exception("El stock no puede ser negativo para el producto: {$barcode}");
        }

        // Procesar fecha de vencimiento (solo si no está vacía)
        $fechaVencimientoProcessed = null;
        if (!empty($fechaVencimiento)) {
            // Debug temporal
            Log::info("Procesando fecha: '{$fechaVencimiento}' para producto: {$barcode}");
            
            $fechaVencimientoProcessed = $this->parseDate($fechaVencimiento);
            if ($fechaVencimientoProcessed === false) {
                throw new \Exception("Formato de fecha inválido para el producto: {$barcode}. Fecha recibida: '{$fechaVencimiento}'");
            }
        }

        // Verificar si ya existe un registro con el mismo producto, almacén y fecha de vencimiento
        $existingRecord = DB::table('product_warehouse')
            ->where('producto_id', $product->id)
            ->where('almacen_id', $this->warehouseId)
            ->where('fecha_vencimiento', $fechaVencimientoProcessed)
            ->first();

        if ($existingRecord) {
            // Si existe, sumar al stock actual
            $newStock = $existingRecord->stock + $stock;
            DB::table('product_warehouse')
                ->where('id', $existingRecord->id)
                ->update([
                    'stock' => $newStock,
                    'updated_at' => now()
                ]);
        } else {
            // Si no existe, crear nuevo registro
            DB::table('product_warehouse')->insert([
                'producto_id' => $product->id,
                'almacen_id' => $this->warehouseId,
                'fecha_vencimiento' => $fechaVencimientoProcessed,
                'stock' => $stock,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->successCount++;
    }

    protected function parseDate($dateString)
    {
        // Limpiar la fecha
        $dateString = trim($dateString);
        
        // Si está vacío, retornar null
        if (empty($dateString)) {
            return null;
        }

        // Verificar si es un número serial de Excel
        if (is_numeric($dateString)) {
            $excelDate = (float) $dateString;
            
            // Excel usa días desde el 1 de enero de 1900
            // Pero Excel tiene un bug: considera 1900 como año bisiesto
            // Para fechas después del 28 de febrero de 1900, restar 1 día
            if ($excelDate >= 60) {
                $excelDate = $excelDate - 2;
            }
            
            $baseDate = new \DateTime('1900-01-01');
            $baseDate->add(new \DateInterval('P' . $excelDate . 'D'));
            
            return $baseDate->format('Y-m-d');
        }

        // Intentar diferentes formatos de fecha
        $formats = [
            'd/m/Y',    // 29/09/2026
            'd-m-Y',    // 29-09-2026
            'Y-m-d',    // 2026-09-29
            'd/m/y',    // 29/09/26
            'm/d/Y',    // 09/29/2026
            'j/n/Y',    // 29/9/2026 (sin ceros a la izquierda)
            'j-n-Y',    // 29-9-2026 (sin ceros a la izquierda)
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Si no funciona con Carbon, intentar con strtotime
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return false; // Retornar false si no se puede parsear
    }

    public function rules(): array
    {
        return [
            'codigo_barras' => 'required',
            'stock' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'codigo_barras.required' => 'El código de barras es obligatorio',
            'stock.required' => 'El stock es obligatorio',
            'stock.numeric' => 'El stock debe ser un número',
            'stock.min' => 'El stock no puede ser negativo',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
