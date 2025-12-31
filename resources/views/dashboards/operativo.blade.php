@extends('adminlte::page')

@section('title', 'Resumen de Operaciones')

@section('content_header')
    <div class="row mb-2 border-bottom pb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-navy font-weight-bold">Resumen de Operaciones</h1>
            <p class="text-muted">Bienvenido al sistema Ser.Gen, {{ auth()->user()->name }}</p>
        </div>
        <div class="col-sm-6 text-right">
            <span class="text-muted small"><i class="far fa-calendar-alt mr-1"></i> {{ date('d/m/Y') }}</span>
        </div>
    </div>
@stop

@section('content')
    {{-- KPI CARDS --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm border-left border-primary">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-exchange-alt text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted font-weight-bold">PRÉSTAMOS VIGENTES</span>
                    <span class="info-box-number text-navy text-xl">{{ $stats['prestamos_activos'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm border-left border-warning">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-undo-alt text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted font-weight-bold">PENDIENTES RETORNO</span>
                    <span class="info-box-number text-xl">{{ $stats['devoluciones_pendientes'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm border-left border-info">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-boxes text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted font-weight-bold">CATÁLOGO DE ÍTEMS</span>
                    <span class="info-box-number text-xl">{{ $stats['items'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm border-left border-danger">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted font-weight-bold">BAJO STOCK</span>
                    <span class="info-box-number text-xl">{{ $stats['stock_bajo'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACCIONES EJECUTIVAS --}}
    <div class="mb-4">
        <h5 class="text-navy font-weight-bold mb-3"><i class="fas fa-rocket mr-2 text-primary small"></i> Acciones Rápidas</h5>
        <div class="d-flex flex-wrap gap-2">
            @can('prestamos.create')
                <a href="{{ route('prestamos.create') }}" class="btn btn-navy shadow-sm mr-2 mb-2 px-4 border-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Registrar Préstamo
                </a>
            @endcan
            @can('items.create')
                <a href="{{ route('items.create') }}" class="btn btn-light shadow-sm mr-2 mb-2 border">
                    <i class="fas fa-barcode mr-2"></i> Alta de Ítem
                </a>
            @endcan
            @can('proyectos.create')
                <a href="{{ route('proyectos.create') }}" class="btn btn-light shadow-sm mr-2 mb-2 border text-muted">Nuevo Proyecto</a>
            @endcan
            @can('roles.view')
                <a href="{{ route('roles.index') }}" class="btn btn-light shadow-sm mr-2 mb-2 border text-muted">Gestión Roles</a>
            @endcan
        </div>
    </div>

    <div class="row">
        {{-- OPERACIONES RECIENTES --}}
        <div class="col-lg-8">
            <div class="card card-navy card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold text-navy">Control de Salidas Recientes</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">Mostrando últimos {{ $ultimosPrestamos->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-valign-middle table-striped table-hover mb-0">
                            <thead class="bg-light small uppercase">
                                <tr>
                                    <th class="pl-3">Control</th>
                                    <th>Asignación / Destino</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Ítems</th>
                                    <th class="text-right pr-3">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($ultimosPrestamos as $p)
                                <tr>
                                    <td class="pl-3 py-3">
                                        <strong class="text-navy">{{ $p->codigo }}</strong>
                                        <small class="text-muted d-block">{{ \Illuminate\Support\Carbon::parse($p->fecha)->format('d M, Y') }}</small>
                                    </td>
                                    <td>
                                        @if($p->tipo_destino === 'Persona')
                                            <span class="text-navy font-weight-bold font-italic">Persona:</span> {{ $p->persona?->nombre ?? '—' }}
                                        @else
                                            <span class="text-navy font-weight-bold font-italic">Proyecto:</span> {{ $p->proyecto?->codigo ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge px-3 py-1 shadow-sm {{ $p->estado === 'Completo' ? 'badge-success' : ($p->estado==='Observado'?'badge-danger':'badge-primary') }}">
                                            {{ strtoupper($p->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">{{ $p->detalles->count() }}</td>
                                    <td class="text-right pr-3 align-middle">
                                        <a href="{{ route('prestamos.show',$p) }}" class="btn btn-xs btn-outline-navy shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> DETALLE
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4 font-italic">No existen registros operacionales recientes.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- AUDITORÍA DE INVENTARIO --}}
        <div class="col-lg-4">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold text-navy">Alertas de Almacén</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($alertasStock as $it)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold text-dark">{{ $it->descripcion }}</span><br>
                                    <small class="text-muted text-uppercase">{{ $it->codigo }} | {{ $it->medida?->simbolo ?? 'u' }}</small>
                                </div>
                                <span class="badge {{ $it->cantidad < 3 ? 'bg-danger' : 'bg-warning' }} rounded-pill px-3 shadow-sm">
                                    {{ $it->cantidad }}
                                </span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted font-italic">Niveles de stock estables.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .bg-navy { background-color: #001f3f !important; color: white !important; }
    .text-navy { color: #001f3f !important; }
    .btn-navy { background-color: #001f3f; color: white; border-bottom: 3px solid #000; }
    .btn-navy:hover { background-color: #001a35; color: white; }
    .card-navy.card-outline { border-top: 3px solid #001f3f; }
    .table-valign-middle td { vertical-align: middle; }
    .small-box { transition: transform .2s ease-in-out; }
    .small-box:hover { transform: translateY(-5px); }
</style>
@stop
