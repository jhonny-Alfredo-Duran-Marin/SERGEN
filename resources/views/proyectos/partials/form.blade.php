<!-- Información Básica -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i> Información Básica del Proyecto
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
                        value="{{ old('codigo', $proyecto->codigo ?? '') }}" placeholder="Ej: PROJ-2024-001" required>
                    @error('codigo')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-9">
                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i> Descripción
                        <span class="text-danger">*</span>
                    </label>
                    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                        rows="2" placeholder="Describe brevemente el proyecto" required>{{ old('descripcion', $proyecto->descripcion ?? '') }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información de Cliente -->
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-building"></i> Información del Cliente
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="empresa">
                        <i class="fas fa-building"></i> Empresa
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="empresa" id="empresa"
                        class="form-control @error('empresa') is-invalid @enderror"
                        value="{{ old('empresa', $proyecto->empresa ?? '') }}" placeholder="Nombre de la empresa"
                        required>
                    @error('empresa')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="orden_compra">
                        <i class="fas fa-file-contract"></i> Orden de Compra
                    </label>
                    <input type="text" name="orden_compra" id="orden_compra"
                        class="form-control @error('orden_compra') is-invalid @enderror"
                        value="{{ old('orden_compra', $proyecto->orden_compra ?? '') }}"
                        placeholder="Número de OC (opcional)">
                    @error('orden_compra')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="sitio">
                        <i class="fas fa-map-marker-alt"></i> Sitio
                    </label>
                    <input type="text" name="sitio" id="sitio"
                        class="form-control @error('sitio') is-invalid @enderror"
                        value="{{ old('sitio', $proyecto->sitio ?? '') }}"
                        placeholder="Ubicación del proyecto (opcional)">
                    @error('sitio')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="estado">
                        <i class="fas fa-toggle-on"></i> Persona
                        <span class="text-danger">*</span>
                    </label>
                    <select name="persona_id" class="form-control">
                        <option value="">— Seleccionar —</option>
                        @foreach ($personas as $per)
                            <option value="{{ $per->id }}" @selected((int) old('persona_id', $proyecto->persona_id ?? 0) === $per->id)>{{ $per->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('persona_id')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Información Financiera y Estado -->
<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-dollar-sign"></i> Información Financiera y Estado
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="monto">
                        <i class="fas fa-dollar-sign"></i> Monto
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" name="monto" id="monto"
                            class="form-control @error('monto') is-invalid @enderror"
                            value="{{ old('monto', $proyecto->monto ?? '0.00') }}" step="0.01" min="0"
                            placeholder="0.00" required>
                    </div>
                    @error('monto')
                        <span class="invalid-feedback d-block">
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
                        <option value="Abierto" @selected(old('estado', $proyecto->estado ?? 'Abierto') === 'Abierto')>
                            Abierto
                        </option>
                        <option value="Cerrado" @selected(old('estado', $proyecto->estado ?? '') === 'Cerrado')>
                            Cerrado
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
                    <label>&nbsp;</label>
                    <div class="custom-control custom-switch" style="padding-top: 8px;">
                        <input type="checkbox" name="es_facturado" value="1" class="custom-control-input"
                            id="es_facturado"
                            {{ old('es_facturado', $proyecto->es_facturado ?? false ? '1' : '') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="es_facturado">
                            <i class="fas fa-file-invoice"></i> ¿Proyecto Facturado?
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cronograma -->
<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt"></i> Cronograma del Proyecto
        </h3>
    </div>
    <div class="card-body">
        <div class="callout callout-info">
            <h5><i class="fas fa-info-circle"></i> Sobre las fechas:</h5>
            <p class="mb-0">Define el período de ejecución del proyecto. Ambas fechas son opcionales pero
                recomendadas para un mejor seguimiento.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_inicio">
                        <i class="fas fa-calendar-alt text-success"></i> Fecha de Inicio
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            class="form-control @error('fecha_inicio') is-invalid @enderror"
                            value="{{ old('fecha_inicio', optional($proyecto->fecha_inicio ?? null)->format('Y-m-d')) }}">
                    </div>
                    @error('fecha_inicio')
                        <span class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_fin">
                        <i class="fas fa-calendar-check text-danger"></i> Fecha de Fin
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                            class="form-control @error('fecha_fin') is-invalid @enderror"
                            value="{{ old('fecha_fin', optional($proyecto->fecha_fin ?? null)->format('Y-m-d')) }}">
                    </div>
                    @error('fecha_fin')
                        <span class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div id="duracion-info" class="alert alert-info" style="display: none;">
            <i class="fas fa-clock"></i>
            <strong>Duración estimada:</strong> <span id="duracion-dias"></span> días
        </div>
    </div>
</div>

<!-- Botones de acción -->
<div class="card">
    <div class="card-body">
        <button type="submit" class="btn btn-{{ ($mode ?? 'create') === 'create' ? 'success' : 'warning' }}">
            <i class="fas fa-save"></i>
            {{ ($mode ?? 'create') === 'create' ? 'Guardar Proyecto' : 'Actualizar Proyecto' }}
        </button>
        <a href="{{ route('proyectos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>
</div>

@push('js')
    <script>
        $(function() {
            // Calcular duración del proyecto
            function calcularDuracion() {
                const fechaInicio = $('#fecha_inicio').val();
                const fechaFin = $('#fecha_fin').val();

                if (fechaInicio && fechaFin) {
                    const inicio = new Date(fechaInicio);
                    const fin = new Date(fechaFin);
                    const diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));

                    if (diferencia >= 0) {
                        $('#duracion-dias').text(diferencia);
                        $('#duracion-info').slideDown();
                    } else {
                        $('#duracion-info').slideUp();
                    }
                } else {
                    $('#duracion-info').slideUp();
                }
            }

            $('#fecha_inicio, #fecha_fin').on('change', calcularDuracion);

            // Calcular al cargar si hay fechas
            calcularDuracion();

            // Validar que fecha fin no sea menor a fecha inicio
            $('#fecha_fin').on('change', function() {
                const fechaInicio = $('#fecha_inicio').val();
                const fechaFin = $(this).val();

                if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                    alert('La fecha de fin no puede ser anterior a la fecha de inicio');
                    $(this).val('');
                }
            });
        });
    </script>
@endpush
