<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($titulo) }} #{{ $registro->codigo }}</title>
    <style>
        @page {
            margin: 25mm 15mm;
        }

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
        }

        .info td {
            padding: 3px 0;
        }

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

        .firmas {
            margin-top: 80px;
            text-align: center;
            font-size: 11px;
        }

        .linea {
            border-top: 1px solid #000;
            width: 200px;
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

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <div class="header">
        <table width="100%">
            <tr>
                <td width="50%"><img src="{{ $logoBase64 }}" style="width:140px"></td>
                <td width="50%" class="empresa-datos">
                    <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                    Santa Cruz de la Sierra – Bolivia<br>
                    Tel: +591 69201292<br>
                    Email: nfabiola@sergenbol.co
                </td>
            </tr>
        </table>
    </div>

    <div class="titulo-recibo">{{ $titulo }} N° {{ $registro->codigo }}</div>

    <table class="info">
        <tr>
            <td><strong>Responsable:</strong> {{ $registro->persona->nombre }}</td>
            <td align="right"><strong>Fecha:</strong> {{ $registro->fecha->format('d/m/Y') }}</td>
        </tr>
        @if ($registro->proyecto)
            <tr>
                <td colspan="2"><strong>Proyecto:</strong> {{ $registro->proyecto->codigo }} -
                    {{ $registro->proyecto->descripcion }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="2"><strong>Tipo de Recibo:</strong> {{ strtoupper($tipo) }}</td>
        </tr>
    </table>

    <table class="detalle">
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th>Descripción</th>
                <th width="10%">Cantidad</th>
                <th width="15%">Costo Unit.</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registro->detalles as $d)
                <tr>
                    <td>{{ $d->item->codigo }}</td>
                    <td>{{ $d->item->descripcion }}</td>
                    <td align="center">{{ $d->cantidad_prestada }}</td>
                    <td align="right">Bs {{ number_format($d->costo_unitario_recibo, 2) }}</td>
                    <td align="right">Bs {{ number_format($d->subtotal_recibo, 2) }}</td>
                </tr>
            @endforeach

            @foreach ($registro->kits as $kit)
                <tr style="background:#f0f0f0; font-weight:bold;">
                    <td>{{ $kit->codigo }}</td>
                    <td colspan="3">KIT: {{ $kit->nombre }}</td>
                    <td align="right">Bs {{ number_format($kit->total_kit_recibo, 2) }}</td>
                </tr>
                @foreach ($kit->items as $item)
                    <tr>
                        <td style="color: #666; font-size: 9px;">{{ $item->codigo }}</td>
                        <td style="padding-left: 10px;">{{ $item->descripcion }}</td>
                        <td align="center">{{ $item->pivot->cantidad }}</td>
                        <td align="right">Bs {{ number_format($item->costo_unitario, 2) }}</td>
                        <td align="right">Bs {{ number_format($item->subtotal_recibo, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right" style="font-weight:bold; padding-top:10px;">TOTAL GENERAL:</td>
                <td align="right" style="font-weight:bold; padding-top:10px; border-top: 1px solid #000;">
                    Bs {{ number_format($registro->monto_total, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="firmas" style="margin-top: 100px;">
        <table width="100%">
            <tr>
                <td width="50%">
                    <div class="linea"></div>
                    <strong>ENTREGADO POR</strong><br>Almacén Ser.Gen
                </td>
                <td width="50%">
                    <div class="linea"></div>
                    <strong>RECIBIDO POR</strong><br>{{ $registro->persona->nombre }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento generado por el Sistema de Gestión de Inventarios – Ser.Gen
    </div>

</body>

</html>
