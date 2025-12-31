<!-- Información Principal -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i> Información Principal del Item
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="codigo">
                        <i class="fas fa-barcode"></i> Código
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="codigo" id="codigo"
                        class="form-control @error('codigo') is-invalid @enderror"
                        value="{{ old('codigo', $codigoAutogenerado ?? ($item->codigo ?? '')) }}" required>
                    @error('codigo')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-tag"></i> Descripción
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="descripcion" id="descripcion"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        value="{{ old('descripcion', $item->descripcion ?? '') }}"
                        placeholder="Nombre descriptivo del item" required>
                    @error('descripcion')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="fabricante">
                        <i class="fas fa-industry"></i> Fabricante
                    </label>
                    <input type="text" name="fabricante" id="fabricante"
                        class="form-control @error('fabricante') is-invalid @enderror"
                        value="{{ old('fabricante', $item->fabricante ?? '') }}"
                        placeholder="Marca o fabricante (opcional)">
                    @error('fabricante')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clasificación -->
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sitemap"></i> Clasificación y Medidas
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="categoria_id">
                        <i class="fas fa-sitemap"></i> Categoría
                        <span class="text-danger">*</span>
                    </label>
                    <select name="categoria_id" id="categoria_id"
                        class="form-control @error('categoria_id') is-invalid @enderror" required>
                        <option value="">— Seleccionar categoría —</option>
                        @foreach ($categorias as $c)
                            <option value="{{ $c->id }}" @selected((int) old('categoria_id', $item->categoria_id ?? 0) === (int) $c->id)>
                                {{ $c->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="area_id">
                        <i class="fas fa-sitemap"></i> Area
                        <span class="text-danger">*</span>
                    </label>
                    <select name="area_id" id="area_id" class="form-control @error('area_id') is-invalid @enderror"
                        required>
                        <option value="">— Seleccionar area —</option>
                        @foreach ($areas as $c)
                            <option value="{{ $c->id }}" @selected((int) old('area_id', $item->area_id ?? 0) === (int) $c->id)>
                                {{ $c->descripcion . ' - (' . ($c->sucursal->descripcion ?? 'N/A') . ')' }}
                            </option>
                        @endforeach
                    </select>
                    @error('area_id')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="medida_id">
                        <i class="fas fa-ruler"></i> Unidad de Medida
                        <span class="text-danger">*</span>
                    </label>
                    <select name="medida_id" id="medida_id"
                        class="form-control @error('medida_id') is-invalid @enderror" required>
                        <option value="">— Seleccionar medida —</option>
                        @foreach ($medidas as $m)
                            <option value="{{ $m->id }}" @selected((int) old('medida_id', $item->medida_id ?? 0) === (int) $m->id)>
                                {{ $m->descripcion }} ({{ $m->simbolo }})
                            </option>
                        @endforeach
                    </select>
                    @error('medida_id')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="ubicacion">
                        <i class="fas fa-map-marker-alt"></i> Ubicación
                    </label>
                    <input type="text" name="ubicacion" id="ubicacion"
                        class="form-control @error('ubicacion') is-invalid @enderror"
                        value="{{ old('ubicacion', $item->ubicacion ?? '') }}" placeholder="Ej: Almacén A, Estante 3">
                    @error('ubicacion')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inventario y Costos -->
<!-- Inventario y Costos -->
<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-boxes"></i> Inventario y Costos
        </h3>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="cantidad">
                        <i class="fas fa-boxes"></i> Cantidad en Stock
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad"
                        class="form-control @error('cantidad') is-invalid @enderror"
                        value="{{ old('cantidad', $item->cantidad ?? 0) }}" min="0" step="1" required>
                    <small class="form-text text-muted">Cantidad disponible en inventario</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="piezas">
                        <i class="fas fa-cubes"></i> Piezas por Unidad
                    </label>
                    <input type="number" name="piezas" id="piezas"
                        class="form-control @error('piezas') is-invalid @enderror"
                        value="{{ old('piezas', $item->piezas ?? 0) }}" min="0" step="1">
                    <small class="form-text text-muted">Número de piezas por unidad</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="costo_unitario">
                        <i class="fas fa-dollar-sign"></i> Costo Unitario
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" name="costo_unitario" id="costo_unitario"
                            class="form-control @error('costo_unitario') is-invalid @enderror"
                            value="{{ old('costo_unitario', $item->costo_unitario ?? '0.00') }}" step="0.01"
                            min="0" required>
                    </div>
                </div>
            </div>

            <!-- DESCUENTO EN MONTO (Bs.) -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="descuento">
                        <i class="fas fa-minus-circle"></i> Descuento (Bs.)
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" name="descuento" id="descuento"
                            class="form-control @error('descuento') is-invalid @enderror"
                            value="{{ old('descuento', $item->descuento ?? 0) }}" min="0" step="0.01">
                    </div>
                    <small class="form-text text-muted">Monto que se resta al total</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>
                        <i class="fas fa-calculator"></i> Valor Total Inventario
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="text" id="valor_total" class="form-control bg-light" value="0.00"
                            readonly>
                    </div>
                    <small class="form-text text-muted">Calculado automáticamente</small>
                </div>
            </div>

        </div>

        <div id="stock-alert" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Advertencia:</strong> Este item tiene stock bajo (≤3 unidades)
        </div>
    </div>
</div>



<!-- Clasificación y Estado -->
<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags"></i> Clasificación y Estado
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo">
                        <i class="fas fa-tags"></i> Tipo de Item
                        <span class="text-danger">*</span>
                    </label>
                    <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror"
                        required>
                        <option value="Herramienta" @selected(old('tipo', $item->tipo ?? '') === 'Herramienta')>
                            <i class="fas fa-tools"></i> Herramienta
                        </option>
                        <option value="Material" @selected(old('tipo', $item->tipo ?? '') === 'Material')>
                            <i class="fas fa-box"></i> Material
                        </option>
                    </select>
                    @error('tipo')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="estado">
                        <i class="fas fa-toggle-on"></i> Estado
                        <span class="text-danger">*</span>
                    </label>
                    <select name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror"
                        required>
                        <option value="Activo" @selected(old('estado', $item->estado ?? 'Activo') === 'Activo')>
                            Activo
                        </option>

                        <option value="Disponible" @selected(old('estado', $item->estado ?? 'Disponible') === 'Disponible')>
                            Disponible
                        </option>
                        <option value="Prestado" @selected(old('estado', $item->estado ?? 'Prestado') === 'Prestado')>
                            Prestado
                        </option>
                        <option value="Dotado" @selected(old('estado', $item->estado ?? 'Dotado') === 'Dotado')>
                            Dotado
                        </option>
                        <option value="Observacion" @selected(old('estado', $item->estado ?? 'Observacion') === 'Observacion')>
                            Observacion
                        </option>
                         <option value="Reservado" @selected(old('estado', $item->estado ?? 'Reservado') === 'Reservado')>
                            Reservado
                        </option>
                        <option value="Baja" @selected(old('estado', $item->estado ?? 'Baja') === 'Baja')>
                            Baja
                        </option>
                        <option value="Pasivo" @selected(old('estado', $item->estado ?? '') === 'Pasivo')>
                            Pasivo
                        </option>
                    </select>
                    @error('estado')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_registro">
                        <i class="fas fa-calendar-alt"></i> Fecha de Registro
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="date" name="fecha_registro" id="fecha_registro"
                            class="form-control @error('fecha_registro') is-invalid @enderror"
                            value="{{ old('fecha_registro', optional($item->fecha_registro ?? null)->format('Y-m-d')) }}">
                    </div>
                    @error('fecha_registro')
                        <span class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Imagen -->
<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-image"></i> Imagen del Item
        </h3>
    </div>
    <div class="card-body">
        <div class="callout callout-info">
            <h5><i class="fas fa-info-circle"></i> Sobre la imagen:</h5>
            <p class="mb-0">Sube una imagen del item para facilitar su identificación. Formatos aceptados: JPG, PNG,
                WEBP. Tamaño máximo recomendado: 2MB.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="imagen">
                        <i class="fas fa-upload"></i> Seleccionar Imagen
                    </label>
                    <div class="custom-file">
                        <input type="file" name="imagen" id="imagen"
                            class="custom-file-input @error('imagen') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/webp">
                        <label class="custom-file-label" for="imagen">Elegir archivo...</label>
                    </div>
                    @error('imagen')
                        <span class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            @if (!empty($item?->imagen_path))
                <div class="col-md-6">
                    <label class="d-block">Vista Previa Actual</label>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $item->thumb_url ?? $item->imagen_url }}" alt="Imagen actual"
                            class="img-thumbnail" style="max-height:120px;">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remove_imagen" value="1" id="remove-imagen"
                                class="custom-control-input">
                            <label for="remove-imagen" class="custom-control-label">
                                <i class="fas fa-trash"></i> Eliminar imagen actual
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Vista previa de nueva imagen -->
        <div id="preview-container" class="mt-3" style="display: none;">
            <label class="d-block">Vista Previa Nueva Imagen:</label>
            <img id="preview-image" src="" alt="Vista previa" class="img-thumbnail"
                style="max-height: 200px;">
        </div>
    </div>
</div>

<!-- Botones de acción -->
<div class="card">
    <div class="card-body">
        <button type="submit" class="btn btn-{{ ($mode ?? 'create') === 'create' ? 'success' : 'warning' }}">
            <i class="fas fa-save"></i> {{ ($mode ?? 'create') === 'create' ? 'Guardar Item' : 'Actualizar Item' }}
        </button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>
</div>

@push('js')
    <script>
        $(function() {

            function calcularValorTotal() {
                const cantidad = parseFloat($('#cantidad').val()) || 0;
                const costo = parseFloat($('#costo_unitario').val()) || 0;
                const descuento = parseFloat($('#descuento').val()) || 0;

                let total = cantidad * costo;

                // Descuento en monto (Bs.)
                if (descuento > 0) {
                    total = total - descuento;
                }

                if (total < 0) total = 0; // evitar negativos

                $('#valor_total').val(total.toFixed(2));
            }

            $('#cantidad, #costo_unitario, #descuento').on('input', calcularValorTotal);
            calcularValorTotal();

            // stock bajo
            $('#cantidad').on('input', function() {
                const cantidad = parseInt($(this).val()) || 0;
                if (cantidad <= 3 && cantidad >= 0) {
                    $('#stock-alert').slideDown();
                } else {
                    $('#stock-alert').slideUp();
                }
            }).trigger('input');

        });
    </script>
@endpush

@push('css')
    <style>
        .callout {
            border-left-width: 5px;
        }

        .gap-3 {
            gap: 1rem;
        }
    </style>
@endpush
