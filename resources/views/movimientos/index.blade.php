@extends('adminlte::page')

@section('title', 'Historial de Movimientos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0">
            <i class="fas fa-history text-danger"></i>
            Historial Completo de Movimientos
        </h1>
        <span class="badge badge-danger badge-lg">
            <i class="fas fa-shield-alt"></i> SOLO SUPER ADMIN
        </span>
    </div>
@stop

@section('content')

    {{-- ESTADÍSTICAS DEL DÍA --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ingresos Hoy</span>
                    <span class="info-box-number">{{ $stats['ingresos_hoy'] }} unds</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Egresos Hoy</span>
                    <span class="info-box-number">{{ $stats['egresos_hoy'] }} unds</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Movimientos Hoy</span>
                    <span class="info-box-number">{{ $stats['total_hoy'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios Activos Hoy</span>
                    <span class="info-box-number">{{ $stats['usuarios_hoy'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card card-outline card-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title">
                <i class="fas fa-search"></i> Filtros Avanzados
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Ítem, código o usuario..."
                        value="{{ request('q') }}">
                </div>

                <div class="col-md-3">
                    <select name="item_id" class="form-select">
                        <option value="">Todos los ítems</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->codigo }} - {{ Str::limit($item->descripcion, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-danger"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA HISTÓRICA --}}
    <div class="card">
        <div class="card-header bg-gradient-dark text-white">
            <h3 class="card-title">
                <i class="fas fa-database"></i> Registro Completo de Movimientos
            </h3>
            <span class="badge badge-light ml-auto">Total: <strong>{{ $movimientos->total() }}</strong></span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Ítem</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                            <th>Origen</th>
                            <th>Nota</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($movimientos as $m)
                            @php
                                $rowClass = match ($m->display_tipo) {
                                    'Ingreso' => 'table-success',
                                    'Egreso' => 'table-danger',
                                    default => 'table-warning',
                                };
                            @endphp

                            <tr class="{{ $rowClass }} text-sm">

                                <td>
                                    <strong>{{ $m->fecha->format('d/m/Y') }}</strong><br>
                                    <small class="text-muted">{{ $m->fecha->format('H:i') }}</small>
                                </td>

                                <td>
                                    <strong>{{ $m->item->codigo ?? '—' }}</strong><br>
                                    <small>{{ $m->item->descripcion ?? '—' }}</small>
                                </td>

                                <td class="text-center">
                                    <span
                                        class="badge
                                @if ($m->display_tipo === 'Ingreso') badge-success
                                @elseif($m->display_tipo === 'Egreso') badge-danger
                                @else badge-warning @endif
                                badge-pill">
                                        {{ strtoupper($m->display_tipo) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $m->accion }}</small>
                                </td>

                                <td class="text-center font-weight-bold">
                                    {{ $m->cantidad ?? '—' }}
                                </td>

                                <td>
                                    <i class="fas fa-user-circle mr-2"></i>
                                    {{ $m->user->name ?? 'Sistema' }}
                                </td>

                                <td>
                                    @if ($m->prestamo)
                                        <a href="{{ route('prestamos.show', $m->prestamo) }}" class="text-primary">
                                            Préstamo {{ $m->prestamo->codigo }}
                                        </a>
                                    @elseif($m->devolucion)
                                        <span class="text-success">
                                            Devolución {{ $m->devolucion->id }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td><small class="text-muted">{{ $m->nota ?? 'Sin nota' }}</small></td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <h4>No hay movimientos registrados</h4>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {!! $movimientos->onEachSide(1)->links('pagination::bootstrap-4') !!}
        </div>


    </div>
@stop

@section('css')
    <style>
        .table-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .table-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .table-warning {
            background-color: rgba(255, 193, 7, 0.15) !important;
        }
    </style>
@stop
