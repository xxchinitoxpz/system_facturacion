<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($sale->tipo_comprobante) }} - {{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 8px;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
            text-align: center;
        }
        .document-number {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .client-section {
            text-align: center;
            margin: 10px 0;
        }
        .client-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .client-document {
            font-size: 9px;
            margin-bottom: 10px;
        }
        .date-time {
            text-align: left;
            font-size: 9px;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 3px 2px;
            font-weight: bold;
            font-size: 8px;
        }
        .items-table td {
            padding: 2px;
            border-bottom: 1px dotted #ccc;
            font-size: 8px;
        }
        .item-qty {
            text-align: center;
            width: 15%;
        }
        .item-um {
            text-align: center;
            width: 15%;
        }
        .item-code {
            text-align: center;
            width: 20%;
        }
        .item-price {
            text-align: right;
            width: 15%;
        }
        .item-total {
            text-align: right;
            width: 15%;
        }
        .item-description {
            width: 20%;
            font-size: 7px;
        }
        .totals {
            margin: 10px 0;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 9px;
        }
        .total-final {
            font-weight: bold;
            font-size: 11px;
            border-top: 2px solid #000;
            padding-top: 5px;
        }
        .total-words {
            font-size: 8px;
            margin: 5px 0;
            text-align: left;
        }
        .payment-info {
            margin: 10px 0;
            font-size: 9px;
        }
        .payment-row {
            margin-bottom: 3px;
        }
        .observations {
            margin: 10px 0;
            font-size: 9px;
        }
        .qr-section {
            text-align: center;
            margin: 15px 0;
        }
        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5px;
            background: #f0f0f0;
            text-align: center;
            word-break: break-all;
            padding: 2px;
        }
        .sunat-info {
            font-size: 7px;
            text-align: center;
            margin: 5px 0;
            color: #666;
        }
        .footer-logo {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Datos de la Empresa -->
    <div class="header">
        <div class="company-name">{{ $sale->branch->company->razon_social ?? 'EMPRESA DEMO SAC' }}</div>
        <div class="company-info">
            RUC: {{ $sale->branch->company->ruc ?? '20100100100' }}<br>
            {{ $sale->branch->company->direccion ?? 'CALLE LAS NORMAS 123' }}<br>
            Telf: {{ $sale->branch->company->telefono ?? '987 654 321' }}
        </div>
    </div>

    <div class="divider"></div>

    <!-- Título del Documento -->
    <div class="document-title">
        {{ strtoupper($sale->tipo_comprobante) }} DE VENTA ELECTRÓNICA
    </div>

    <!-- Número de Documento -->
    <div class="document-number">
        {{ $sunatResponse->numero_documento ?? 'B002 - 10300686' }}
    </div>

    <!-- Información del Cliente -->
    <div class="client-section">
        <div class="client-name">{{ $sale->client->nombre_completo ?? 'CLIENTE GENERAL' }}</div>
        <div class="divider"></div>
        <div class="client-document">{{ $sale->client->tipo_documento ?? 'DNI' }} {{ $sale->client->nro_documento ?? '00000000' }}</div>
    </div>

    <!-- Fecha y Hora -->
    <div class="date-time">
        FECHA: {{ $sale->fecha_venta ? $sale->fecha_venta->format('d/m/Y') : 'N/A' }} HORA: {{ $sale->fecha_venta ? $sale->fecha_venta->format('h:i A') : 'N/A' }}
    </div>

    <div class="divider"></div>

    <!-- Tabla de Productos -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="item-qty">Cant</th>
                <th class="item-um">U.M</th>
                <th class="item-code">COD</th>
                <th class="item-price">PRECIO</th>
                <th class="item-total">TOTAL</th>
            </tr>
            <tr>
                <th colspan="5" class="item-description">DESCRIPCION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->products as $product)
            <tr>
                <td class="item-qty">{{ $product->pivot->cantidad }}</td>
                <td class="item-um">UNIDAD</td>
                <td class="item-code">{{ $product->barcode ?? '9810007005004' }}</td>
                <td class="item-price">{{ number_format($product->pivot->precio_unitario, 2) }}</td>
                <td class="item-total">{{ number_format($product->pivot->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="item-description">
                    {{ $product->pivot->nombre_producto ?? $product->nombre }}
                    @if($product->pivot->presentacion_id)
                        (Presentación)
                    @endif
                </td>
            </tr>
            @endforeach
            
            @foreach($sale->combos as $combo)
            <tr>
                <td class="item-qty">{{ $combo->pivot->cantidad }}</td>
                <td class="item-um">UNIDAD</td>
                <td class="item-code">C{{ str_pad($combo->id, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="item-price">{{ number_format($combo->pivot->precio_unitario, 2) }}</td>
                <td class="item-total">{{ number_format($combo->pivot->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="item-description">
                    {{ $combo->pivot->nombre ?? $combo->nombre }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totales -->
    <div class="totals">
        <div class="total-row">
            <span>TOTAL GRAVADO (S/)</span>
            <span>{{ number_format($sale->total / 1.18, 2) }}</span>
        </div>
        <div class="total-row">
            <span>I.G.V (S/)</span>
            <span>{{ number_format($sale->total - ($sale->total / 1.18), 2) }}</span>
        </div>
        <div class="total-row total-final">
            <span>TOTAL (S/)</span>
            <span>{{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <!-- Total en Palabras -->
    <div class="total-words">
        SON: {{ strtoupper(\App\Helpers\NumberHelper::numberToWords($sale->total)) }} SOLES
    </div>

    <div class="divider"></div>

    <!-- Información de Pago -->
    @if($sale->payments->count() > 0)
    <div class="payment-info">
        @foreach($sale->payments as $payment)
        <div class="payment-row">
            FORMA DE PAGO: 
            @switch($payment->tipo_pago)
                @case(1) EFECTIVO @break
                @case(2) TARJETA @break
                @case(3) TARJETA @break
                @case(4) TRANSFERENCIA @break
                @case(5) BILLETERA VIRTUAL @break
                @case(6) BILLETERA VIRTUAL @break
                @case(7) OTROS @break
                @default EFECTIVO
            @endswitch
        </div>
        <div class="payment-row">
            COND.VENTA: CONTADO
        </div>
        @endforeach
    </div>
    @endif

    <!-- Observaciones -->
    <div class="observations">
        Observaciones: {{ $sale->observaciones ?: ' ' }}
    </div>

    <!-- Código QR 
    <div class="qr-section">
        <div class="qr-placeholder">
            QR CODE<br>
            {{ $codigoQR ?? 'HASH_DEL_DOCUMENTO' }}
        </div>
    </div>
    -->
    <!-- Hash del Documento -->
    @if($sunatResponse && $sunatResponse->hash_documento)
    <div class="sunat-info">
        <strong>HASH DEL DOCUMENTO</strong><br>
        <span style="font-size: 6px; word-break: break-all;">{{ $sunatResponse->hash_documento }}</span>
    </div>
    @endif

    <!-- Información SUNAT -->
    <div class="sunat-info">
        <strong>Representación Impresa de la {{ strtoupper($sale->tipo_comprobante) }} DE VENTA ELECTRÓNICA</strong><br>
        Puede consultar en: WWW.SUNAT.GOB.PE<br>
    </div>

</body>
</html>

