<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Consumos</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        /* Cabecera Estándar Ser.Gen */
        .header { width: 100%; border-bottom: 2px solid #001f3f; padding-bottom: 10px; margin-bottom: 20px; }
        .empresa-datos { text-align: right; font-size: 10px; line-height: 1.3; }

        .titulo { text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0; color: #001f3f; text-transform: uppercase; }

        /* Tabla de Datos */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #001f3f; color: white; padding: 8px; text-align: left; border: 1px solid #001f3f; }
        td { padding: 7px; border: 1px solid #ccc; vertical-align: middle; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-gray { background-color: #f4f4f4; }
        .font-bold { font-weight: bold; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="30%" style="border:none;"><img src="{{ $logoBase64 }}" style="width:140px"></td>
                <td width="70%" class="empresa-datos" style="border:none;">
                    <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                    Santa Cruz de la Sierra – Bolivia | Tel: +591 69201292<br>
                    Email: nfabiola@sergenbol.co | Reporte generado el: {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="titulo">{{ $titulo }}</div>

    <table>
        <thead>
            <tr>
                <th width="80">FECHA</th>
                <th width="100">CÓDIGO</th>
                <th>DESCRIPCIÓN DEL MATERIAL</th>
                <th>PROYECTO DESTINO</th>
                <th>RESPONSABLE</th>
                <th width="60" class="text-center">CANT.</th>
                <th width="90" class="text-right">P. UNITARIO</th>
                <th width="100" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consumos as $c)
            <tr>
                <td class="text-center">{{ $c->created_at->format('d/m/Y') }}</td>
                <td class="font-bold">{{ $c->item->codigo }}</td>
                <td>{{ $c->item->descripcion }}</td>
                <td>{{ $c->proyecto->descripcion ?? 'GASTO GENERAL' }}</td>
                <td>{{ $c->persona->nombre ?? 'S/N' }}</td>
                <td class="text-center">{{ $c->cantidad_consumida }}</td>
                <td class="text-right">Bs. {{ number_format($c->precio_unitario, 2) }}</td>
                <td class="text-right font-bold">Bs. {{ number_format($c->cantidad_consumida * $c->precio_unitario, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">No se encontraron registros de consumo.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray font-bold" style="font-size: 13px;">
                <td colspan="7" class="text-right">INVERSIÓN TOTAL ACUMULADA:</td>
                <td class="text-right text-danger">Bs. {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Este documento es un reporte consolidado generado por el Sistema de Inventarios Ser.Gen.
        Página 1 de 1
    </div>
</body>
</html>
