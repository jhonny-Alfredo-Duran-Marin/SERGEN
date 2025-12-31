<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 150px; }
        .company-info { text-align: right; font-size: 10px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .items-table th { background-color: #f2f2f2; }
        .footer-signs { width: 100%; margin-top: 100px; text-align: center; }
        .sign-box { width: 45%; display: inline-block; border-top: 1px solid #000; padding-top: 5px; margin: 0 2%; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td><img src="{{ $logoBase64 }}" class="logo"></td>
            <td class="company-info">
                <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                Santa Cruz de la Sierra – Bolivia<br>
                Tel: +591 69201292<br>
                Email: nfabiola@sergenbol.co
            </td>
        </tr>
    </table>

    <div class="title">{{ $titulo }}</div>

    <table class="info-table">
        <tr>
            <td><strong>Responsable:</strong> {{ $registro->persona->nombre }}</td>
            <td align="right"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tipo de Recibo:</strong> DOTACIÓN</td>
            <td align="right"><strong>Estado:</strong> {{ $registro->estado_final }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th width="45%">Descripción / Observación</th>
                <th width="10%">Cantidad</th>
                <th width="15%">Estado Item</th>
                <th width="15%">Próx. Renovación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registro->items as $it)
            <tr>
                <td>{{ $it->item->codigo }}</td>
                <td>{{ $it->item->descripcion }}<br><small>{{ $it->observacion }}</small></td>
                <td align="center">{{ $it->cantidad }}</td>
                <td>{{ $it->estado_item }}</td>
                <td>{{ $it->fecha_siguiente ? \Carbon\Carbon::parse($it->fecha_siguiente)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-signs">
        <div class="sign-box">
            <strong>ENTREGADO POR</strong><br>Almacén Ser.Gen
        </div>
        <div class="sign-box">
            <strong>RECIBIDO POR</strong><br>{{ $registro->persona->nombre }}
        </div>
    </div>
</body>
</html>
