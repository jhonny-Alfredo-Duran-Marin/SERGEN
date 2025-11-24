@extends('adminlte::page')

@section('title', 'Gestión de Incidentes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-exclamation-triangle"></i> Gestión de Incidentes</h1>
        <a href="{{ route('incidentes.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Incidente
        </a>
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
                    <h3>{{ $incidentes->total() }}</h3>
                    <p>Total Incidentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $incidentes->where('estado', 'ACTIVO')->count() }}</h3>
                    <p>Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $incidentes->where('estado', 'EN_PROCESO')->count() }}</h3>
                    <p>En Proceso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sync"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $incidentes->where('estado', 'COMPLETADO')->count() }}</h3>
                    <p>Completados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de incidentes -->
    <div class="card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Incidentes</h3>
            <div class="card-tools">
                <span class="badge badge-danger">
                    {{ $incidentes->total() }} {{ $incidentes->total() == 1 ? 'incidente' : 'incidentes' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($incidentes->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">#</th>
                            <th><i class="fas fa-barcode"></i> Código</th>
                            <th><i class="fas fa-user"></i> Persona</th>
                            <th><i class="fas fa-tag"></i> Tipo</th>
                            <th class="text-center"><i class="fas fa-toggle-on"></i> Estado</th>
                            <th><i class="fas fa-calendar"></i> Fecha</th>
                            <th width="250" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentes as $i)
                        <tr>
                            <td class="text-muted">
                                {{ $loop->iteration + ($incidentes->currentPage() - 1) * $incidentes->perPage() }}
                            </td>
                            <td>
                                <a href="{{ route('incidentes.show', $i) }}"
                                   class="text-danger font-weight-bold">
                                    <i class="fas fa-external-link-alt"></i> {{ $i->codigo }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $i->persona->nombre }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-{{ $i->tipo === 'PRESTAMO' ? 'primary' : 'info' }}">
                                    <i class="fas fa-{{ $i->tipo === 'PRESTAMO' ? 'hand-holding' : 'gift' }}"></i>
                                    {{ $i->tipo }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $estadoBadge = [
                                        'ACTIVO' => ['class' => 'warning', 'icon' => 'exclamation-circle'],
                                        'EN_PROCESO' => ['class' => 'info', 'icon' => 'sync'],
                                        'COMPLETADO' => ['class' => 'success', 'icon' => 'check-circle']
                                    ];
                                    $badge = $estadoBadge[$i->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                                @endphp
                                <span class="badge badge-{{ $badge['class'] }}">
                                    <i class="fas fa-{{ $badge['icon'] }}"></i>
                                    {{ $i->estado }}
                                </span>
                            </td>
                            <td>
                                {{ $i->fecha_incidente }}
                                <br>
                                <small class="text-muted">
                                    {{ $i->fecha_incidente }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('incidentes.show', $i) }}"
                                       class="btn btn-sm btn-info"
                                       data-toggle="tooltip"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('incidentes.edit', $i) }}"
                                       class="btn btn-sm btn-warning"
                                       data-toggle="tooltip"
                                       title="Editar incidente">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($i->estado !== 'COMPLETADO')
                                    <a href="{{ route('incidentes.devolver', $i) }}"
                                       class="btn btn-sm btn-primary"
                                       data-toggle="tooltip"
                                       title="Registrar devolución">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                    @endif

                                    <form action="{{ route('incidentes.destroy', $i) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar el incidente {{ $i->codigo }}?\n\nEsta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                data-toggle="tooltip"
                                                title="Eliminar incidente">
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
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay incidentes registrados</p>
                <a href="{{ route('incidentes.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Registrar primer incidente
                </a>
            </div>
            @endif
        </div>
        @if($incidentes->hasPages())
        <div class="card-footer clearfix">
            {{ $incidentes->links() }}
        </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Incidentes</span>
                    <span class="info-box-number">Registra y controla pérdidas, daños y devoluciones de items en préstamos y dotaciones</span>
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
</style>
@stop

@section('js')
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
