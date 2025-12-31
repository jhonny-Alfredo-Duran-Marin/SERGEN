<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="50%"><img src="{{ $logoBase64 }}" style="width:140px"></td>
                <td width="50%" class="empresa-datos">
                    <strong>Ser.Gen Telecomunicación & Construcción</strong><br>
                    Santa Cruz – Bolivia | Tel: +591 69201292
                </td>
            </tr>
        </table>
    </div>

    <div class="titulo-recibo text-danger">{{ $titulo }}</div>

    <table class="info">
        <tr>
            <td><strong>Responsable:</strong> {{ $registro->persona->nombre }}</td>
            <td align="right"><strong>Fecha Proceso:</strong> {{ $registro->fecha->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Incidente Referencia:</strong> {{ $registro->codigo }}</td>
        </tr>
    </table>

    <table class="detalle">
        <thead>
            <tr>
                <th width="20%">Código</th>
                <th>Descripción Item</th>
                <th width="15%">Tipo Orig.</th>
                <th width="15%">Resultado</th>
                <th width="10%">Cant.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">{{ $registro->item_codigo }}</td>
                <td>{{ $registro->descripcion }}</td>
                <td align="center">{{ strtoupper($registro->tipo) }}</td>
                <td align="center"><strong>{{ $registro->resultado }}</strong></td>
                <td align="center">{{ $registro->cantidad }}</td>
            </tr>
        </tbody>
    </table>

    <div class="firmas" style="margin-top: 120px;">
        <table width="100%">
            <tr>
                <td width="50%">
                    <div class="linea"></div>
                    <strong>ENTREGADO POR</strong><br>{{ $registro->persona->nombre }}
                </td>
                <td width="50%">
                    <div class="linea"></div>
                    <strong>RECIBIDO POR (ALMACÉN)</strong><br>Firma y Sello
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento certifica la recepción parcial/total de los ítems afectados en el incidente {{ $registro->codigo }}.
    </div>
</body>
