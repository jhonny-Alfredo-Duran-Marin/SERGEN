@extends('adminlte::page')

@section('title', 'Editar Incidente')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Incidente</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Callout informativo -->
            <div class="callout callout-warning">
                <h5><i class="fas fa-info-circle"></i> Editando Incidente:</h5>
                <p class="mb-0">
                    <strong class="text-lg">{{ $incidente->codigo }}</strong> -
                    {{ $incidente->persona->nombre }}
                </p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="fas fa-exclamation-triangle"></i> Errores:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('incidentes.update', $incidente) }}">
                @csrf
                @method('PUT')

                <!-- Información General -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información General
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">
                                        <i class="fas fa-tag"></i> Tipo
                                    </label>
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="PRESTAMO" @selected($incidente->tipo === 'PRESTAMO')>
                                            Préstamo
                                        </option>
                                        <option value="DOTACION" @selected($incidente->tipo === 'DOTACION')>
                                            Dotación
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="persona_id">
                                        <i class="fas fa-user"></i> Persona
                                    </label>
                                    <select name="persona_id" class="form-control" required>
                                        @foreach($personas as $p)
                                        <option value="{{ $p->id }}" @selected($p->id == $incidente->persona_id)>
                                            {{ $p->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_incidente">
                                        <i class="fas fa-calendar"></i> Fecha
                                    </label>
                                    <input type="date"
                                           name="fecha_incidente"
                                           value="{{ $incidente->fecha_incidente }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="relacion_id">
                                <i class="fas fa-link"></i> Relacionado con
                            </label>
                            <select name="relacion_id" id="relacion_id" class="form-control">
                                <option value="">— No relacionar —</option>

                                <optgroup label="Préstamos" id="groupPrestamos">
                                    @foreach($prestamos as $pr)
                                    <option value="{{ $pr->id }}"
                                        @selected($incidente->items->first()?->pivot->prestamo_id == $pr->id)>
                                        Préstamo #{{ $pr->id }} — {{ $pr->persona->nombre ?? '—' }}
                                    </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Dotaciones" id="groupDotaciones">
                                    @foreach($dotaciones as $dt)
                                    <option value="{{ $dt->id }}"
                                        @selected($incidente->items->first()?->pivot->dotacion_id == $dt->id)>
                                        Dotación #{{ $dt->id }} — {{ $dt->persona->nombre ?? '—' }}
                                    </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">
                                <i class="fas fa-file-alt"></i> Descripción
                            </label>
                            <textarea name="descripcion"
                                      class="form-control"
                                      rows="3">{{ $incidente->descripcion }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Ítems afectados -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i> Ítems Afectados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabla-items">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Ítem</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                        <th>Observación</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidente->items as $i => $it)
                                    <tr>
                                        <td>
                                            <select name="items[{{ $i }}][item_id]" class="form-control">
                                                @foreach($items as $op)
                                                <option value="{{ $op->id }}" @selected($op->id == $it->id)>
                                                    {{ $op->codigo }} — {{ $op->descripcion }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   min="1"
                                                   name="items[{{ $i }}][cantidad]"
                                                   value="{{ $it->pivot->cantidad }}"
                                                   class="form-control">
                                        </td>
                                        <td>
                                            <select name="items[{{ $i }}][estado_item]" class="form-control">
                                                <option value="PERDIDO" @selected($it->pivot->estado_item == 'PERDIDO')>
                                                    PERDIDO
                                                </option>
                                                <option value="DANADO" @selected($it->pivot->estado_item == 'DANADO')>
                                                    DAÑADO
                                                </option>
                                                <option value="NO_DEVUELTO" @selected($it->pivot->estado_item == 'NO_DEVUELTO')>
                                                    NO DEVUELTO
                                                </option>
                                                <option value="BAJA" @selected($it->pivot->estado_item == 'BAJA')>
                                                    BAJA
                                                </option>
                                                <option value="OTRO" @selected($it->pivot->estado_item == 'OTRO')>
                                                    OTRO
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control"
                                                   name="items[{{ $i }}][observacion]"
                                                   value="{{ $it->pivot->observacion }}">
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Actualizar Incidente
                        </button>
                        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Información adicional -->
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-database"></i> Información del Sistema</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $incidente->id }}</dd>

                        <dt class="col-sm-3">Código:</dt>
                        <dd class="col-sm-9">{{ $incidente->codigo }}</dd>

                        <dt class="col-sm-3">Estado:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-warning">{{ $incidente->estado }}</span>
                        </dd>

                        <dt class="col-sm-3">Creado:</dt>
                        <dd class="col-sm-9">{{ $incidente->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Actualizado:</dt>
                        <dd class="col-sm-9">{{ $incidente->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
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
    // Event listener para eliminar filas
    $(document).on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
    });

    // Mostrar/ocultar grupos según tipo
    $('#tipo').on('change', function() {
        const tipo = $(this).val();
        if (tipo === 'PRESTAMO') {
            $('#groupPrestamos').show();
            $('#groupDotaciones').hide();
        } else if (tipo === 'DOTACION') {
            $('#groupDotaciones').show();
            $('#groupPrestamos').hide();
        }
    });

    // Trigger inicial
    $('#tipo').trigger('change');

    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop
