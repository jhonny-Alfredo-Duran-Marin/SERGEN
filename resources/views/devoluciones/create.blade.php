@extends('adminlte::page')
@section('title', 'Devolución — ' . $prestamo->codigo)

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="m-0">
        <i class="fas fa-undo-alt text-primary"></i>
        Devolución — {{ $prestamo->codigo }}
    </h1>
    <div>
        <button type="button" class="btn btn-danger"
                data-toggle="modal" data-target="#modal-incidente">
            <i class="fas fa-exclamation-triangle"></i> Reportar Incidente
        </button>
        <a href="{{ route('prestamos.show', $prestamo) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <!-- RESUMEN -->
        <div class="card card-info card-outline mb-4">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Resumen del Préstamo</h3>
            </div>
            <div class="card-body">
                <div class="row text-sm">
                    <div class="col-md-3"><strong>Código:</strong> {{ $prestamo->codigo }}</div>
                    <div class="col-md-3"><strong>Fecha:</strong> {{ $prestamo->fecha->format('d/m/Y') }}</div>
                    <div class="col-md-3"><strong>Persona:</strong> {{ $prestamo->persona->nombre }}</div>
                    <div class="col-md-3">
                        <strong>KIT:</strong>
                        @if($prestamo->kit)
                            <span class="badge badge-success">{{ $prestamo->kit->codigo }} — {{ $prestamo->kit->nombre }}</span>
                        @else
                            <span class="text-muted">Sin kit</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($prestamo->kit)
            <!-- DEVOLVER KIT COMPLETO -->
            <div class="card card-success mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i> Devolución Rápida del KIT
                    </h3>
                </div>
                <div class="card-body text-center py-5">
                    <p class="lead mb-4">
                        ¿El kit <strong>{{ $prestamo->kit->nombre }}</strong> vuelve <u>completo y en buen estado</u>?
                    </p>
                    <form method="POST" action="{{ route('devoluciones.kit', $prestamo) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-success px-5">
                            <i class="fas fa-check-circle"></i> SÍ, DEVOLVER KIT COMPLETO
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- DEVOLUCIÓN MANUAL -->
        <form method="POST" action="{{ route('devoluciones.store', $prestamo) }}" class="card card-primary card-outline">
            @csrf
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-list"></i> Devolución Detallada
                </h3>
                <button type="button" id="devolver-todo" class="btn btn-warning btn-sm">
                    <i class="fas fa-magic"></i> Devolver TODO lo pendiente
                </button>
            </div>

            <div class="card-body">
                <div class="form-group mb-4">
                    <label><i class="fas fa-sticky-note"></i> Nota general (opcional)</label>
                    <textarea name="nota" class="form-control" rows="2" placeholder="Ej: El kit volvió completo, sin daños...">{{ old('nota') }}</textarea>
                </div>

                @if($pendientes->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-devolucion">
                            <thead class="bg-light">
                                <tr>
                                    <th>Ítem</th>
                                    <th class="text-center">Prestado</th>
                                    <th class="text-center">Devuelto</th>
                                    <th class="text-center">Pendiente</th>
                                    <th style="width:180px">Devolver</th>
                                    <th style="width:120px">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendientes as $d)
                                    <tr data-item="{{ $d->item_id }}">
                                        <td>
                                            <strong>{{ $d->item->codigo }}</strong><br>
                                            <small class="text-muted">{{ $d->item->descripcion }}</small>
                                            <input type="hidden" name="items[]" value="{{ $d->item_id }}">
                                        </td>
                                        <td class="text-center">{{ $d->cantidad_prestada }}</td>
                                        <td class="text-center">{{ $d->cantidad_devuelta }}</td>
                                        <td class="text-center font-weight-bold text-danger">
                                            {{ $d->pendiente }}
                                        </td>
                                        <td>
                                            <input type="number" name="cantidades[]"
                                                   class="form-control form-control-sm devolver-cant"
                                                   min="0" max="{{ $d->pendiente }}"
                                                   value="0" data-max="{{ $d->pendiente }}">
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-warning estado-item">Sin devolver</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>¡Todo devuelto!</h4>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-lg float-right" id="btn-guardar">
                    <i class="fas fa-save"></i> Guardar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DE INCIDENTE (AHORA SÍ FUNCIONA) --}}
<div class="modal fade" id="modal-incidente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('prestamos.incidentes.store', $prestamo) }}" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Reportar Incidente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ítem con problema *</label>
                            <select name="item_id" class="form-control" required>
                                <option value="">Seleccionar ítem...</option>
                                @foreach($prestamo->detalles as $d)
                                    <option value="{{ $d->item_id }}">
                                        {{ $d->item->codigo }} — {{ $d->item->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de incidente *</label>
                            <select name="tipo" class="form-control" required>
                                <option value="Falta">Falta</option>
                                <option value="Daño">Daño</option>
                                <option value="Pérdida">Pérdida</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción detallada</label>
                    <textarea name="nota" class="form-control" rows="4"
                              placeholder="Ej: Faltan 2 prensas sargento del kit..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save"></i> Guardar Incidente
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
    .estado-item { transition: all 0.3s; }
    .devuelto { background: #d4edda !important; }
    .parcial { background: #fff3cd !important; }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filas = document.querySelectorAll('#tabla-devolucion tbody tr');

    const actualizarEstado = (input) => {
        const tr = input.closest('tr');
        const devuelto = parseInt(input.value) || 0;
        const max = parseInt(input.dataset.max);
        const badge = tr.querySelector('.estado-item');

        tr.classList.remove('devuelto', 'parcial');
        badge.className = 'badge estado-item';

        if (devuelto === 0) {
            badge.classList.add('badge-warning');
            badge.textContent = 'Sin devolver';
        } else if (devuelto === max) {
            badge.classList.add('badge-success');
            badge.textContent = 'Completo';
            tr.classList.add('devuelto');
        } else {
            badge.classList.add('badge-info');
            badge.textContent = 'Parcial';
            tr.classList.add('parcial');
        }
    };

    document.querySelectorAll('.devolver-cant').forEach(input => {
        input.addEventListener('input', () => {
            let val = parseInt(input.value) || 0;
            if (val < 0) val = 0;
            if (val > parseInt(input.dataset.max)) val = parseInt(input.dataset.max);
            input.value = val;
            actualizarEstado(input);
        });
        actualizarEstado(input);
    });

    document.getElementById('devolver-todo')?.addEventListener('click', () => {
        document.querySelectorAll('.devolver-cant').forEach(input => {
            input.value = input.dataset.max;
            actualizarEstado(input);
        });
    });

    document.getElementById('btn-guardar')?.addEventListener('click', function(e) {
        const total = Array.from(document.querySelectorAll('.devolver-cant'))
            .reduce((sum, i) => sum + (parseInt(i.value) || 0), 0);

        if (total === 0) {
            e.preventDefault();
            alert('Debe devolver al menos 1 ítem');
        }
    });
});
</script>
@stop
