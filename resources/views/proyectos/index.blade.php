@extends('adminlte::page')

@section('title', 'Gestión de Proyectos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-project-diagram"></i> Gestión de Proyectos</h1>
        @can('proyectos.create')
        <a href="{{ route('proyectos.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Proyecto
        </a>
        @endcan
    </div>
@stop

@section('content')
    @if(session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $proyectos->total() }}</h3>
                    <p>Total Proyectos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $proyectos->where('estado', 'Abierto')->count() }}</h3>
                    <p>Abiertos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $proyectos->where('es_facturado', true)->count() }}</h3>
                    <p>Facturados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($proyectos->sum('monto'), 0) }}</h3>
                    <p>Monto Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
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
            <form method="GET" action="{{ route('proyectos.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-search"></i> Buscar:</label>
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   value="{{ request('q') }}"
                                   placeholder="Código, empresa, sitio o descripción">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Estado:</label>
                            <select name="estado" class="form-control">
                                <option value="">— Todos —</option>
                                <option value="Abierto" @selected(request('estado')==='Abierto')>Abierto</option>
                                <option value="Cerrado" @selected(request('estado')==='Cerrado')>Cerrado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-file-invoice"></i> Facturación:</label>
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox"
                                       name="facturado"
                                       value="1"
                                       class="custom-control-input"
                                       id="facturado"
                                       {{ request('facturado')==='1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="facturado">
                                    Solo facturados
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('proyectos.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de proyectos -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Proyectos</h3>
            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $proyectos->total() }} {{ $proyectos->total() == 1 ? 'proyecto' : 'proyectos' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($proyectos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">#</th>
                            <th><i class="fas fa-barcode"></i> Código</th>
                            <th><i class="fas fa-building"></i> Empresa</th>
                            <th><i class="fas fa-map-marker-alt"></i> Sitio</th>
                            <th class="text-right"><i class="fas fa-dollar-sign"></i> Monto</th>
                            <th class="text-center" width="100"><i class="fas fa-file-invoice"></i> Fact.</th>
                            <th class="text-center" width="100"><i class="fas fa-toggle-on"></i> Estado</th>
                            <th class="text-center"><i class="fas fa-calendar"></i> Fechas</th>
                            <th class="text-center" width="200"><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proyectos as $p)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($proyectos->currentPage() - 1) * $proyectos->perPage() }}</td>
                            <td>
                                <a href="{{ route('proyectos.show', $p) }}" class="text-primary font-weight-bold">
                                    <i class="fas fa-external-link-alt"></i> {{ $p->codigo }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $p->empresa }}</strong>
                                @if($p->orden_compra)
                                    <br><small class="text-muted">
                                        <i class="fas fa-file-contract"></i> OC: {{ $p->orden_compra }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($p->sitio)
                                    <span class="text-info">
                                        <i class="fas fa-map-marker-alt"></i> {{ $p->sitio }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <strong class="text-success">
                                    ${{ number_format($p->monto, 2, '.', ',') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $p->es_facturado ? 'success' : 'secondary' }}">
                                    <i class="fas fa-{{ $p->es_facturado ? 'check' : 'times' }}"></i>
                                    {{ $p->es_facturado ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $p->estado === 'Abierto' ? 'primary' : 'dark' }}">
                                    <i class="fas fa-{{ $p->estado === 'Abierto' ? 'folder-open' : 'folder' }}"></i>
                                    {{ $p->estado }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small>
                                    <i class="fas fa-calendar-alt text-success"></i>
                                    {{ $p->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                    <br>
                                    <i class="fas fa-calendar-check text-danger"></i>
                                    {{ $p->fecha_fin?->format('d/m/Y') ?? '—' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('proyectos.show', $p) }}"
                                       class="btn btn-sm btn-info"
                                       data-toggle="tooltip"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('proyectos.update')
                                    <a href="{{ route('proyectos.edit', $p) }}"
                                       class="btn btn-sm btn-warning"
                                       data-toggle="tooltip"
                                       title="Editar proyecto">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('proyectos.delete')
                                    <form action="{{ route('proyectos.destroy', $p) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar el proyecto {{ $p->codigo }}?\n\nEsta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                data-toggle="tooltip"
                                                title="Eliminar proyecto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="4" class="text-right"><strong>Totales:</strong></td>
                            <td class="text-right">
                                <strong class="text-success">
                                    ${{ number_format($proyectos->sum('monto'), 2, '.', ',') }}
                                </strong>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay proyectos registrados</p>
                @can('proyectos.create')
                <a href="{{ route('proyectos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear primer proyecto
                </a>
                @endcan
            </div>
            @endif
        </div>
        @if($proyectos->hasPages())
        <div class="card-footer clearfix">
            {{ $proyectos->links() }}
        </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Proyectos</span>
                    <span class="info-box-number">Administra los proyectos, controla estados, fechas y montos de facturación</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .small-box > .inner {
        padding: 15px;
    }
    .small-box .icon {
        font-size: 70px;
        top: 10px;
        right: 15px;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
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
