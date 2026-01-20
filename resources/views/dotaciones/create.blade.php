@extends('adminlte::page')

@section('title', 'Nueva Dotación')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="m-0">
                    <i class="fas fa-plus-circle text-success"></i> Crear Nueva Dotación
                </h1>
                <p class="text-muted mb-0">Complete el formulario para registrar una nueva dotación de equipos y materiales</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary shadow">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <!-- Pasos del formulario -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-center flex-fill">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                            <div class="small font-weight-bold">Datos Generales</div>
                        </div>
                        <div class="flex-fill text-center">
                            <i class="fas fa-arrow-right text-muted"></i>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-boxes fa-lg"></i>
                            </div>
                            <div class="small font-weight-bold">Asignar Ítems</div>
                        </div>
                        <div class="flex-fill text-center">
                            <i class="fas fa-arrow-right text-muted"></i>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-check fa-lg"></i>
                            </div>
                            <div class="small font-weight-bold">Confirmar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('dotaciones.store') }}" id="form-dotacion">
        @csrf

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <!-- Datos Generales -->
                <div class="card card-primary card-outline shadow">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> <strong>Paso 1: Datos Generales</strong>
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light">Requerido</span>
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
                                            <option value="">— Seleccionar persona responsable —</option>
                                            @foreach ($personas as $p)
                                                <option value="{{ $p->id }}" @selected(old('persona_id') == $p->id)>
                                                    {{ $p->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('persona_id')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @else
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Seleccione la persona que recibirá los ítems
                                        </small>
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
                                               value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}"
                                               required>
                                    </div>
                                    @error('fecha')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @else
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Fecha y hora de registro de la dotación
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nota" class="font-weight-bold">
                                        <i class="fas fa-sticky-note text-warning"></i> Nota u Observaciones
                                        <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <textarea name="nota"
                                              id="nota"
                                              class="form-control @error('nota') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Ingrese cualquier observación o comentario adicional sobre esta dotación...">{{ old('nota') }}</textarea>
                                    @error('nota')
                                        <span class="text-danger small">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </span>
                                    @else
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Campo opcional para notas adicionales
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Callout informativo -->
                        <div class="callout callout-info">
                            <h6><i class="fas fa-lightbulb"></i> Información Importante:</h6>
                            <ul class="mb-0 pl-4">
                                <li>La persona seleccionada será responsable de los ítems asignados</li>
                                <li>La fecha y hora se utilizarán como referencia para las renovaciones</li>
                                <li>Todos los campos marcados con <span class="text-danger">*</span> son obligatorios</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Ítems (incluir parcial mejorado) -->
                @include('dotaciones.partials.form', ['items' => $items, 'dotacion' => null])

                <!-- Botones de acción -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-lg btn-block shadow">
                                    <i class="fas fa-save"></i> Guardar Dotación
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="reset" class="btn btn-warning btn-lg btn-block shadow" onclick="resetForm()">
                                    <i class="fas fa-redo"></i> Limpiar Formulario
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary btn-lg btn-block shadow">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instrucciones finales -->
                <div class="alert alert-info alert-dismissible shadow">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <h5><i class="icon fas fa-info"></i> Antes de Guardar</h5>
                    <ul class="mb-0">
                        <li>Verifique que todos los datos generales estén completos</li>
                        <li>Asegúrese de haber agregado al menos un ítem a la dotación</li>
                        <li>Confirme que las cantidades no excedan el stock disponible</li>
                        <li>Revise las fechas de renovación programadas</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .select2-container--bootstrap4 .select2-selection {
            border-color: #ced4da;
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        .callout {
            border-radius: 8px;
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
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });

            // Validación del formulario antes de enviar
            $('#form-dotacion').on('submit', function(e) {
                const itemsCount = $('#tabla-items tbody tr').not('#no-items-row').length;

                if (itemsCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ítems Requeridos',
                        text: 'Debe agregar al menos un ítem a la dotación antes de guardar',
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

                // Confirmación final
                e.preventDefault();
                Swal.fire({
                    title: '¿Guardar Dotación?',
                    html: `Se creará una nueva dotación con <strong>${itemsCount} ítem(s)</strong>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Sí, guardar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Guardando...',
                            html: 'Por favor espere mientras se procesa la dotación',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Enviar formulario
                        this.submit();
                    }
                });
            });
        });

        function resetForm() {
            Swal.fire({
                title: '¿Limpiar Formulario?',
                text: "Se perderán todos los datos ingresados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Limpiar select2
                    $('.select2').val(null).trigger('change');

                    // Limpiar tabla de items
                    $('#tabla-items tbody').html(`
                        <tr id="no-items-row">
                            <td colspan="5" class="text-center text-muted py-5 bg-light">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="h5">No hay ítems agregados</p>
                                <p class="small">Utilice el buscador para agregar productos a esta dotación</p>
                            </td>
                        </tr>
                    `);

                    // Resetear índice
                    index = 0;
                    addedItemIds.clear();
                    updateTotalItems();

                    toastr.success('Formulario limpiado', 'Éxito');
                }
            });
        }
    </script>
@stop
