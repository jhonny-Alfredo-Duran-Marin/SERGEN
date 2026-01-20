@extends('adminlte::page')

@section('title', 'Editar Dotación')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="m-0">
                    <i class="fas fa-edit text-warning"></i> Editar Dotación
                    <span class="badge badge-primary badge-lg">#{{ $dotacion->id }}</span>
                </h1>
                <p class="text-muted mb-0">Modifique los datos de la dotación existente</p>
            </div>
            <div class="col-auto">
                <div class="btn-group shadow" role="group">
                    <a href="{{ route('dotaciones.show', $dotacion) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <!-- Información de la dotación actual -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="alert alert-info shadow-sm">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col">
                        <h5 class="mb-1">Editando Dotación</h5>
                        <p class="mb-0">
                            <strong>Responsable:</strong> {{ $dotacion->persona->nombre }} |
                            <strong>Fecha Original:</strong> {{ $dotacion->fecha->format('d/m/Y h:i A') }} |
                            <strong>Estado:</strong>
                            <span class="badge badge-{{ $dotacion->estado_final == 'ABIERTA' ? 'warning' : 'success' }}">
                                {{ $dotacion->estado_final }}
                            </span>
                        </p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('dotaciones.recibo', $dotacion) }}"
                           class="btn btn-sm btn-danger"
                           target="_blank">
                            <i class="fas fa-file-pdf"></i> Ver PDF Actual
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <form method="POST" action="{{ route('dotaciones.update', $dotacion) }}" id="form-edit-dotacion">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <!-- Datos Generales -->
                <div class="card card-warning card-outline shadow">
                    <div class="card-header bg-gradient-warning">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> <strong>Actualizar Datos Generales</strong>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="persona_id" class="font-weight-bold">
                                        <i class="fas fa-user text-primary"></i> Persona Responsable
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-tag"></i>
                                            </span>
                                        </div>
                                        <select name="persona_id"
                                                id="persona_id"
                                                class="form-control select2 @error('persona_id') is-invalid @enderror"
                                                required>
                                            @foreach ($personas as $p)
                                                <option value="{{ $p->id }}" @selected($dotacion->persona_id == $p->id)>
                                                    {{ $p->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('persona_id')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha" class="font-weight-bold">
                                        <i class="fas fa-calendar-alt text-success"></i> Fecha y Hora de Dotación
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-check"></i>
                                            </span>
                                        </div>
                                        <input type="datetime-local"
                                               name="fecha"
                                               id="fecha"
                                               class="form-control @error('fecha') is-invalid @enderror"
                                               value="{{ old('fecha', $dotacion->fecha->format('Y-m-d\TH:i')) }}"
                                               required>
                                    </div>
                                    @error('fecha')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado_final" class="font-weight-bold">
                                        <i class="fas fa-traffic-light text-info"></i> Estado de la Dotación
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="estado_final"
                                            id="estado_final"
                                            class="form-control @error('estado_final') is-invalid @enderror"
                                            required>
                                        <option value="ABIERTA" @selected($dotacion->estado_final == 'ABIERTA')>
                                            <i class="fas fa-folder-open"></i> ABIERTA
                                        </option>
                                        <option value="DEVUELTA" @selected($dotacion->estado_final == 'DEVUELTA')>
                                            <i class="fas fa-check-circle"></i> DEVUELTA
                                        </option>
                                        <option value="COMPLETADA" @selected($dotacion->estado_final == 'COMPLETADA')>
                                            <i class="fas fa-flag-checkered"></i> COMPLETADA
                                        </option>
                                    </select>
                                    @error('estado_final')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @else
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Cambie el estado según el progreso de la dotación
                                        </small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nota" class="font-weight-bold">
                                        <i class="fas fa-sticky-note text-warning"></i> Nota u Observaciones
                                    </label>
                                    <textarea name="nota"
                                              id="nota"
                                              class="form-control @error('nota') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Observaciones adicionales...">{{ old('nota', $dotacion->nota) }}</textarea>
                                    @error('nota')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Callout de advertencia -->
                        <div class="callout callout-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Importante:</h6>
                            <p class="mb-0">
                                Al cambiar la fecha de dotación, considere actualizar también las fechas de renovación de los ítems.
                                Los cambios en el estado pueden afectar los reportes y seguimiento.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ítems (incluir parcial mejorado) -->
                @include('dotaciones.partials.form', [
                    'items' => $items,
                    'dotacion' => $dotacion
                ])

                <!-- Resumen de cambios -->
                <div class="card card-info card-outline shadow">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-check"></i> <strong>Resumen de la Dotación</strong>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-gradient-primary">
                                    <span class="info-box-icon">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">ID Dotación</span>
                                        <span class="info-box-number">#{{ $dotacion->id }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Ítems</span>
                                        <span class="info-box-number" id="total-items-summary">
                                            {{ $dotacion->items->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-gradient-warning">
                                    <span class="info-box-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Creado</span>
                                        <span class="info-box-number">
                                            {{ $dotacion->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-gradient-info">
                                    <span class="info-box-icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Modificado</span>
                                        <span class="info-box-number">
                                            {{ $dotacion->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning btn-lg btn-block shadow">
                                    <i class="fas fa-save"></i> Actualizar Dotación
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('dotaciones.show', $dotacion) }}"
                                   class="btn btn-info btn-lg btn-block shadow">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('dotaciones.index') }}"
                                   class="btn btn-secondary btn-lg btn-block shadow">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>

                        <!-- Botón de eliminación -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <hr>
                                <div class="text-center">
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            onclick="confirmarEliminacion()">
                                        <i class="fas fa-trash-alt"></i> Eliminar esta Dotación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Formulario oculto para eliminación -->
    <form id="delete-form"
          action="{{ route('dotaciones.destroy', $dotacion) }}"
          method="POST"
          class="d-none">
        @csrf
        @method('DELETE')
    </form>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        .info-box {
            min-height: 90px;
            border-radius: 8px;
        }

        .callout {
            border-radius: 8px;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.8rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: '— Seleccione una opción —',
                allowClear: false
            });

            // Actualizar resumen cuando cambia la tabla
            const originalUpdateTotal = window.updateTotalItems;
            window.updateTotalItems = function() {
                originalUpdateTotal();
                const total = $('#tabla-items tbody tr').not('#no-items-row').length;
                $('#total-items-summary').text(total);
            };

            // Validación del formulario
            $('#form-edit-dotacion').on('submit', function(e) {
                const itemsCount = $('#tabla-items tbody tr').not('#no-items-row').length;

                if (itemsCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ítems Requeridos',
                        text: 'Debe tener al menos un ítem en la dotación',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                // Validar cantidades vs stock
                let errorStock = false;
                $('.cantidad-input').each(function() {
                    const cantidad = parseInt($(this).val()) || 0;
                    const stock = parseInt($(this).data('stock')) || 0;

                    if (cantidad > stock) {
                        errorStock = true;
                        return false;
                    }
                });

                if (errorStock) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Stock',
                        text: 'Hay ítems con cantidades que exceden el stock disponible',
                        confirmButtonColor: '#d33'
                    });
                    return false;
                }

                // Confirmación
                e.preventDefault();
                Swal.fire({
                    title: '¿Actualizar Dotación?',
                    html: `Se actualizará la dotación <strong>#{{ $dotacion->id }}</strong> con los nuevos datos`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Sí, actualizar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Actualizando...',
                            html: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        this.submit();
                    }
                });
            });
        });

        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Eliminar Dotación?',
                html: `
                    <div class="text-left">
                        <p>Esta acción es <strong>irreversible</strong> y eliminará:</p>
                        <ul>
                            <li>La dotación #{{ $dotacion->id }}</li>
                            <li>Todos los ítems asociados</li>
                            <li>El historial de cambios</li>
                        </ul>
                        <p class="text-danger"><strong>¿Está completamente seguro?</strong></p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar definitivamente',
                cancelButtonText: '<i class="fas fa-times"></i> No, conservar',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
@stop
