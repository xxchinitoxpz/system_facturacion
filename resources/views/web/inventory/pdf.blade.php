<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        
        .info-section {
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        
        .info-section h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 15px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-card {
            display: table-cell;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 25%;
            vertical-align: top;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .table th {
            background: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .table tr:hover {
            background: #f3f4f6;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .filters-section {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filters-section h4 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 13px;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #475569;
        }
        
        .filter-value {
            color: #64748b;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .stats-grid {
                display: table !important;
                width: 100% !important;
            }
            
            .stats-row {
                display: table-row !important;
            }
            
            .stat-card {
                display: table-cell !important;
                width: 25% !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Inventario</h1>
        <div class="subtitle">Generado el {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Información de filtros aplicados -->
    @if($filtros['search'] || $filtros['almacen_id'] || $filtros['mostrar_stock_cero'])
    <div class="filters-section">
        <h4>Filtros Aplicados:</h4>
        @if($filtros['search'])
            <div class="filter-item">
                <span class="filter-label">Búsqueda:</span>
                <span class="filter-value">"{{ $filtros['search'] }}"</span>
            </div>
        @endif
        @if($filtros['almacen_id'] && $almacen_nombre)
            <div class="filter-item">
                <span class="filter-label">Almacén:</span>
                <span class="filter-value">{{ $almacen_nombre }}</span>
            </div>
        @endif
        @if($filtros['mostrar_stock_cero'])
            <div class="filter-item">
                <span class="filter-label">Incluir stock cero:</span>
                <span class="filter-value">Sí</span>
            </div>
        @endif
    </div>
    @endif

    <!-- Estadísticas generales -->
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($total_productos) }}</div>
                <div class="stat-label">Total Productos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($total_stock) }}</div>
                <div class="stat-label">Total Stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($productos_vencidos) }}</div>
                <div class="stat-label">Productos Vencidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($productos_por_vencer) }}</div>
                <div class="stat-label">Por Vencer (30 días)</div>
            </div>
        </div>
    </div>

    <!-- Tabla de inventario -->
    <div class="table-container">
        @if($inventory->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código de Barras</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Fecha Vencimiento</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->producto_nombre }}</strong>
                            </td>
                            <td>{{ $item->barcode }}</td>
                            <td>{{ $item->categoria_nombre }}</td>
                            <td>{{ $item->marca_nombre }}</td>
                            <td class="text-center">
                                <strong>{{ number_format($item->stock) }}</strong>
                            </td>
                            <td class="text-center">
                                @if($item->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') }}
                                @else
                                    <span style="color: #9ca3af;">Sin fecha</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->fecha_vencimiento)
                                    @php
                                        $fechaVencimiento = \Carbon\Carbon::parse($item->fecha_vencimiento);
                                        $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                                    @endphp
                                    @if($fechaVencimiento->isPast())
                                        <span class="badge badge-danger">Vencido</span>
                                    @elseif($diasRestantes > 0 && $diasRestantes <= 30)
                                        <span class="badge badge-warning">Por Vencer</span>
                                    @else
                                        <span class="badge badge-success">Vigente</span>
                                    @endif
                                @else
                                    <span class="badge badge-success">Sin Fecha</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <h3>No se encontraron productos</h3>
                <p>No hay productos que coincidan con los filtros aplicados.</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }} | Sistema de Facturación</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>
