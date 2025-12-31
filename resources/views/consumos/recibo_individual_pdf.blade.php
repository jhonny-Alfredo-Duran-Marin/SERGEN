<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Consumo #{{ $registro->id }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { width: 100%; border-bottom: 2px solid #001f3f; padding-bottom: 10px; margin-bottom: 20px; }
        .empresa-datos { text-align: right; font-size: 10px; }
        .titulo { text-align: center; font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 20px 0; color: #001f3f; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #001f3f; color: white; padding: 10px; text-align: left; text-transform: uppercase; }
        .items-table td { border: 1px solid #ddd; padding: 10px; }
        .total-box { float: right; width: 250px; background: #f8f9fa; border: 1px solid #001f3f; padding: 10px; margin-top: 10px; }
        .firmas { margin-top: 80px; width: 100%; }
        .linea { border-top: 1px solid #333; width: 200px; margin: 0 auto 5px auto; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="50%"><img src="{{ $logoBase64 }}" style="width:150px"></td>
            <td class="empresa-datos">
                <strong>SER.GEN TELECOMUNICACIÓN & CONSTRUCCIÓN</strong><br>
                Santa Cruz de la Sierra – Bolivia<br>
                Tel: +591 69201292 | Email: nfabiola@sergenbol.co
            </td>
        </tr>
    </table>

    <div class="titulo">{{ $titulo }} N° {{ str_pad($registro->id, 5, '0', STR_PAD_LEFT) }}</div>

    <table class="info-table">
        <tr>
            <td><strong>RESPONSABLE:</strong> {{ $registro->persona->nombre }}</td>
            <td align="right"><strong>FECHA:</strong> {{ $registro->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>PROYECTO:</strong> {{ $registro->proyecto->descripcion ?? 'GASTO GENERAL' }}</td>
            <td align="right"><strong>TIPO:</strong> CONSUMO DIRECTO</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="15%">CÓDIGO</th>
                <th>DESCRIPCIÓN DEL MATERIAL</th>
                <th width="15%" style="text-align: center;">CANT.</th>
                <th width="20%" style="text-align: right;">UNITARIO</th>
                <th width="20%" style="text-align: right;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $registro->item->codigo }}</td>
                <td>{{ $registro->item->descripcion }}</td>
                <td align="center">{{ $registro->cantidad_consumida }}</td>
                <td align="right">Bs. {{ number_format($registro->precio_unitario, 2) }}</td>
                <td align="right">Bs. {{ number_format($subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <table width="100%">
            <tr>
                <td><strong>TOTAL RECIBIDO:</strong></td>
                <td align="right" style="font-size: 16px; color: #d9534f;"><strong>Bs. {{ number_format($subtotal, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <table class="firmas">
        <tr>
            <td width="50%" align="center">
                <div class="linea"></div>
                <strong>ENTREGADO POR</strong><br>Almacén Central Ser.Gen
            </td>
            <td width="50%" align="center">
                <div class="linea"></div>
                <strong>RECIBIDO POR</strong><br>{{ $registro->persona->nombre }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Documento oficial de control interno Ser.Gen - Prohibida su reproducción parcial.
    </div>
</body>
</html>
