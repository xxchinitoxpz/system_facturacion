<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Sesión de Caja</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4f46e5;
            font-size: 20px;
            margin: 0 0 8px 0;
        }
        
        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .info-section {
            margin-bottom: 15px;
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #4f46e5;
        }
        
        .info-section h3 {
            color: #4f46e5;
            font-size: 14px;
            margin: 0 0 10px 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 25%;
            padding: 3px 0;
        }
        
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        
        .main-content {
            display: flex;
            gap: 15px;
        }
        
        .left-column {
            flex: 1;
        }
        
        .right-column {
            flex: 1;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-abierta {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-cerrada {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .status-temporal {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .totals-section {
            margin: 15px 0;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .totals-table th,
        .totals-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        
        .totals-table th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
        }
        
        .totals-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .amount-positive {
            color: #059669;
        }
        
        .amount-negative {
            color: #dc2626;
        }
        
        .movements-section {
            margin: 15px 0;
        }
        
        .movements-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }
        
        .movements-table th,
        .movements-table td {
            border: 1px solid #d1d5db;
            padding: 4px;
            text-align: left;
        }
        
        .movements-table th {
            background-color: #6b7280;
            color: white;
            font-weight: bold;
        }
        
        .movements-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #d1d5db;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE DE SESIÓN DE CAJA</h1>
        <h2>{{ $session->cashBox->nombre }} - {{ $session->cashBox->branch->nombre }}</h2>
    </div>

    <!-- Contenido principal en dos columnas -->
    <div class="main-content">
        <!-- Columna izquierda - Información de la Sesión -->
        <div class="left-column">
            <div class="info-section">
                <h3>Información de la Sesión</h3>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">ID de Sesión:</div>
                        <div class="info-value">#{{ $session->id }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Caja:</div>
                        <div class="info-value">{{ $session->cashBox->nombre }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Sucursal:</div>
                        <div class="info-value">{{ $session->cashBox->branch->nombre }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Usuario:</div>
                        <div class="info-value">{{ $session->user->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Apertura:</div>
                        <div class="info-value">{{ $session->fecha_hora_apertura->format('d/m/Y H:i:s') }}</div>
                    </div>
                    @if($session->fecha_hora_cierre)
                    <div class="info-row">
                        <div class="info-label">Cierre:</div>
                        <div class="info-value">{{ $session->fecha_hora_cierre->format('d/m/Y H:i:s') }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">
                            <span class="status status-{{ $session->estado }}">
                                {{ ucfirst($session->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha - Montos -->
        <div class="right-column">
            <div class="info-section">
                <h3>Montos de la Sesión</h3>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Monto Apertura:</div>
                        <div class="info-value amount amount-positive">S/ {{ number_format($session->monto_apertura, 2) }}</div>
                    </div>
                    @if($session->monto_cierre)
                    <div class="info-row">
                        <div class="info-label">Monto Cierre:</div>
                        <div class="info-value amount amount-positive">S/ {{ number_format($session->monto_cierre, 2) }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Saldo Actual:</div>
                        <div class="info-value amount {{ $session->saldo_actual >= 0 ? 'amount-positive' : 'amount-negative' }}">
                            S/ {{ number_format($session->saldo_actual, 2) }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Efectivo en Caja:</div>
                        <div class="info-value amount amount-positive">S/ {{ number_format($montoEnCaja, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Totales por Método de Pago -->
    @if($totalesPorMetodo->count() > 0)
    <div class="totals-section">
        <h3>Totales por Método de Pago</h3>
        <table class="totals-table">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Ingresos</th>
                    <th>Salidas</th>
                    <th>Neto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($totalesPorMetodo as $metodo => $movimientos)
                    @php
                        $ingresos = $movimientos->where('tipo', 'ingreso')->sum('total');
                        $salidas = $movimientos->where('tipo', 'salida')->sum('total');
                        $neto = $ingresos - $salidas;
                    @endphp
                    <tr>
                        <td><strong>{{ ucfirst($metodo) }}</strong></td>
                        <td class="amount amount-positive">S/ {{ number_format($ingresos, 2) }}</td>
                        <td class="amount amount-negative">S/ {{ number_format($salidas, 2) }}</td>
                        <td class="amount {{ $neto >= 0 ? 'amount-positive' : 'amount-negative' }}">
                            S/ {{ number_format($neto, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Movimientos -->
    @if($session->movements->count() > 0)
    <div class="movements-section">
        <h3>Movimientos de la Sesión</h3>
        <table class="movements-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($session->movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <span class="status status-{{ $movement->tipo }}">
                            {{ ucfirst($movement->tipo) }}
                        </span>
                    </td>
                    <td>{{ ucfirst($movement->metodo_pago ?? 'N/A') }}</td>
                    <td>{{ $movement->descripcion }}</td>
                    <td class="amount {{ $movement->tipo === 'ingreso' ? 'amount-positive' : 'amount-negative' }}">
                        {{ $movement->tipo === 'ingreso' ? '+' : '-' }}S/ {{ number_format($movement->monto, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Facturación - {{ config('app.name') }}</p>
    </div>
</body>
</html>
