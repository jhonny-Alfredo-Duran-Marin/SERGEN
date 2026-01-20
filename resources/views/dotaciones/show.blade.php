@extends('adminlte::page')

@section('title', 'Detalle de Dotación')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="m-0">
                    <i class="fas fa-eye text-info"></i> Detalle de Dotación
                    <span class="badge badge-primary badge-lg">#{{ $dotacion->id }}</span>
                </h1>
                <p class="text-muted mb-0">Información completa de la dotación asignada</p>
            </div>
            <div class="col-auto">
                <div class="btn-group shadow" role="group">
                    <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('dotaciones.edit', $dotacion) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('dotaciones.recibo', $dotacion) }}"
                       class="btn btn-danger"
                       target="_blank">
                        <i class="fas fa-file-pdf"></i> Generar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Columna Izquierda: Información General -->
        <div class="col-lg-4">
            <!-- Información de la Persona -->
            <div class="card card-widget widget-user shadow">
                <div class="widget-user-header bg-gradient-primary">
                    <h3 class="widget-user-username">{{ $dotacion->persona->nombre }}</h3>
                    <h5 class="widget-user-desc">Responsable de la Dotación</h5>
                </div>
                <div class="widget-user-image">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center"
                         style="width: 90px; height: 90px; border: 3px solid #fff;">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                </div>
                <div class="card-footer pt-5">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $dotacion->items->count() }}</h5>
                                <span class="description-text">ÍTEMS</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $dotacion->items->sum('cantidad') }}</h5>
                                <span class="description-text">CANTIDAD TOTAL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Dotación -->
            <div class="card card-primary card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> <strong>Información General</strong>
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">
                            <i class="fas fa-hashtag text-primary"></i> ID Dotación:
                        </dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-primary badge-lg">#{{ $dotacion->id }}</span>
                        </dd>

                        <dt class="col-sm-5">
                            <i class="fas fa-calendar-day text-success"></i> Fecha:
                        </dt>
                        <dd class="col-sm-7">
                            <strong>{{ $dotacion->fecha->format('d/m/Y') }}</strong>
                        </dd>

                        <dt class="col-sm-5">
                            <i class="far fa-clock text-info"></i> Hora:
                        </dt>
                        <dd class="col-sm-7">
                            {{ $dotacion->fecha->format('h:i A') }}
                        </dd>

                        <dt class="col-sm-5">
                            <i class="fas fa-traffic-light text-warning"></i> Estado:
                        </dt>
                        <dd class="col-sm-7">
                            @php
                                $estados = [
                                    'ABIERTA' => ['class' => 'warning', 'icon' => 'folder-open'],
                                    'DEVUELTA' => ['class' => 'success', 'icon' => 'check-circle'],
                                    'COMPLETADA' => ['class' => 'primary', 'icon' => 'flag-checkered'],
                                ];
                                $estado = $estados[$dotacion->estado_final] ?? ['class' => 'secondary', 'icon' => 'question'];
                            @endphp
                            <span class="badge badge-{{ $estado['class'] }} badge-lg">
                                <i class="fas fa-{{ $estado['icon'] }}"></i>
                                {{ $dotacion->estado_final }}
                            </span>
                        </dd>

                        <dt class="col-sm-5">
                            <i class="fas fa-clock text-muted"></i> Registrado:
                        </dt>
                        <dd class="col-sm-7 text-muted small">
                            {{ $dotacion->created_at->diffForHumans() }}
                        </dd>
                    </dl>

                    @if($dotacion->nota)
                        <hr>
                        <div class="callout callout-info">
                            <h6><i class="fas fa-comment"></i> Nota:</h6>
                            <p class="mb-0">{{ $dotacion->nota }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline de Actividad -->
            <div class="card card-success card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> <strong>Historial</strong>
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">{{ $dotacion->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus-circle bg-success"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="far fa-clock"></i> {{ $dotacion->created_at->format('h:i A') }}
                                </span>
                                <h3 class="timeline-header">Dotación Creada</h3>
                                <div class="timeline-body">
                                    Se registró la dotación con {{ $dotacion->items->count() }} ítems
                                </div>
                            </div>
                        </div>
                        @if($dotacion->updated_at != $dotacion->created_at)
                            <div>
                                <i class="fas fa-edit bg-warning"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="far fa-clock"></i> {{ $dotacion->updated_at->format('h:i A') }}
                                    </span>
                                    <h3 class="timeline-header">Última Modificación</h3>
                                    <div class="timeline-body">
                                        {{ $dotacion->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div>
                            <i class="far fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Ítems Asignados -->
        <div class="col-lg-8">
            <div class="card card-primary card-outline shadow">
                <div class="card-header bg-gradient-navy">
                    <h3 class="card-title">
                        <i class="fas fa-boxes"></i> <strong>Ítems Asignados</strong>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light badge-lg">
                            {{ $dotacion->items->count() }} {{ Str::plural('ítem', $dotacion->items->count()) }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($dotacion->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="60" class="text-center">#</th>
                                        <th>Ítem</th>
                                        <th width="100" class="text-center">Cantidad</th>
                                        <th width="150">Estado Origen</th>
                                        <th width="150">Próxima Entrega</th>
                                        <th width="130">Estado Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dotacion->items as $idx => $it)
                                        <tr>
                                            <td class="text-center align-middle font-weight-bold text-muted">
                                                {{ $idx + 1 }}
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <div class="rounded bg-gradient-primary text-white d-flex align-items-center justify-content-center"
                                                             style="width: 45px; height: 45px;">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div>
                                                            <span class="badge badge-info">{{ $it->item->codigo }}</span>
                                                        </div>
                                                        <div class="mt-1">
                                                            <strong>{{ $it->item->descripcion }}</strong>
                                                        </div>
                                                        <div class="text-muted small mt-1">
                                                            <i class="fas fa-warehouse"></i>
                                                            Stock disponible:
                                                            <span class="badge badge-secondary">{{ $it->item->cantidad }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <h5 class="mb-0">
                                                    <span class="badge badge-primary badge-lg">
                                                        {{ $it->cantidad }}
                                                    </span>
                                                </h5>
                                            </td>
                                            <td class="align-middle">
                                                @php
                                                    $origenBadge = [
                                                        'USO_PROPIO' => ['class' => 'info', 'icon' => 'user'],
                                                        'DE_VENTA' => ['class' => 'success', 'icon' => 'shopping-cart'],
                                                        'COMPRADO' => ['class' => 'warning', 'icon' => 'dollar-sign'],
                                                    ];
                                                    $origen = $origenBadge[$it->estado_item] ?? ['class' => 'secondary', 'icon' => 'question'];
                                                @endphp
                                                <span class="badge badge-{{ $origen['class'] }}">
                                                    <i class="fas fa-{{ $origen['icon'] }}"></i>
                                                    {{ $it->estado_item }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                @if($it->fecha_siguiente)
                                                    @php
                                                        $fechaSiguiente = \Carbon\Carbon::parse($it->fecha_siguiente);
                                                        $esVencido = $fechaSiguiente->isPast() && $dotacion->estado_final == 'ABIERTA';
                                                        $estaProximo = $fechaSiguiente->diffInDays(now()) <= 30 && !$esVencido;
                                                    @endphp

                                                    <div>
                                                        <i class="fas fa-calendar-alt text-primary"></i>
                                                        <strong>{{ $fechaSiguiente->format('d/m/Y') }}</strong>
                                                    </div>
                                                    <div class="mt-1">
                                                        @if($esVencido)
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-exclamation-triangle"></i> VENCIDO
                                                            </span>
                                                        @elseif($estaProximo)
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock"></i> PRÓXIMO
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> VIGENTE
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-minus-circle"></i> No programada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($it->estado_item_devolucion)
                                                    <span class="badge badge-info">
                                                        {{ $it->estado_item_devolucion }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-handshake"></i> EN USO
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay ítems asignados a esta dotación</p>
                        </div>
                    @endif
                </div>

                <!-- Footer con resumen -->
                @if($dotacion->items->count() > 0)
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-gradient-info">
                                    <span class="info-box-icon">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Ítems</span>
                                        <span class="info-box-number">{{ $dotacion->items->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Cantidad Total</span>
                                        <span class="info-box-number">{{ $dotacion->items->sum('cantidad') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-gradient-warning">
                                    <span class="info-box-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Con Renovación</span>
                                        <span class="info-box-number">
                                            {{ $dotacion->items->whereNotNull('fecha_siguiente')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Alertas de vencimientos -->
            @php
                $itemsVencidos = $dotacion->items->filter(function($item) use ($dotacion) {
                    return $item->fecha_siguiente &&
                           \Carbon\Carbon::parse($item->fecha_siguiente)->isPast() &&
                           $dotacion->estado_final == 'ABIERTA';
                });

                $itemsProximos = $dotacion->items->filter(function($item) use ($dotacion) {
                    return $item->fecha_siguiente &&
                           \Carbon\Carbon::parse($item->fecha_siguiente)->diffInDays(now()) <= 30 &&
                           !\Carbon\Carbon::parse($item->fecha_siguiente)->isPast() &&
                           $dotacion->estado_final == 'ABIERTA';
                });
            @endphp

            @if($itemsVencidos->count() > 0)
                <div class="alert alert-danger alert-dismissible shadow">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <h5><i class="icon fas fa-ban"></i> ¡Atención! Renovaciones Vencidas</h5>
                    Hay {{ $itemsVencidos->count() }} {{ Str::plural('ítem', $itemsVencidos->count()) }}
                    con fecha de renovación vencida que requieren atención inmediata.
                </div>
            @endif

            @if($itemsProximos->count() > 0)
                <div class="alert alert-warning alert-dismissible shadow">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Renovaciones Próximas</h5>
                    Hay {{ $itemsProximos->count() }} {{ Str::plural('ítem', $itemsProximos->count()) }}
                    con renovación próxima (menos de 30 días).
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.4rem 0.7rem;
        }

        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #dee2e6;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }

        .timeline > div > .timeline-item {
            margin-right: 10px;
            margin-left: 60px;
            margin-top: 0;
            padding: 0;
        }

        .card-widget {
            border-radius: 10px;
        }

        .info-box {
            border-radius: 8px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
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
