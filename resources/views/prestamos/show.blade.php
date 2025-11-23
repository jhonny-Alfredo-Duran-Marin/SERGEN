@extends('adminlte::page')
@section('title', 'Préstamo ' . $prestamo->codigo)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-file-alt"></i> Préstamo {{ $prestamo->codigo }}
            <span
                class="badge badge-{{ $prestamo->estado === 'Completo' ? 'success' : ($prestamo->estado === 'Observado' ? 'warning' : 'primary') }}">
                {{ $prestamo->estado }}
            </span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('prestamos.edit', $prestamo) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('prestamos.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> Volver</a>
        </div>
    </div>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" title="OK">{{ session('status') }}</x-adminlte-alert>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">{{ optional($prestamo->fecha)->format('Y-m-d') }}</dd>
                <dt class="col-sm-3">Destino</dt>
                <dd class="col-sm-9">
                    @if ($prestamo->tipo_destino === 'Persona')
                        Persona: {{ $prestamo->persona?->nombre ?? '—' }}
                    @elseif($prestamo->tipo_destino === 'Proyecto')
                        Proyecto: {{ $prestamo->proyecto?->codigo }} — {{ $prestamo->proyecto?->descripcion }}
                    @else
                        Otro
                    @endif
                </dd>
                <dt class="col-sm-3">Nota</dt>
                <dd class="col-sm-9">{{ $prestamo->nota ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card card-outline card-info">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Detalle</h3>

            {{-- Acciones sobre detalle --}}
            <div class="btn-group">
                @if ($prestamo->estado !== 'Completo')
                    <a href="{{ route('devoluciones.create', $prestamo) }}" class="btn btn-primary">
                        <i class="fas fa-undo"></i> Registrar devolución
                    </a>
                    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#modal-incidente">
                        <i class="fas fa-exclamation-triangle"></i> Reporte de incidente
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ítem</th>
                            <th class="text-end">Prestado</th>
                            <th class="text-end">Devuelto</th>
                            <th class="text-end">Pendiente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamo->detalles as $d)
                            @php $pend = max(0, $d->cantidad_prestada - $d->cantidad_devuelta); @endphp
                            <tr>
                                <td>{{ $d->item->descripcion }}</td>
                                <td class="text-end">{{ $d->cantidad_prestada }}</td>
                                <td class="text-end">{{ $d->cantidad_devuelta }}</td>
                                <td class="text-end">
                                    <span class="badge badge-{{ $pend > 0 ? 'warning' : 'success' }}">{{ $pend }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Devoluciones --}}
    @if ($prestamo->devoluciones->count())
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="fas fa-undo-alt"></i> Devoluciones</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Nota</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestamo->devoluciones as $dev)
                                <tr>
                                    <td>{{ $dev->fecha->format('Y-m-d H:i') }}</td>
                                    <td>{{ $dev->estado }}</td>
                                    <td>{{ $dev->nota ?? '—' }}</td>
                                    <td>
                                        @foreach ($dev->detalles as $dd)
                                            <div>{{ $dd->item->descripcion }}: {{ $dd->cantidad }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: Reporte de incidente --}}
    <div class="modal fade" id="modal-incidente" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('prestamos.incidentes.store', $prestamo) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Reporte de incidente</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ítem (opcional)</label>
                        <select name="detalle_prestamo_id" class="form-control">
                            <option value="">— General —</option>
                            @foreach ($prestamo->detalles as $d)
                                <option value="{{ $d->id }}">{{ $d->item->descripcion }} — Pend:
                                    {{ max(0, $d->cantidad_prestada - $d->cantidad_devuelta) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="Falta">Falta</option>
                            <option value="Daño">Daño</option>
                            <option value="Pérdida">Pérdida</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripción / nota</label>
                        <textarea name="nota" class="form-control" rows="3" placeholder="Describe brevemente lo ocurrido…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger"><i class="fas fa-paper-plane"></i> Reportar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#modal-incidente">
                        <i class="fas fa-exclamation-triangle"></i> Reportar incidente
                    </a>

                </div>
            </form>
        </div>
    </div>
@stop
