@extends('adminlte::page')

@section('title', 'Consola de Gestión Central - Ser.Gen')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 text-navy font-weight-bold" style="letter-spacing: -0.5px;">
                <i class="fas fa-chart-line mr-2"></i>Consola de Gestión Estratégica
            </h1>
            <p class="text-muted small mb-0">Constructora Ser.Gen | Monitoreo de Activos y Flujo de Almacén</p>
        </div>
        <div class="col-sm-6 text-right">
            <div class="btn-group shadow-sm">
                <button class="btn btn-white btn-sm border">
                    <i class="far fa-calendar-alt mr-1"></i> {{ date('d M, Y') }}
                </button>
                <button class="btn btn-navy btn-sm" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt mr-1"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- RESUMEN EJECUTIVO DE ACTIVOS --}}
    <div class="row">
        @php
            $kpis = [
                ['label' => 'Valor Total Inventario', 'val' => $stats['items_total'], 'icon' => 'fa-warehouse', 'border' => 'primary', 'desc' => 'Ítems registrados'],
                ['label' => 'Riesgo de Desabastecimiento', 'val' => $stats['items_bajo_stock'], 'icon' => 'fa-exclamation-triangle', 'border' => 'warning', 'desc' => 'Artículos críticos'],
                ['label' => 'Operaciones en Campo', 'val' => $stats['prestamos_activos'], 'icon' => 'fa-hard-hat', 'border' => 'info', 'desc' => 'Equipos en préstamo'],
                ['label' => 'Transacciones del Día', 'val' => $stats['movimientos_hoy'], 'icon' => 'fa-exchange-alt', 'border' => 'success', 'desc' => 'Flujo de hoy'],
            ];
        @endphp

        @foreach($kpis as $k)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-left-lg border-{{ $k['border'] }} mb-4 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $k['border'] }} text-uppercase mb-1">
                                {{ $k['label'] }}
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $k['val'] }}</div>
                            <div class="text-muted smallest mt-1">{{ $k['desc'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $k['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        {{-- AUDITORÍA DE TRANSACCIONES --}}
        <div class="col-lg-8">
            <div class="card card-navy card-outline shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-navy">
                        <i class="fas fa-history mr-2 text-muted"></i>Registro de Transacciones Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-striped align-middle mb-0">
                            <thead class="thead-light small font-weight-bold text-muted">
                                <tr>
                                    <th class="pl-4">ARTÍCULO</th>
                                    <th class="text-center">VOLUMEN</th>
                                    <th>RESPONSABLE</th>
                                    <th class="text-right pr-4">HORA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosMovimientos as $m)
                                <tr>
                                    <td class="pl-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-{{ $m->tipo == 'Ingreso' ? 'success' : 'danger' }}-light mr-3">
                                                <i class="fas fa-{{ $m->tipo == 'Ingreso' ? 'arrow-down' : 'arrow-up' }} text-{{ $m->tipo == 'Ingreso' ? 'success' : 'danger' }} font-xs"></i>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold d-block text-dark">{{ $m->item->codigo }}</span>
                                                <span class="smallest text-muted text-uppercase">{{ Str::limit($m->item->descripcion, 35) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $m->tipo == 'Ingreso' ? 'badge-success' : 'badge-danger' }} px-3 py-1 font-weight-normal shadow-xs">
                                            {{ $m->tipo == 'Ingreso' ? '+' : '-' }}{{ $m->cantidad }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-muted small"><i class="far fa-user mr-1"></i> {{ $m->user->name }}</td>
                                    <td class="text-right pr-4 align-middle text-muted smallest font-weight-bold">{{ $m->fecha->format('H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-center border-0">
                    <a href="{{ route('movimientos.index') }}" class="small font-weight-bold text-navy">Ver Reporte Maestro de Movimientos <i class="fas fa-chevron-right ml-1"></i></a>
                </div>
            </div>
        </div>

        {{-- AUDITORÍA DE INVENTARIO CRÍTICO --}}
        <div class="col-lg-4">
            <div class="card card-danger card-outline shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i>Alertas de Reabastecimiento
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($stockCritico as $i)
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom hover-light">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger-light p-2 rounded-circle mr-3" style="width:40px; height:40px; line-height:24px; text-align:center;">
                                <i class="fas fa-box-open text-danger small"></i>
                            </div>
                            <div>
                                <span class="d-block font-weight-bold text-dark small">{{ $i->codigo }}</span>
                                <span class="smallest text-muted text-truncate d-inline-block" style="max-width: 150px;">{{ $i->descripcion }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-pill badge-danger shadow-xs">{{ $i->cantidad }} u.</span>
                            <span class="d-block smallest text-muted mt-1">Existencia</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                        <p class="small font-weight-bold">Todos los niveles de stock operan con normalidad</p>
                    </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-danger shadow-sm w-100 font-weight-bold">
                        Solicitud de Reposición
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    :root { --sergen-navy: #001f3f; --sergen-slate: #f8f9fc; }
    .text-navy { color: var(--sergen-navy) !important; }
    .btn-navy { background: var(--sergen-navy); color: #fff; }
    .btn-navy:hover { background: #001a35; color: #fff; }
    .card-navy.card-outline { border-top: 4px solid var(--sergen-navy); }
    .border-left-lg { border-left: 0.25rem solid !important; }
    .bg-navy-light { background: rgba(0, 31, 63, 0.05); }
    .bg-success-light { background: rgba(40, 167, 69, 0.1); }
    .bg-danger-light { background: rgba(220, 53, 69, 0.1); }
    .icon-circle { height: 2.5rem; width: 2.5rem; border-radius: 100%; display: flex; align-items: center; justify-content: center; }
    .smallest { font-size: 0.75rem; }
    .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.04)!important; }
    .hover-light:hover { background-color: #fcfcfc; }
    .card { border-radius: 10px; }
    .table thead th { border-bottom: 2px solid #edf2f9; }
</style>
@stop
