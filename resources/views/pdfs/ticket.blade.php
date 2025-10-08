<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 5px;  
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .branch-name {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .ticket-info {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .client-info {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .products-table {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .product-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .product-name {
            flex: 2;
            text-align: left;
        }
        .product-qty {
            flex: 1;
            text-align: center;
        }
        .product-price {
            flex: 1;
            text-align: right;
        }
        .product-total {
            flex: 1;
            text-align: right;
            font-weight: bold;
        }
        .total-section {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .grand-total {
            font-size: 12px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .payment-info {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 8px;
        }
        .thank-you {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .date-time {
            font-size: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $sale->branch->company->razon_social ?? 'SISTEMA DE FACTURACIÓN' }}</div>
        <div class="branch-name">{{ $sale->branch->nombre ?? 'Sucursal' }}</div>
        <div>RUC: {{ $sale->branch->company->ruc ?? 'N/A' }}</div>
        <div>Dirección: {{ $sale->branch->direccion ?? 'N/A' }}</div>
    </div>

    <!-- Información del Ticket -->
    <div class="ticket-info">
        <div class="ticket-row">
            <span>Tipo:</span>
            <span>{{ strtoupper($sale->tipo_comprobante) }}</span>
        </div>
        <div class="ticket-row">
            <span>Serie:</span>
            <span>{{ $sale->serie ?? 'N/A' }}</span>
        </div>
        <div class="ticket-row">
            <span>Correlativo:</span>
            <span>{{ $sale->correlativo ?? 'N/A' }}</span>
        </div>
        <div class="ticket-row">
            <span>Fecha:</span>
            <span>{{ $sale->fecha_venta ? $sale->fecha_venta->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div class="ticket-row">
            <span>Hora:</span>
            <span>{{ $sale->fecha_venta ? $sale->fecha_venta->format('H:i') : 'N/A' }}</span>
        </div>
        <div class="ticket-row">
            <span>Vendedor:</span>
            <span>
                @if($sale->user && $sale->user->name)
                    @php
                        $nombre = $sale->user->name;
                        $primerasLetras = substr($nombre, 0, 4);
                        $nombreEnmascarado = $primerasLetras . '*****';
                    @endphp
                    {{ $nombreEnmascarado }}
                @else
                    N/A
                @endif
            </span>
        </div>
    </div>

    <!-- Información del Cliente -->
    <div class="client-info">
        <div class="ticket-row">
            <span>Cliente:</span>
            <span>{{ $sale->client->nombre_completo ?? 'CLIENTE GENERAL' }}</span>
        </div>
        <div class="ticket-row">
            <span>Documento:</span>
            <span>{{ $sale->client->nro_documento ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Productos -->
    <div class="products-table">
        <div class="product-row" style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 5px;">
            <span class="product-name">PRODUCTO</span>
            <span class="product-qty">CANT</span>
            <span class="product-price">P.U.</span>
            <span class="product-total">TOTAL</span>
        </div>
        
        @foreach($sale->products as $product)
            <div class="product-row">
                <span class="product-name">{{ $product->pivot->nombre_producto }}</span>
                <span class="product-qty">{{ $product->pivot->cantidad }}</span>
                <span class="product-price">S/{{ number_format($product->pivot->precio_unitario, 2) }}</span>
                <span class="product-total">S/{{ number_format($product->pivot->subtotal, 2) }}</span>
            </div>
        @endforeach
        
        @foreach($sale->combos as $combo)
            <div class="product-row">
                <span class="product-name">{{ $combo->nombre }}</span>
                <span class="product-qty">{{ $combo->pivot->cantidad }}</span>
                <span class="product-price">S/{{ number_format($combo->pivot->precio_unitario, 2) }}</span>
                <span class="product-total">S/{{ number_format($combo->pivot->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>

    <!-- Totales -->
    <div class="total-section">
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>S/{{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <!-- Información del Pago -->
    @if($sale->payments->count() > 0)
        <div class="payment-info">
            @foreach($sale->payments as $payment)
                <div class="ticket-row">
                    <span>Forma de Pago:</span>
                    <span>
                        @switch($payment->tipo_pago)
                            @case(1)
                                EFECTIVO
                                @break
                            @case(2)
                                TARJETA
                                @break
                            @case(3)
                                TARJETA
                                @break
                            @case(4)
                                TRANSFERENCIA
                                @break
                            @case(5)
                                BILLETERA VIRTUAL
                                @break
                            @case(6)
                                BILLETERA VIRTUAL
                                @break
                            @case(7)
                                OTROS
                                @break
                            @default
                                N/A
                        @endswitch
                    </span>
                </div>
                <div class="ticket-row">
                    <span>Monto Recibido:</span>
                    <span>S/{{ number_format($payment->monto_recibido, 2) }}</span>
                </div>
                <div class="ticket-row">
                    <span>Vuelto:</span>
                    <span>S/{{ number_format($payment->vuelto, 2) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="thank-you">¡GRACIAS POR SU COMPRA!</div>
        <div>Este documento es una representación impresa</div>
        <div>de un comprobante electrónico</div>
        <div class="date-time">
            Impreso: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
