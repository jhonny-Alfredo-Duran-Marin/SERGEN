{{-- resources/views/compras/_form.blade.php --}}

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
            placeholder="Ej: 50.00" value="{{ old('costo_total', $compra->costo_total ?? '') }}"
            min="0" step="0.01" required />
    </div>
    <div class="col-md-4">
        <x-adminlte-input name="cantidad" type="number" label="Cantidad Comprada"
            placeholder="Ej: 2" value="{{ old('cantidad', $compra->cantidad ?? 1) }}"
            min="1" step="1" required />
    </div>
    <div class="col-md-4">
        <x-adminlte-select name="tipo_compra" label="Tipo de Compra" required>
            @php
                $tipo = old('tipo_compra', $compra->tipo_compra ?? 'Material');
            @endphp
            <option value="Material" {{ $tipo == 'Material' ? 'selected' : '' }}>Material (Genera Alerta)</option>
            <option value="Herramienta" {{ $tipo == 'Herramienta' ? 'selected' : '' }}>Herramienta (Genera Alerta)</option>
            <option value="Insumos" {{ $tipo == 'Insumos' ? 'selected' : '' }}>Insumos (No Alerta)</option>
            <option value="Otros" {{ $tipo == 'Otros' ? 'selected' : '' }}>Otros (No Alerta)</option>
        </x-adminlte-select>
    </div>
</div>

{{-- Solo mostramos el cambio de estado en el formulario de EDITAR --}}
@isset($compra)
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-select name="estado_procesamiento" label="Estado (Alerta)" required>
                @php
                    $estado = old('estado_procesamiento', $compra->estado_procesamiento ?? 'Pendiente');
                @endphp
                <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="Resuelto" {{ $estado == 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
            </x-adminlte-select>
        </div>
    </div>
@endisset

<div class="text-right mt-3">
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Compra
    </button>
</div>
