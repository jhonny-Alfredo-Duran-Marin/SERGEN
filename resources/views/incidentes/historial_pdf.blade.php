<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; }
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; }
        .titulo { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0; text-transform: uppercase; }
        .info-table { width: 100%; border: 1px solid #ddd; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 5px; border: 1px solid #ddd; }
        .label { background: #f4f6f9; font-weight: bold; width: 20%; }
        .section-header { background: #343a40; color: white; padding: 5px; font-weight: bold; margin-top: 10px; }
        .detalle-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .detalle-table th { background: #e9ecef; border: 1px solid #dee2e6; padding: 5px; }
        .detalle-table td { border: 1px solid #dee2e6; padding: 5px; text-align: center; }
        .resumen-table { width: 100%; border: 2px solid #000; margin-top: 20px; border-collapse: collapse; }
        .resumen-table td { padding: 8px; border: 1px solid #000; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="50%"><img src="{{ $logoBase64 }}" width="120"></td>
            <td width="50%" align="right">
                <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                Santa Cruz de la Sierra - Bolivia<br>
                Tel: +591 69201292
            </td>
        </tr>
    </table>

    <div class="titulo">{{ $titulo }} {{ $incidente->codigo }}</div>

    <table class="info-table">
        <tr>
            <td class="label">Responsable:</td><td>{{ $incidente->persona->nombre }}</td>
            <td class="label">Estado Incidente:</td><td><strong>{{ $incidente->estado }}</strong></td>
        </tr>
        <tr>
            <td class="label">Fecha Registro:</td><td>{{ \Carbon\Carbon::parse($incidente->fecha_incidente)->format('d/m/Y') }}</td>
            <td class="label">Total Devoluciones:</td><td>{{ $incidente->devoluciones->count() }}</td>
        </tr>
    </table>

    @foreach($incidente->devoluciones->groupBy('created_at') as $fecha => $devoluciones)
    <div class="section-header">DEVOLUCIÓN #{{ $loop->iteration }} <span style="float: right">Registrado por: Almacén</span></div>
    <table class="detalle-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Estado/Tipo</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devoluciones as $dev)
            <tr>
                <td>{{ $dev->item->codigo }}</td>
                <td align="left">{{ $dev->item->descripcion }}</td>
                <td>{{ $dev->cantidad_devuelta }}</td>
                <td>{{ $dev->tipo }}</td>
                <td><strong>{{ $dev->resultado }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <table class="resumen-table">
        <tr>
            <td colspan="2" class="label" align="center">RESUMEN GENERAL DEL INCIDENTE</td>
        </tr>
        <tr>
            <td><strong>Total Items Afectados:</strong> {{ $incidente->items->sum('pivot.cantidad') }}</td>
            <td><strong>Total Items Recuperados (OK):</strong> {{ $incidente->devoluciones->where('resultado', 'DEVUELTO_OK')->sum('cantidad_devuelta') }}</td>
        </tr>
        <tr>
            <td><strong>Estado Final:</strong> {{ $incidente->estado }}</td>
            <td><strong>Saldo Pendiente:</strong> {{ $incidente->items->sum('pivot.cantidad') - $incidente->devoluciones->sum('cantidad_devuelta') }}</td>
        </tr>
    </table>

    <div class="footer">Documento generado el {{ now()->format('d/m/Y H:i') }} | Sistema de Gestión Ser.Gen</div>
</body>
</html>
