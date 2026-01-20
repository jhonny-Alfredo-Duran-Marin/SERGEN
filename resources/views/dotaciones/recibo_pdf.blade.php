<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
            margin: 20px;
        }

        /* Encabezado */
        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 25%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 75%;
            vertical-align: top;
            text-align: right;
            padding-left: 15px;
        }

        .logo {
            max-width: 120px;
            height: auto;
        }

        .company-info {
            font-size: 9px;
            color: #000;
            line-height: 1.4;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        /* Título principal */
        .title-section {
            border: 2px solid #000;
            text-align: center;
            padding: 12px;
            margin: 15px 0;
            background: #f5f5f5;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #000;
        }

        .subtitle {
            font-size: 10px;
            margin-top: 3px;
            color: #333;
            font-weight: normal;
        }

        /* Información principal */
        .info-section {
            margin: 15px 0;
            border: 1px solid #000;
            padding: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 10px;
            border: 1px solid #666;
        }

        .info-label {
            font-weight: bold;
            color: #000;
            width: 25%;
            background: #e8e8e8;
        }

        .info-value {
            color: #000;
            width: 25%;
        }

        /* Tabla de ítems */
        .items-section {
            margin: 20px 0;
        }

        .section-title {
            background: #000;
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 2px solid #000;
        }

        .items-table thead {
            background: #d0d0d0;
            color: #000;
        }

        .items-table th {
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        .items-table tbody tr {
            border: 1px solid #000;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table td {
            padding: 8px 6px;
            font-size: 10px;
            vertical-align: top;
            border: 1px solid #666;
        }

        .item-code {
            border: 1px solid #000;
            padding: 2px 6px;
            font-weight: bold;
            display: inline-block;
            font-size: 9px;
            background: #e8e8e8;
        }

        .item-description {
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }

        .item-observation {
            color: #333;
            font-size: 9px;
            font-style: italic;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Resumen */
        .summary-section {
            background: #f0f0f0;
            padding: 12px;
            border: 1px solid #000;
            margin: 15px 0;
        }

        .summary-table {
            width: 100%;
        }

        .summary-table td {
            padding: 4px 8px;
        }

        .summary-label {
            font-weight: bold;
            color: #000;
        }

        .summary-value {
            text-align: right;
            font-size: 12px;
            font-weight: bold;
            color: #000;
        }

        /* Firmas */
        .signatures {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-container {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 48%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1.5px solid #000;
            margin: 70px auto 8px;
            width: 75%;
        }

        .signature-title {
            font-weight: bold;
            font-size: 10px;
            color: #000;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .signature-name {
            font-size: 9px;
            color: #000;
        }

        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            color: #000;
            font-size: 8px;
        }

        .footer-note {
            background: #f5f5f5;
            border: 1px solid #999;
            padding: 10px 12px;
            margin: 12px 0;
            font-size: 9px;
        }

        .footer-note strong {
            color: #000;
        }

        /* Utilidades */
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }

        /* Página */
        @page {
            margin: 15mm;
        }

        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
            </div>
            <div class="header-right">
                <div class="company-name">Ser.Gen Telecomunicación & Construcción</div>
                <div class="company-info">
                    <strong>Dirección:</strong> Santa Cruz de la Sierra – Bolivia<br>
                    <strong>Teléfono:</strong> +591 69201292<br>
                    <strong>Email:</strong> nfabiola@sergenbol.co<br>
                    <strong>Fecha Emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Título -->
    <div class="title-section">
        <div class="title">{{ $titulo }}</div>
        <div class="subtitle">Registro de Asignación de Equipos y Materiales</div>
    </div>

    <!-- Información Principal -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">N° de Dotación:</td>
                <td class="info-value">
                    <strong>#{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }}</strong>
                </td>
                <td class="info-label">Estado:</td>
                <td class="info-value">
                    <strong>{{ $registro->estado_final }}</strong>
                </td>
            </tr>
            <tr>
                <td class="info-label">Responsable:</td>
                <td class="info-value" colspan="3">
                    <strong>{{ strtoupper($registro->persona->nombre) }}</strong>
                </td>
            </tr>
            <tr>
                <td class="info-label">Fecha de Dotación:</td>
                <td class="info-value">
                    {{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}
                </td>
                <td class="info-label">Hora:</td>
                <td class="info-value">
                    {{ \Carbon\Carbon::parse($registro->fecha)->format('H:i') }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Tipo de Registro:</td>
                <td class="info-value">DOTACIÓN DE EQUIPOS</td>
                <td class="info-label">Total Ítems:</td>
                <td class="info-value">
                    <strong>{{ $registro->items->count() }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla de Ítems -->
    <div class="items-section">
        <div class="section-title">
            DETALLE DE ÍTEMS ASIGNADOS
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">N°</th>
                    <th width="12%">Código</th>
                    <th width="38%">Descripción / Observación</th>
                    <th width="10%" class="text-center">Cantidad</th>
                    <th width="15%">Estado Origen</th>
                    <th width="20%">Próx. Renovación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registro->items as $index => $it)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="item-code">{{ $it->item->codigo }}</span>
                    </td>
                    <td>
                        <div class="item-description">{{ $it->item->descripcion }}</div>
                        @if($it->observacion)
                            <div class="item-observation">Obs: {{ $it->observacion }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <strong>{{ $it->cantidad }}</strong>
                    </td>
                    <td>
                        <strong>{{ $it->estado_item }}</strong>
                    </td>
                    <td>
                        @if($it->fecha_siguiente)
                            @php
                                $fechaSig = \Carbon\Carbon::parse($it->fecha_siguiente);
                                $vencido = $fechaSig->isPast();
                            @endphp
                            <strong>{{ $fechaSig->format('d/m/Y') }}</strong>
                            @if($vencido && $registro->estado_final == 'ABIERTA')
                                <div style="margin-top: 2px;"><strong>*** VENCIDO ***</strong></div>
                            @endif
                        @else
                            No programada
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label" width="70%">Total de Ítems Diferentes:</td>
                <td class="summary-value">{{ $registro->items->count() }}</td>
            </tr>
            <tr>
                <td class="summary-label">Cantidad Total de Unidades:</td>
                <td class="summary-value">{{ $registro->items->sum('cantidad') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Ítems con Renovación Programada:</td>
                <td class="summary-value">{{ $registro->items->whereNotNull('fecha_siguiente')->count() }}</td>
            </tr>
        </table>
    </div>

    <!-- Nota Adicional -->
    @if($registro->nota)
    <div class="footer-note">
        <strong>OBSERVACIONES:</strong><br>
        {{ $registro->nota }}
    </div>
    @endif

    <!-- Términos y Condiciones -->
    <div class="footer-note">
        <strong>TÉRMINOS Y CONDICIONES:</strong><br>
        1. El responsable se compromete a cuidar y usar adecuadamente los equipos asignados.<br>
        2. Cualquier daño o pérdida debe ser reportado inmediatamente al departamento de almacén.<br>
        3. Los ítems deben ser devueltos en las fechas de renovación indicadas o al término del contrato.<br>
        4. Este documento es válido como comprobante oficial de entrega-recepción.
    </div>

    <!-- Firmas -->
    <div class="signatures">
        <div class="signature-container">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">ENTREGADO POR</div>
                <div class="signature-name">Almacén Ser.Gen Telecomunicación & Construcción</div>
                <div class="signature-name" style="margin-top: 5px;">
                    Fecha: ______________________
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">RECIBIDO POR</div>
                <div class="signature-name">{{ strtoupper($registro->persona->nombre) }}</div>
                <div class="signature-name" style="margin-top: 5px;">
                    C.I.: ______________________
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Este documento fue generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }} a las {{ \Carbon\Carbon::now()->format('H:i') }}<br>
            Ser.Gen Telecomunicación & Construcción - Santa Cruz de la Sierra, Bolivia<br>
            Documento N°: DOT-{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }} | Página 1 de 1
        </p>
    </div>
</body>
</html>
