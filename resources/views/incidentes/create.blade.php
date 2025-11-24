@extends('adminlte::page')

@section('title', 'Nuevo Incidente')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Registrar Nuevo Incidente</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="fas fa-exclamation-triangle"></i> Errores en el formulario:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('incidentes.store') }}">
                @csrf

                <!-- Información General -->
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información General del Incidente
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">
                                        <i class="fas fa-tag"></i> Tipo de Incidente
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="tipo"
                                            id="tipo"
                                            class="form-control @error('tipo') is-invalid @enderror"
                                            required>
                                        <option value="">— Seleccionar tipo —</option>
                                        <option value="PRESTAMO" @selected(old('tipo') === 'PRESTAMO')>
                                            Préstamo
                                        </option>
                                        <option value="DOTACION" @selected(old('tipo') === 'DOTACION')>
                                            Dotación
                                        </option>
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="persona_id">
                                        <i class="fas fa-user"></i> Persona Involucrada
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="persona_id"
                                            id="persona_id"
                                            class="form-control @error('persona_id') is-invalid @enderror"
                                            required>
                                        <option value="">— Seleccionar persona —</option>
                                        @foreach($personas as $p)
                                            <option value="{{ $p->id }}" @selected(old('persona_id') == $p->id)>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_incidente">
                                        <i class="fas fa-calendar"></i> Fecha del Incidente
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           name="fecha_incidente"
                                           id="fecha_incidente"
                                           class="form-control @error('fecha_incidente') is-invalid @enderror"
                                           value="{{ old('fecha_incidente', date('Y-m-d')) }}"
                                           required>
                                    @error('fecha_incidente')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="relacion_id">
                                        <i class="fas fa-link"></i> Relacionado con (Opcional)
                                    </label>
                                    <select name="relacion_id"
                                            id="relacion_id"
                                            class="form-control">
                                        <option value="">— No relacionar —</option>

                                        <optgroup label="Préstamos" id="groupPrestamos" style="display:none;">
                                            @foreach($prestamos as $pr)
                                            <option value="{{ $pr->id }}">
                                                Préstamo #{{ $pr->id }} — {{ $pr->persona->nombre ?? '—' }}
                                            </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Dotaciones" id="groupDotaciones" style="display:none;">
                                            @foreach($dotaciones as $dt)
                                            <option value="{{ $dt->id }}">
                                                Dotación #{{ $dt->id }} — {{ $dt->persona->nombre ?? '—' }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">
                                <i class="fas fa-file-alt"></i> Descripción del Incidente
                            </label>
                            <textarea name="descripcion"
                                      id="descripcion"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Detalle lo sucedido...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ítems Afectados -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i> Ítems Afectados
                        </h3>
                        <div class="card-tools">
                            <button type="button"
                                    id="add-item"
                                    class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Agregar Ítem
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-warning">
                            <h5><i class="fas fa-info-circle"></i> Importante:</h5>
                            <p class="mb-0">Agregue todos los ítems afectados por este incidente. Debe agregar al menos un ítem.</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabla-items">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="40%">Ítem</th>
                                        <th width="15%">Cantidad</th>
                                        <th width="20%">Estado</th>
                                        <th width="20%">Observación</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no-items-row">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No hay ítems agregados. Haga clic en "Agregar Ítem"</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Registrar Incidente
                        </button>
                        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    .callout {
        border-left-width: 5px;
    }
</style>
@stop

@section('js')
<script>
$(function() {
    let index = 0;

    // Cambiar grupo de relaciones según tipo
    $('#tipo').on('change', function() {
        const tipo = $(this).val();
        $('#groupPrestamos').hide();
        $('#groupDotaciones').hide();
        $('#relacion_id').val('');

        if (tipo === 'PRESTAMO') {
            $('#groupPrestamos').show();
        } else if (tipo === 'DOTACION') {
            $('#groupDotaciones').show();
        }
    });

    // Agregar ítem
    $('#add-item').on('click', function() {
        // Ocultar mensaje de "sin ítems"
        $('#no-items-row').remove();

        const fila = `
            <tr>
                <td>
                    <select name="items[${index}][item_id]" class="form-control" required>
                        <option value="">— Seleccionar ítem —</option>
                        @foreach($items as $it)
                        <option value="{{ $it->id }}">{{ $it->codigo }} — {{ $it->descripcion }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number"
                           min="1"
                           name="items[${index}][cantidad]"
                           class="form-control"
                           placeholder="0"
                           required>
                </td>
                <td>
                    <select name="items[${index}][estado_item]" class="form-control" required>
                        <option value="PERDIDO">PERDIDO</option>
                        <option value="DANADO">DAÑADO</option>
                        <option value="NO_DEVUELTO">NO DEVUELTO</option>
                        <option value="BAJA">BAJA</option>
                        <option value="OTRO">OTRO</option>
                    </select>
                </td>
                <td>
                    <input type="text"
                           name="items[${index}][observacion]"
                           class="form-control"
                           placeholder="Opcional">
                </td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-danger btn-sm delete-row"
                            data-toggle="tooltip"
                            title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#tabla-items tbody').append(fila);
        index++;

        // Inicializar tooltips en la nueva fila
        $('[data-toggle="tooltip"]').tooltip();

        // Event listener para eliminar
        $('.delete-row').off('click').on('click', function() {
            $(this).closest('tr').remove();

            // Si no quedan filas, mostrar mensaje
            if ($('#tabla-items tbody tr').length === 0) {
                $('#tabla-items tbody').html(`
                    <tr id="no-items-row">
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No hay ítems agregados. Haga clic en "Agregar Ítem"</p>
                        </td>
                    </tr>
                `);
            }
        });
    });

    // Triggerear cambio inicial si hay valor
    $('#tipo').trigger('change');
});
</script>
@stop
