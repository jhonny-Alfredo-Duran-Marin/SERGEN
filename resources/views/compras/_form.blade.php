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

{{-- Mostrar estado solo al editar --}}
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

{{-- CARGA DE IMAGEN + PREVIEW --}}
<div class="row">
    <div class="col-md-12">

        {{-- INPUT FILE --}}
        <x-adminlte-input-file name="imagen" id="imagen-input" label="Imagen de la compra" accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-primary">
                    <i class="fas fa-image"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>

        {{-- PREVIEW EN TIEMPO REAL --}}
        <div id="preview-container" class="mt-2" style="display: none;">
            <img id="preview-img" src="" class="img-thumbnail" style="max-height: 150px;">
        </div>

        {{-- MOSTRAR IMAGEN ACTUAL (EN EDITAR) --}}
        @if(isset($compra) && $compra->imagen)
            <div class="mt-3">
                <label class="font-weight-bold">Imagen actual:</label>
                <br>
                <img src="{{ asset('storage/' . $compra->imagen) }}"
                     class="img-thumbnail"
                     style="max-height:150px;">
            </div>
        @endif

    </div>
</div>

<div class="text-right mt-3">
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Compra
    </button>
</div>

{{-- JS PARA MOSTRAR VISTA PREVIA DE IMAGEN --}}
@section('js')
<script>
    document.getElementById('imagen-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
</script>
@endsection
