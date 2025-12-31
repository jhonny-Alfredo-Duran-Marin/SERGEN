<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>HISTORIAL DE DEVOLUCIONES - {{ $prestamo->codigo }}</title>
    <style>
        @page { margin: 25mm 15mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .empresa-datos {
            text-align: right;
            font-size: 10px;
            line-height: 1.4;
        }
        .titulo-recibo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .info-prestamo {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 20px;
        }
        .info-prestamo table {
            width: 100%;
            font-size: 11px;
        }
        .info-prestamo td {
            padding: 3px 0;
        }
        .devolucion-box {
            border: 1px solid #000;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .devolucion-header {
            background-color: #343a40;
            color: white;
            padding: 8px;
            font-weight: bold;
        }
        .devolucion-info {
            background-color: #f8f9fa;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        table.detalle {
            width: 100%;
            border-collapse: collapse;
        }
        table.detalle th,
        table.detalle td {
            border: 1px solid #dee2e6;
            padding: 5px;
            font-size: 10px;
        }
        table.detalle th {
            background-color: #e9ecef;
            text-align: center;
        }
        .estado-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .estado-completa { background-color: #28a745; color: white; }
        .estado-parcial { background-color: #ffc107; color: black; }
        .estado-pendiente { background-color: #6c757d; color: white; }
        .estado-ok { background-color: #d4edda; color: #155724; }
        .estado-faltante { background-color: #fff3cd; color: #856404; }
        .estado-danado { background-color: #f8d7da; color: #721c24; }
        .estado-perdido { background-color: #d6d8db; color: #383d41; }
        .estado-consumido { background-color: #cce5ff; color: #004085; }
        .resumen-final {
            background-color: #e9ecef;
            border: 2px solid #000;
            padding: 10px;
            margin-top: 20px;
            font-size: 11px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #444;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <table width="100%">
            <tr>
                <td width="50%">
                    <img src="{{ $logoBase64 }}" style="width:140px">
                </td>
                <td width="50%" class="empresa-datos">
                    <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                    Santa Cruz de la Sierra – Bolivia<br>
                    Tel: +591 69201292<br>
                    Email: nfabiola@sergenbol.co
                </td>
            </tr>
        </table>
    </div>

    <!-- TITULO -->
    <div class="titulo-recibo">
        HISTORIAL DE DEVOLUCIONES<br>
        PRÉSTAMO {{ $prestamo->codigo }}
    </div>

    <!-- INFO PRESTAMO -->
    <div class="info-prestamo">
        <table>
            <tr>
                <td width="25%"><strong>Responsable:</strong></td>
                <td width="25%">{{ $prestamo->persona->nombre }}</td>
                <td width="25%"><strong>Estado Préstamo:</strong></td>
                <td width="25%">
                    <span class="estado-badge estado-{{ strtolower($prestamo->estado) }}">
                        {{ $prestamo->estado }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Fecha Préstamo:</strong></td>
                <td>{{ $prestamo->fecha->format('d/m/Y h:i A') }}</td>
                <td><strong>Total Devoluciones:</strong></td>
                <td><strong>{{ $prestamo->devoluciones->count() }}</strong></td>
            </tr>
            @if($prestamo->proyecto)
            <tr>
                <td><strong>Proyecto:</strong></td>
                <td colspan="3">{{ $prestamo->proyecto->codigo }} - {{ $prestamo->proyecto->descripcion }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- DEVOLUCIONES -->
    @foreach($prestamo->devoluciones as $index => $dev)
        <div class="devolucion-box">
            <!-- Header de devolución -->
            <div class="devolucion-header">
                DEVOLUCIÓN #{{ $dev->id }}
                <span style="float: right;">
                    <span class="estado-badge estado-{{ strtolower($dev->estado) }}">
                        {{ $dev->estado }}
                    </span>
                </span>
            </div>

            <!-- Info de devolución -->
            <div class="devolucion-info">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <strong>Fecha:</strong> {{ $dev->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td width="50%" align="right">
                            <strong>Registrado por:</strong> {{ $dev->user->name ?? 'Sistema' }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Items Sueltos -->
            @if($dev->detalles->count() > 0)
                <div style="padding: 8px; background-color: #e3f2fd; font-weight: bold; font-size: 10px;">
                    ITEMS SUELTOS ({{ $dev->detalles->count() }})
                </div>
                <table class="detalle">
                    <thead>
                        <tr>
                            <th width="15%">Código</th>
                            <th>Descripción</th>
                            <th width="12%">Cantidad</th>
                            <th width="15%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dev->detalles as $det)
                        <tr>
                            <td>{{ $det->item->codigo }}</td>
                            <td>{{ $det->item->descripcion }}</td>
                            <td align="center">{{ $det->cantidad }}</td>
                            <td align="center">
                                <span class="estado-badge estado-{{ strtolower($det->estado) }}">
                                    {{ $det->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Items de Kits -->
            @if($dev->detallesKit->count() > 0)
                <div style="padding: 8px; background-color: #fff3cd; font-weight: bold; font-size: 10px; margin-top: 5px;">
                    ITEMS DE KITS ({{ $dev->detallesKit->count() }})
                </div>
                <table class="detalle">
                    <thead>
                        <tr>
                            <th width="15%">Kit</th>
                            <th width="12%">Código</th>
                            <th>Descripción</th>
                            <th width="10%">Cant.</th>
                            <th width="15%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dev->detallesKit as $det)
                        <tr>
                            <td>{{ $det->kit->codigo }}</td>
                            <td>{{ $det->item->codigo }}</td>
                            <td>{{ $det->item->descripcion }}</td>
                            <td align="center">{{ $det->cantidad }}</td>
                            <td align="center">
                                <span class="estado-badge estado-{{ strtolower($det->estado) }}">
                                    {{ $det->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($index < $prestamo->devoluciones->count() - 1 && ($index + 1) % 2 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach

    <!-- RESUMEN FINAL -->
    <div class="resumen-final">
        <strong>RESUMEN GENERAL</strong>
        <table width="100%" style="margin-top: 10px;">
            <tr>
                <td width="50%">
                    <strong>Total Devoluciones Registradas:</strong> {{ $prestamo->devoluciones->count() }}<br>
                    <strong>Devoluciones Completas:</strong> {{ $prestamo->devoluciones->where('estado', 'Completa')->count() }}<br>
                    <strong>Devoluciones Parciales:</strong> {{ $prestamo->devoluciones->where('estado', 'Parcial')->count() }}
                </td>
                <td width="50%">
                    @php
                        $totalItemsSueltos = $prestamo->devoluciones->sum(function($d) { return $d->detalles->count(); });
                        $totalItemsKits = $prestamo->devoluciones->sum(function($d) { return $d->detallesKit->count(); });
                    @endphp
                    <strong>Total Items Sueltos Devueltos:</strong> {{ $totalItemsSueltos }}<br>
                    <strong>Total Items de Kits Devueltos:</strong> {{ $totalItemsKits }}<br>
                    <strong>Total General:</strong> {{ $totalItemsSueltos + $totalItemsKits }}
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento generado por el Sistema de Gestión de Inventarios – Ser.Gen<br>
        Historial completo de devoluciones del préstamo {{ $prestamo->codigo }}
    </div>

</body>
</html>
