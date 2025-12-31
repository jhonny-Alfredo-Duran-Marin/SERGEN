@csrf

<div class="row">
    <div class="col-md-6">
        <x-adminlte-input name="descripcion" label="Descripción de la Compra"
            placeholder="Ej: 2 Martillos, Ferretería Don Pepito"
            value="{{ old('descripcion', $compra->descripcion ?? '') }}" required />
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="fecha_compra" type="date" label="Fecha de Compra"
            value="{{ old('fecha_compra', $compra->fecha_compra ?? now()->format('Y-m-d')) }}" required />
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <x-adminlte-input name="costo_total" type="number" label="Costo Total ($)"
            placeholder="Ej: 50.00"
            value="{{ old('costo_total', $compra->costo_total ?? '') }}"
            min="0" step="0.01" required />
    </div>

    <div class="col-md-4">
        <x-adminlte-input name="cantidad" type="number" label="Cantidad Comprada"
            placeholder="Ej: 2"
            value="{{ old('cantidad', $compra->cantidad ?? 1) }}"
            min="1" step="1" required />
    </div>

    <div class="col-md-4">
        <x-adminlte-select name="tipo_compra" label="Tipo de Compra" required>
            @php $tipo = old('tipo_compra', $compra->tipo_compra ?? 'Material'); @endphp
            <option value="Material" {{ $tipo == 'Material' ? 'selected' : '' }}>Material (Genera Alerta)</option>
            <option value="Herramienta" {{ $tipo == 'Herramienta' ? 'selected' : '' }}>Herramienta (Genera Alerta)</option>
            <option value="Insumos" {{ $tipo == 'Insumos' ? 'selected' : '' }}>Insumos (No Alerta)</option>
            <option value="Otros" {{ $tipo == 'Otros' ? 'selected' : '' }}>Otros (No Alerta)</option>
        </x-adminlte-select>
    </div>
</div>

@isset($compra)
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-select name="estado_procesamiento" label="Estado (Alerta)" required>
                @php $estado = old('estado_procesamiento', $compra->estado_procesamiento ?? 'Pendiente'); @endphp
                <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="Resuelto" {{ $estado == 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
            </x-adminlte-select>
        </div>
    </div>
@endisset

{{-- SECCIÓN DE IMÁGENES: COMPRA Y QR --}}
<div class="row">
    {{-- Columna para Imagen Principal --}}
    <div class="col-md-6">
        <x-adminlte-input-file name="imagen" id="imagen-input" label="Imagen de la compra" accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-primary"><i class="fas fa-image"></i></div>
            </x-slot>
        </x-adminlte-input-file>

        {{-- Preview Imagen Principal --}}
        <div id="preview-container" class="mt-2" style="display: none;">
            <img id="preview-img" src="" class="img-thumbnail" style="max-height: 120px;">
        </div>

        {{-- Contenedor de imagen actual con ID para ocultar --}}
        @if(isset($compra) && $compra->imagen)
            <div class="mt-2" id="imagen-antigua-contenedor">
                <small class="text-muted d-block">Imagen actual:</small>
                <img src="{{ asset('storage/' . $compra->imagen) }}" class="img-thumbnail" style="max-height:100px;">
            </div>
        @endif
    </div>

    {{-- Columna para Imagen QR --}}
    <div class="col-md-6">
        <x-adminlte-input-file name="qr" id="qr-input" label="Imagen Código QR (Opcional)" accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-purple text-white" style="background-color: #6f42c1 !important;">
                    <i class="fas fa-qrcode"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>

        {{-- Preview QR --}}
        <div id="qr-preview-container" class="mt-2" style="display: none;">
            <img id="qr-preview-img" src="" class="img-thumbnail" style="max-height: 120px;">
        </div>

        {{-- Contenedor de QR actual con ID para ocultar --}}
        @if(isset($compra) && $compra->qr)
            <div class="mt-2" id="qr-antiguo-contenedor">
                <small class="text-muted d-block">QR actual:</small>
                <img src="{{ asset('storage/' . $compra->qr) }}" class="img-thumbnail" style="max-height:100px;">
            </div>
        @endif
    </div>
</div>

<div class="text-right mt-4">
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Registro
    </button>
</div>

@section('js')
<script>
    // Script para Preview de Imagen Principal
    document.getElementById('imagen-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';

            // Ocultamos la imagen vieja si existe
            const antigua = document.getElementById('imagen-antigua-contenedor');
            if(antigua) antigua.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    // Script para Preview de QR
    document.getElementById('qr-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qr-preview-img').src = e.target.result;
            document.getElementById('qr-preview-container').style.display = 'block';

            // Ocultamos el QR viejo si existe
            const qrAntiguo = document.getElementById('qr-antiguo-contenedor');
            if(qrAntiguo) qrAntiguo.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
