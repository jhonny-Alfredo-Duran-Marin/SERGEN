@extends('adminlte::page')

@section('title', 'Detalle del Incidente')

@section('content_header')
    <h1>
        <i class="fas fa-exclamation-triangle"></i> Incidente:
        <span class="text-danger">{{ $incidente->codigo }}</span>
    </h1>
@stop

@section('content')
    @if(session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row">
        <!-- Columna Principal -->
        <div class="col-md-8">
            <!-- Información General -->
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información General
                    </h3>
                    <div class="card-tools">
                        @php
                            $estadoBadge = [
                                'ACTIVO' => ['class' => 'warning', 'icon' => 'exclamation-circle'],
                                'EN_PROCESO' => ['class' => 'info', 'icon' => 'sync'],
                                'COMPLETADO' => ['class' => 'success', 'icon' => 'check-circle']
                            ];
                            $badge = $estadoBadge[$incidente->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                        @endphp
                        <span class="badge badge-{{ $badge['class'] }} badge-lg">
                            <i class="fas fa-{{ $badge['icon'] }}"></i>
                            {{ $incidente->estado }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">
                            <i class="fas fa-barcode text-danger"></i> Código:
                        </dt>
                        <dd class="col-sm-8">
                            <strong class="text-lg">{{ $incidente->codigo }}</strong>
                        </dd>

                        <dt class="col-sm-4">
                            <i class="fas fa-user text-primary"></i> Persona:
                        </dt>
                        <dd class="col-sm-8">
                            <strong>{{ $incidente->persona->nombre }}</strong>
                        </dd>

                        <dt class="col-sm-4">
                            <i class="fas fa-tag text-info"></i> Tipo:
                        </dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $incidente->tipo === 'PRESTAMO' ? 'primary' : 'info' }}">
                                {{ $incidente->tipo }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">
                            <i class="fas fa-calendar text-warning"></i> Fecha:
                        </dt>
                        <dd class="col-sm-8">
                            {{ $incidente->fecha_incidente }}
                            <small class="text-muted">
                                ({{ $incidente->fecha_incidente }})
                            </small>
                        </dd>

                        @if($incidente->descripcion)
                        <dt class="col-sm-4">
                            <i class="fas fa-file-alt text-secondary"></i> Descripción:
                        </dt>
                        <dd class="col-sm-8">
                            {{ $incidente->descripcion }}
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Ítems Afectados -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i> Ítems Afectados
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">
                            {{ $incidente->items->count() }} {{ $incidente->items->count() == 1 ? 'ítem' : 'ítems' }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Estado</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidente->items as $it)
                            <tr>
                                <td>
                                    <strong>{{ $it->codigo }}</strong><br>
                                    <small class="text-muted">{{ $it->descripcion }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger">
                                        {{ $it->pivot->cantidad }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">
                                        {{ $it->pivot->estado_item }}
                                    </span>
                                </td>
                                <td>
                                    {{ $it->pivot->observacion ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Devoluciones Registradas -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-undo"></i> Devoluciones Registradas
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success">
                            {{ $incidente->devoluciones->count() }} {{ $incidente->devoluciones->count() == 1 ? 'devolución' : 'devoluciones' }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($incidente->devoluciones->count() > 0)
                    <table class="table table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Item</th>
                                <th class="text-center">Cantidad</th>
                                <th>Resultado</th>
                                <th class="text-center">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidente->devoluciones as $d)
                            <tr>
                                <td>
                                    {{ $d->created_at->format('d/m/Y H:i') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $d->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $d->item->codigo }}</strong><br>
                                    <small class="text-muted">{{ $d->item->descripcion }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">
                                        {{ $d->cantidad_devuelta }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $resultadoBadge = [
                                            'DEVUELTO_OK' => ['class' => 'success', 'icon' => 'check'],
                                            'DEVUELTO_DANADO' => ['class' => 'warning', 'icon' => 'exclamation-triangle'],
                                            'NO_RECUPERADO' => ['class' => 'danger', 'icon' => 'times'],
                                            'REPARABLE' => ['class' => 'info', 'icon' => 'wrench']
                                        ];
                                        $resBadge = $resultadoBadge[$d->resultado] ?? ['class' => 'secondary', 'icon' => 'question'];
                                    @endphp
                                    <span class="badge badge-{{ $resBadge['class'] }}">
                                        <i class="fas fa-{{ $resBadge['icon'] }}"></i>
                                        {{ $d->resultado }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('incidentes.recibo', $d) }}"
                                       class="btn btn-dark btn-sm"
                                       target="_blank"
                                       data-toggle="tooltip"
                                       title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No hay devoluciones registradas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-md-4">
            <!-- Acciones -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Acciones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($incidente->estado !== 'COMPLETADO')
                        <a href="{{ route('incidentes.devolver', $incidente) }}"
                           class="btn btn-success btn-block">
                            <i class="fas fa-undo"></i> Registrar Devolución
                        </a>
                        @endif

                        <a href="{{ route('incidentes.edit', $incidente) }}"
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Editar Incidente
                        </a>

                        <a href="{{ route('incidentes.index') }}"
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>

                        <button type="button"
                                class="btn btn-danger btn-block"
                                data-toggle="modal"
                                data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Eliminar Incidente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Resumen
                    </h3>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td><i class="fas fa-box text-muted"></i> Items afectados:</td>
                            <td class="text-right">
                                <strong>{{ $incidente->items->count() }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-layer-group text-muted"></i> Cantidad total:</td>
                            <td class="text-right">
                                <strong>{{ $incidente->items->sum('pivot.cantidad') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-undo text-muted"></i> Devoluciones:</td>
                            <td class="text-right">
                                <strong class="text-success">
                                    {{ $incidente->devoluciones->count() }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-check text-muted"></i> Cant. devuelta:</td>
                            <td class="text-right">
                                <strong class="text-success">
                                    {{ $incidente->devoluciones->sum('cantidad_devuelta') }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el incidente <strong>{{ $incidente->codigo }}</strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong>
                        <ul class="mb-0">
                            <li>Se perderá toda la información del incidente</li>
                            <li>Se eliminarán las devoluciones registradas</li>
                            <li>Esta acción no se puede deshacer</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('incidentes.destroy', $incidente) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5em 0.75em;
    }
    .d-grid {
        display: grid;
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@stop

@section('js')
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
