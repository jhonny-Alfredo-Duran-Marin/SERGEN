@extends('adminlte::page')

@section('title', 'Gestión de Dotaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-gift"></i> Gestión de Dotaciones</h1>
        <a href="{{ route('dotaciones.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Dotación
        </a>
    </div>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $dotaciones->total() }}</h3>
                    <p>Total Dotaciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'ABIERTA')->count() }}</h3>
                    <p>Abiertas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'DEVUELTA')->count() }}</h3>
                    <p>Devueltas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'COMPLETADA')->count() }}</h3>
                    <p>Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-flag-checkered"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card card-primary card-outline collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dotaciones.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-search"></i> Buscar Persona:</label>
                            <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                                placeholder="Nombre de la persona">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Persona Específica:</label>
                            <select name="persona_id" class="form-control">
                                <option value="">— Todas —</option>
                                @foreach ($personas as $p)
                                    <option value="{{ $p->id }}" @selected(request('persona_id') == $p->id)>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Desde:</label>
                            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Hasta:</label>
                            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de dotaciones -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Dotaciones</h3>
            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $dotaciones->total() }} {{ $dotaciones->total() == 1 ? 'dotación' : 'dotaciones' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($dotaciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="80">ID</th>
                                <th><i class="fas fa-user"></i> Persona</th>
                                <th><i class="fas fa-calendar"></i> Fecha</th>
                                <th class="text-center"><i class="fas fa-box"></i> Items</th>
                                <th class="text-center"><i class="fas fa-toggle-on"></i> Estado</th>
                                <th width="220" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dotaciones as $d)
                                <tr>
                                    <td class="text-muted">
                                        {{ $loop->iteration + ($dotaciones->currentPage() - 1) * $dotaciones->perPage() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('dotaciones.show', $d) }}"
                                            class="text-primary font-weight-bold">
                                            <i class="fas fa-external-link-alt"></i> #{{ $d->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong>{{ $d->persona->nombre }}</strong>
                                    </td>
                                    <td>
                                        {{ $d->fecha }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $d->fecha }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            {{ $d->items->count() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $estadoBadge = [
                                                'ABIERTA' => ['class' => 'warning', 'icon' => 'folder-open'],
                                                'DEVUELTA' => ['class' => 'success', 'icon' => 'check-circle'],
                                                'COMPLETADA' => ['class' => 'primary', 'icon' => 'flag-checkered'],
                                            ];
                                            $badge = $estadoBadge[$d->estado_final] ?? [
                                                'class' => 'secondary',
                                                'icon' => 'question',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $badge['class'] }}">
                                            <i class="fas fa-{{ $badge['icon'] }}"></i>
                                            {{ $d->estado_final }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            {{-- 1. Ver detalles (Info) --}}
                                            <a href="{{ route('dotaciones.show', $d) }}"
                                                class="btn btn-sm btn-info mx-1 shadow-sm" data-toggle="tooltip"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- 2. Editar (Warning) --}}
                                            <a href="{{ route('dotaciones.edit', $d) }}"
                                                class="btn btn-sm btn-warning mx-1 shadow-sm" data-toggle="tooltip"
                                                title="Editar dotación">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- 3. Imprimir Recibo (Rojo/PDF) --}}
                                            <a href="{{ route('dotaciones.recibo', $d) }}"
                                                class="btn btn-sm btn-danger mx-1 shadow-sm" target="_blank"
                                                data-toggle="tooltip" title="Imprimir Recibo PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            {{-- 5. Eliminar (Formulario) --}}
                                            <form action="{{ route('dotaciones.destroy', $d) }}" method="POST"
                                                class="d-inline mx-1"
                                                onsubmit="return confirm('¿Estás seguro de eliminar la dotación #{{ $d->id }}?\n\nEsta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"
                                                    data-toggle="tooltip" title="Eliminar dotación">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay dotaciones registradas</p>
                    <a href="{{ route('dotaciones.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Crear primera dotación
                    </a>
                </div>
            @endif
        </div>
        @if ($dotaciones->hasPages())
            <div class="card-footer clearfix">
                {{ $dotaciones->links() }}
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Dotaciones</span>
                    <span class="info-box-number">Asigna items a personas de forma permanente y controla
                        devoluciones</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box>.inner {
            padding: 15px;
        }

        .small-box .icon {
            font-size: 70px;
            top: 10px;
            right: 15px;
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
