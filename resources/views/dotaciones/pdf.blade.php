<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dotación #{{ $dotacion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px; }
        .title { font-size: 20px; font-weight: bold; text-align: center; margin-bottom: 15px; }
        .header { margin-bottom: 10px; }
        .firma-box { margin-top: 40px; text-align: center; }
    </style>
</head>
<body>

    <div class="title">COMPROBANTE DE DOTACIÓN</div>

    <div class="header">
        <strong>Dotación N°:</strong> {{ $dotacion->id }} <br>
        <strong>Fecha:</strong> {{ $dotacion->fecha }} <br>
        <strong>Persona:</strong> {{ $dotacion->persona->nombre }} <br>
        <strong>Estado:</strong> {{ $dotacion->estado_final }}
    </div>

    <h3>Ítems Entregados</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Estado Item</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dotacion->items as $di)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $di->item->codigo }}</td>
                    <td>{{ $di->item->descripcion }}</td>
                    <td>{{ $di->cantidad }}</td>
                    <td>{{ $di->estado_item }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="firma-box">
        <br><br><br>
        ____________________________ <br>
        Firma del Responsable
    </div>

</body>
</html>
