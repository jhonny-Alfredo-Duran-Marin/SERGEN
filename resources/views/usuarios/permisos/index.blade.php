@extends('adminlte::page')

@section('title', 'Gestión de Permisos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-key"></i> Gestión de Permisos
        </h1>

        {{-- Buscador --}}
        <form action="{{ route('permissions.index') }}" method="GET" class="d-none d-md-flex" role="search">
            <div class="input-group">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Buscar permiso...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit" title="Buscar">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($permissions->total()) }}</h3>
                    <p>Total Permisos</p>
                </div>
                <div class="icon"><i class="fas fa-key"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-list"></i> Lista de Permisos
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $permissions->total() }} permisos</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($permissions->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="60">#</th>
                                <th><i class="fas fa-tag"></i> Nombre del Permiso</th>
                                <th class="d-none d-sm-table-cell" width="160">
                                    <i class="fas fa-shield-alt"></i> Guard
                                </th>
                                {{-- SIN columna de acciones --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $p)
                                <tr>
                                    <td class="text-muted">
                                        {{ $loop->iteration + ($permissions->currentPage() - 1) * $permissions->perPage() }}
                                    </td>
                                    <td>
                                        {{ \App\Support\PermissionLabel::label($p->name) }}


                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge badge-info">{{ $p->guard_name ?? 'web' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No hay permisos registrados</p>
                </div>
            @endif
        </div>

        @if ($permissions->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{-- ÚNICA paginación (forzamos Bootstrap para evitar el "Showing 1 to ..." en inglés) --}}
                {{ $permissions->appends(['q' => request('q')])->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@stop
