@extends('adminlte::page')

@section('title', 'Devoluciones — ' . $prestamo->codigo)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-history text-primary"></i>
        Historial de Devoluciones — {{ $prestamo->codigo }}
    </h1>
    <div>
        @if($prestamo->estado !== 'Completo')
            <a href="{{ route('devoluciones.create', $prestamo) }}" class="btn btn-success shadow-sm">
                <i class="fas fa-plus"></i> Nueva Devolución
            </a>
        @endif
        <a href="{{ route('prestamos.show', $prestamo) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver al Préstamo
        </a>
    </div>
</div>
@stop

@section('content')

{{-- Info del Préstamo --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong class="text-muted">Estado del Préstamo:</strong>
                        <h4>
                            <span class="badge badge-{{
                                $prestamo->estado === 'Activo' ? 'primary' :
                                ($prestamo->estado === 'Observado' ? 'warning' : 'success')
                            }}">
                                <i class="fas fa-{{
                                    $prestamo->estado === 'Activo' ? 'clock' :
                                    ($prestamo->estado === 'Observado' ? 'exclamation-triangle' : 'check-circle')
                                }}"></i>
                                {{ $prestamo->estado }}
                            </span>
                        </h4>
                    </div>
                    <div class="col-md-3">
                        <strong class="text-muted">Persona:</strong>
                        <p class="mb-0">{{ $prestamo->persona->nombre }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong class="text-muted">Fecha Préstamo:</strong>
                        <p class="mb-0">{{ $prestamo->fecha->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong class="text-muted">Total Devoluciones:</strong>
                        <p class="mb-0">
                            <span class="badge badge-info badge-lg">
                                {{ $prestamo->devoluciones->count() }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Timeline de Devoluciones --}}
@if ($prestamo->devoluciones->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay devoluciones registradas</h5>
            <p class="text-muted">Este préstamo aún no tiene devoluciones</p>
            @if($prestamo->estado !== 'Completo')
                <a href="{{ route('devoluciones.create', $prestamo) }}" class="btn btn-success mt-2">
                    <i class="fas fa-plus"></i> Registrar Primera Devolución
                </a>
            @endif
        </div>
    </div>
@else
    <div class="timeline">
        @foreach ($prestamo->devoluciones->sortByDesc('created_at') as $dev)
            <div class="time-label">
                <span class="bg-{{
                    $dev->estado === 'Completa' ? 'success' :
                    ($dev->estado === 'Parcial' ? 'warning' : 'secondary')
                }}">
                    {{ $dev->created_at->format('d/m/Y') }}
                </span>
            </div>

            <div>
                <i class="fas fa-{{
                    $dev->estado === 'Completa' ? 'check-circle bg-success' :
                    ($dev->estado === 'Parcial' ? 'clock bg-warning' : 'times-circle bg-danger')
                }}"></i>

                <div class="timeline-item">
                    <span class="time">
                        <i class="fas fa-clock"></i>
                        {{ $dev->created_at->format('H:i') }}
                    </span>

                    <h3 class="timeline-header">
                        <strong>Devolución #{{ $dev->id }}</strong>
                        <span class="badge badge-{{
                            $dev->estado === 'Completa' ? 'success' :
                            ($dev->estado === 'Parcial' ? 'warning' : 'secondary')
                        }} ml-2">
                            {{ $dev->estado }}
                        </span>
                    </h3>

                    <div class="timeline-body">
                        {{-- Items Sueltos --}}
                        @if($dev->detalles->count() > 0)
                            <h5 class="text-primary">
                                <i class="fas fa-box"></i> Items Sueltos
                            </h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Cant. / Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dev->detalles->groupBy('item_id') as $itemId => $detalles)
                                            @php $item = $detalles->first()->item; @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->codigo ?? 'S/C' }}</strong><br>
                                                    <small class="text-muted">{{ $item->descripcion ?? 'Item no encontrado' }}</small>
                                                </td>
                                                <td>
                                                    @foreach($detalles as $det)
                                                        <span class="badge badge-{{
                                                            $det->estado === 'OK' ? 'success' :
                                                            ($det->estado === 'Faltante' ? 'warning' : 'danger')
                                                        }} mb-1">
                                                            {{ $det->cantidad }} — {{ $det->estado }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- Items de Kits --}}
                        @if($dev->detallesKit->count() > 0)
                            <h5 class="text-warning mt-3">
                                <i class="fas fa-boxes"></i> Items retornados de Kits
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Kit</th>
                                            <th>Item</th>
                                            <th class="text-center">Cant. / Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dev->detallesKit->groupBy('kit_id') as $kitId => $detallesKit)
                                            @php
                                                $kit = $detallesKit->first()->kit;
                                                $itemsEnEsteKit = $detallesKit->groupBy('item_id');
                                            @endphp
                                            @foreach($itemsEnEsteKit as $itemId => $detalles)
                                                @php $item = $detalles->first()->item; @endphp
                                                <tr>
                                                    @if($loop->first)
                                                        <td rowspan="{{ $itemsEnEsteKit->count() }}" class="align-middle bg-light">
                                                            @if($kit)
                                                                <strong>{{ $kit->codigo }}</strong><br>
                                                                <small class="text-muted">{{ $kit->nombre }}</small>
                                                            @else
                                                                <span class="text-muted italic">Kit no identificado</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <strong>{{ $item->codigo ?? 'S/C' }}</strong><br>
                                                        <small class="text-muted">{{ $item->descripcion ?? 'N/A' }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @foreach($detalles as $det)
                                                            <span class="badge badge-{{
                                                                $det->estado === 'OK' ? 'success' :
                                                                ($det->estado === 'Faltante' ? 'warning' : 'danger')
                                                            }} mb-1">
                                                                {{ $det->cantidad }} — {{ $det->estado }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if($dev->nota)
                            <div class="alert alert-info mt-2 mb-0">
                                <i class="fas fa-comment"></i>
                                <strong>Nota:</strong> {{ $dev->nota }}
                            </div>
                        @endif
                    </div>

                    <div class="timeline-footer">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Registrado por: {{ $dev->user->name ?? 'Sistema' }}
                        </small>
                    </div>
                </div>
            </div>
        @endforeach

        <div>
            <i class="fas fa-flag-checkered bg-gray"></i>
        </div>
    </div>
@endif

@stop

@section('css')
<style>
    .timeline { position: relative; margin: 0 0 30px 0; padding: 0; list-style: none; }
    .timeline:before { content: ''; position: absolute; top: 0; bottom: 0; width: 4px; background: #ddd; left: 31px; margin: 0; }
    .timeline > div > .timeline-item { box-shadow: 0 1px 4px rgba(0,0,0,0.1); border-radius: 4px; margin-top: 0; background: #fff; color: #444; margin-left: 60px; margin-right: 15px; padding: 0; position: relative; }
    .timeline > div > .fas { width: 40px; height: 40px; font-size: 16px; line-height: 40px; position: absolute; color: #fff; background: #999; border-radius: 50%; text-align: center; left: 18px; top: 0; }
    .timeline > .time-label > span { font-weight: 600; padding: 5px 10px; display: inline-block; background-color: #fff; border-radius: 4px; }
    .timeline-header { margin: 0; color: #555; border-bottom: 1px solid #f4f4f4; padding: 10px; font-size: 16px; line-height: 1.1; }
    .timeline-body { padding: 10px; }
    .timeline-footer { padding: 10px; background-color: #f4f4f4; border-top: 1px solid #ddd; }
    .time { color: #999; float: right; padding: 10px; font-size: 12px; }
    .badge-lg { font-size: 1rem; padding: 0.5em 0.75em; }
</style>
@stop
