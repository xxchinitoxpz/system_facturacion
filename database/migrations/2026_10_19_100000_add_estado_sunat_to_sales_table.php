<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estados SUNAT de boletas/facturas:
     * pendiente (registrado, sin enviar), aceptado, rechazado, error (se puede reenviar),
     * anulado (anulado antes de emitirse, no se envía). Null para tickets.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('estado_sunat')->nullable()->after('correlativo')->index();
        });

        $comprobantes = DB::table('sales')
            ->whereIn(DB::raw('LOWER(tipo_comprobante)'), ['boleta', 'factura'])
            ->get(['id', 'serie', 'correlativo']);

        foreach ($comprobantes as $sale) {
            $aceptado = DB::table('sunat_responses')
                ->where('venta_id', $sale->id)
                ->where('exitoso', true)
                ->orderByDesc('id')
                ->first();

            if ($aceptado) {
                $data = ['estado_sunat' => 'aceptado'];

                // Completar serie y correlativo a partir del número emitido (ej. B001-00000123)
                if ((!$sale->serie || !$sale->correlativo) && preg_match('/^([A-Z]\w+)-(\d+)$/', $aceptado->numero_documento, $m)) {
                    $data['serie'] = $m[1];
                    $data['correlativo'] = (int) $m[2];
                }

                DB::table('sales')->where('id', $sale->id)->update($data);
                continue;
            }

            $tieneRespuesta = DB::table('sunat_responses')->where('venta_id', $sale->id)->exists();

            DB::table('sales')->where('id', $sale->id)->update([
                'estado_sunat' => $tieneRespuesta ? 'error' : 'pendiente',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['estado_sunat']);
            $table->dropColumn('estado_sunat');
        });
    }
};
