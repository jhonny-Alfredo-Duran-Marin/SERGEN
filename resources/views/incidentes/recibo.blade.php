<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #dc3545;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #dc3545;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        .table th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .signature-box {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #000;
            width: 300px;
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .observation-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="header">
        <div class="title">RECIBO DE DEVOLUCIÓN</div>
        <div class="subtitle">Sistema de Gestión de Inventario</div>
    </div>

    <!-- Información del Incidente -->
    <div class="section">
        <div class="section-title">INFORMACIÓN DEL INCIDENTE</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Código Incidente:</span>
                <strong>{{ $devolucion->incidente->codigo }}</strong>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="badge badge-{{ $devolucion->incidente->tipo === 'PRESTAMO' ? 'success' : 'warning' }}">
                    {{ $devolucion->incidente->tipo }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Persona:</span>
                {{ $devolucion->incidente->persona->nombre }}
            </div>
            <div class="info-row">
                <span class="info-label">Fecha Incidente:</span>
                {{ $devolucion->incidente->fecha_incidente }}
            </div>
            <div class="info-row">
                <span class="info-label">Fecha Devolución:</span>
                {{ $devolucion->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Detalle de la Devolución -->
    <div class="section">
        <div class="section-title">DETALLE DE LA DEVOLUCIÓN</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Código Item</th>
                    <th>Descripción</th>
                    <th style="text-align:center;">Cantidad</th>
                    <th style="text-align:center;">Resultado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $devolucion->item->codigo }}</td>
                    <td>{{ $devolucion->item->descripcion }}</td>
                    <td style="text-align:center;">
                        <strong>{{ $devolucion->cantidad_devuelta }}</strong>
                    </td>
                    <td style="text-align:center;">
                        @php
                            $resultadoClass = [
                                'DEVUELTO_OK' => 'success',
                                'DEVUELTO_DANADO' => 'warning',
                                'NO_RECUPERADO' => 'danger',
                                'REPARABLE' => 'warning'
                            ];
                            $class = $resultadoClass[$devolucion->resultado] ?? 'success';
                        @endphp
                        <span class="badge badge-{{ $class }}">
                            {{ $devolucion->resultado }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Observaciones -->
    @if($devolucion->observacion)
    <div class="section">
        <div class="section-title">OBSERVACIONES</div>
        <div class="observation-box">
            {{ $devolucion->observacion }}
        </div>
    </div>
    @endif

    <!-- Impacto en Inventario -->
    <div class="section">
        <div class="section-title">IMPACTO EN INVENTARIO</div>
        <div class="info-box">
            @if($devolucion->resultado === 'DEVUELTO_OK')
                <div style="color: #28a745; font-weight: bold;">
                    ✓ Se sumaron {{ $devolucion->cantidad_devuelta }} unidades al inventario del item {{ $devolucion->item->codigo }}
                </div>
            @else
                <div style="color: #dc3545; font-weight: bold;">
                    ✗ No se realizaron cambios en el inventario debido al estado: {{ $devolucion->resultado }}
                </div>
            @endif
        </div>
    </div>

    <!-- Firma -->
    <div style="margin-top: 60px;">
        <div class="signature-box">
            <strong>Firma del Responsable</strong>
            <br><br>
            _________________________________
            <br>
            <small>{{ $devolucion->incidente->persona->nombre }}</small>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }} |
        Recibo ID: {{ $devolucion->id }} |
        Sistema de Gestión de Inventario
    </div>

</body>
</html>
