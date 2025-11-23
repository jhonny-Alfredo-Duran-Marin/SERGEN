@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" title="OK">{{ session('status') }}</x-adminlte-alert>
    @endif

    <p class="mb-3">¡Estás autenticado, {{ auth()->user()->name ?? auth()->user()->email }}!</p>

    {{-- KPIs / Small boxes --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['prestamos_activos'] ?? 0 }}</h3>
                    <p>Préstamos activos</p>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
                @can('prestamos.view')
                <a href="{{ route('prestamos.index') }}" class="small-box-footer">Ver préstamos <i class="fas fa-arrow-circle-right"></i></a>
                @endcan
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['devoluciones_pendientes'] ?? 0 }}</h3>
                    <p>Devoluciones pendientes</p>
                </div>
                <div class="icon"><i class="fas fa-undo-alt"></i></div>
                @can('prestamos.view')
                <a href="{{ route('prestamos.index') }}" class="small-box-footer">Gestionar <i class="fas fa-arrow-circle-right"></i></a>
                @endcan
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['items'] ?? 0 }}</h3>
                    <p>Items</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                @can('items.view')
                <a href="{{ route('items.index') }}" class="small-box-footer">Ver items <i class="fas fa-arrow-circle-right"></i></a>
                @endcan
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['stock_bajo'] ?? 0 }}</h3>
                    <p>Stock bajo (&lt; 5)</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                @can('items.view')
                <a href="{{ route('items.index') }}" class="small-box-footer">Revisar <i class="fas fa-arrow-circle-right"></i></a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        @can('prestamos.create')
            <a href="{{ route('prestamos.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Nuevo préstamo</a>
        @endcan
        @can('items.create')
            <a href="{{ route('items.create') }}" class="btn btn-outline-secondary"><i class="fas fa-plus me-1"></i> Nuevo item</a>
        @endcan>
        @can('proyectos.create')
            <a href="{{ route('proyectos.create') }}" class="btn btn-outline-secondary">Nuevo proyecto</a>
        @endcan
        @can('personas.create')
            <a href="{{ route('personas.create') }}" class="btn btn-outline-secondary">Nueva persona</a>
        @endcan
        @can('roles.view') <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Roles</a> @endcan
        @can('permissions.view') <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Permisos</a> @endcan
    </div>

    <div class="row">
        {{-- Últimos préstamos --}}
        <div class="col-lg-8">
            <x-adminlte-card title="Últimos préstamos" theme="light" icon="fas fa-exchange-alt">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Destino</th>
                                <th>Estado</th>
                                <th class="text-end">Ítems</th>
                                <th style="width:120px"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($ultimosPrestamos as $p)
                            <tr>
                                <td><strong>{{ $p->codigo }}</strong></td>
                                <td>{{ \Illuminate\Support\Carbon::parse($p->fecha)->format('Y-m-d') }}</td>
                                <td>
                                    @if($p->tipo_destino === 'Persona')
                                        Persona: {{ $p->persona?->nombre ?? '—' }}
                                    @elseif($p->tipo_destino === 'Proyecto')
                                        Proyecto: {{ $p->proyecto?->codigo ?? '—' }}
                                    @else
                                        Otro
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $p->estado === 'Completo' ? 'bg-success' : ($p->estado==='Observado'?'bg-danger':'bg-primary') }}">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                                <td class="text-end">{{ $p->detalles->count() }}</td>
                                <td class="text-end">
                                    @can('prestamos.view')
                                    <a href="{{ route('prestamos.show',$p) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-3">Sin registros</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>

        {{-- Alertas de stock --}}
        <div class="col-lg-4">
            <x-adminlte-card title="Alertas de stock" theme="light" icon="fas fa-exclamation-triangle">
                <ul class="list-group mb-0">
                    @forelse($alertasStock as $it)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong>{{ $it->codigo }}</strong> — {{ $it->descripcion }}
                                <small class="text-muted">({{ $it->medida?->simbolo ?? 'u' }})</small>
                            </span>
                            <span class="badge {{ $it->cantidad < 3 ? 'bg-danger' : 'bg-warning' }} rounded-pill">
                                {{ $it->cantidad }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Sin alertas</li>
                    @endforelse
                </ul>
            </x-adminlte-card>
        </div>
    </div>
@stop
