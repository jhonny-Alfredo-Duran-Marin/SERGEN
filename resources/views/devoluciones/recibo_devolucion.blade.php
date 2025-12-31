<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RECIBO DE DEVOLUCIÓN #{{ $registro->codigo }}</title>
    <style>
        @page { margin: 25mm 15mm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
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
            font-size: 11px;
            line-height: 1.4;
        }
        .titulo-recibo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .info {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info td { padding: 3px 0; }
        .estado-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .estado-completa { background-color: #28a745; color: white; }
        .estado-parcial { background-color: #ffc107; color: black; }
        .estado-pendiente { background-color: #6c757d; color: white; }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.detalle th,
        table.detalle td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
        }
        table.detalle th {
            background-color: #eaeaea;
            text-align: center;
        }
        .seccion-titulo {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            border: 1px solid #000;
        }
        .estado-item {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .estado-ok { background-color: #d4edda; color: #155724; }
        .estado-faltante { background-color: #fff3cd; color: #856404; }
        .estado-danado { background-color: #f8d7da; color: #721c24; }
        .estado-perdido { background-color: #d6d8db; color: #383d41; }
        .estado-consumido { background-color: #cce5ff; color: #004085; }

        .firmas {
            margin-top: 80px;
            text-align: center;
            font-size: 11px;
        }
        .linea {
            border-top: 1px solid #000;
            width: 220px;
            margin: 0 auto 5px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #444;
        }
        .clear { clear: both; }
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
        RECIBO DE DEVOLUCIÓN N° {{ str_pad($registro->codigo, 6, '0', STR_PAD_LEFT) }}
    </div>

    <!-- INFO -->
    <table class="info">
        <tr>
            <td width="50%">
                <strong>Préstamo:</strong> {{ $registro->prestamo_codigo }}
            </td>
            <td width="50%" align="right">
                <strong>Fecha Devolución:</strong> {{ $registro->fecha->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td>
               <strong>Responsable:</strong> {{ $registro->prestamo->persona->nombre }}
            </td>
            <td align="right">
                <strong>Estado:</strong>
                <span class="estado-badge estado-{{ strtolower($registro->estado) }}">
                    {{ $registro->estado }}
                </span>
            </td>
        </tr>
        @if($registro->proyecto)
        <tr>
            <td colspan="2">
                <strong>Proyecto:</strong> {{ $registro->proyecto->codigo }} - {{ $registro->proyecto->descripcion }}
            </td>
        </tr>
        @endif
        <tr>
            <td colspan="2">
                <strong>Registrado por:</strong> {{ $registro->user->name ?? 'Sistema' }}
            </td>
        </tr>
    </table>

    <!-- ITEMS SUELTOS -->
    @if($registro->detalles->count() > 0)
        <div class="seccion-titulo">
            ITEMS SUELTOS DEVUELTOS
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
                @foreach($registro->detalles as $det)
                <tr>
                    <td>{{ $det->item->codigo }}</td>
                    <td>{{ $det->item->descripcion }}</td>
                    <td align="center">{{ $det->cantidad }}</td>
                    <td align="center">
                        <span class="estado-item estado-{{ strtolower($det->estado) }}">
                            {{ strtoupper($det->estado) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- ITEMS DE KITS -->
    @if($registro->detallesKit->count() > 0)
        <div class="seccion-titulo" style="margin-top: 20px;">
            ITEMS DE KITS DEVUELTOS
        </div>

        <table class="detalle">
            <thead>
                <tr>
                    <th width="15%">Kit</th>
                    <th width="15%">Código Item</th>
                    <th>Descripción</th>
                    <th width="12%">Cantidad</th>
                    <th width="15%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registro->detallesKit as $det)
                <tr>
                    <td>{{ $det->kit->codigo }}</td>
                    <td>{{ $det->item->codigo }}</td>
                    <td>{{ $det->item->descripcion }}</td>
                    <td align="center">{{ $det->cantidad }}</td>
                    <td align="center">
                        <span class="estado-item estado-{{ strtolower($det->estado) }}">
                            {{ strtoupper($det->estado) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- RESUMEN -->
    <table width="100%" style="margin-top:25px; font-size: 11px;">
        <tr>
            <td width="70%"></td>
            <td width="30%">
                <table width="100%" style="border: 1px solid #000;">
                    <tr style="background-color: #f0f0f0;">
                        <td style="padding: 5px;"><strong>Resumen de Devolución</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;">
                            <strong>Items sueltos:</strong> {{ $registro->detalles->count() }}<br>
                            <strong>Items de kits:</strong> {{ $registro->detallesKit->count() }}<br>
                            <strong>Total items:</strong> {{ $registro->detalles->count() + $registro->detallesKit->count() }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="clear"></div>

    <!-- FIRMAS -->
    <div class="firmas">
        <table width="100%">
            <tr>
                <td width="50%">
                    <div class="linea"></div>
                    Firma Recepción (Almacén)
                </td>
                <td width="50%">
                    <div class="linea"></div>
                    Firma Entrega ({{ $registro->prestamo->persona->nombre}})
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento generado por el Sistema de Gestión de Inventarios – Ser.Gen<br>
        Devolución correspondiente al préstamo {{ $registro->prestamo_codigo }}
    </div>

</body>
</html>
