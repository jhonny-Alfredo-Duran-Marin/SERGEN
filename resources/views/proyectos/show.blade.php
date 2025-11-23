@extends('adminlte::page')

@section('title', 'Detalle del Proyecto')

@section('content_header')
    <h1>
        <i class="fas fa-project-diagram"></i> Proyecto:
        <span class="text-primary">{{ $proyecto->codigo }}</span>
    </h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Información Principal -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información Principal
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $proyecto->estado === 'Abierto' ? 'primary' : 'dark' }}">
                            <i class="fas fa-{{ $proyecto->estado === 'Abierto' ? 'folder-open' : 'folder' }}"></i>
                            {{ $proyecto->estado }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">
                            <i class="fas fa-barcode text-primary"></i> Código:
                        </dt>
                        <dd class="col-sm-9">
                            <strong class="text-lg">{{ $proyecto->codigo }}</strong>
                        </dd>

                        <dt class="col-sm-3">
                            <i class="fas fa-align-left text-info"></i> Descripción:
                        </dt>
                        <dd class="col-sm-9">
                            {{ $proyecto->descripcion }}
                        </dd>

                        <dt class="col-sm-3">
                            <i class="fas fa-building text-primary"></i> Empresa:
                        </dt>
                        <dd class="col-sm-9">
                            <strong>{{ $proyecto->empresa }}</strong>
                        </dd>

                        <dt class="col-sm-3">
                            <i class="fas fa-file-contract text-warning"></i> Orden de Compra:
                        </dt>
                        <dd class="col-sm-9">
                            @if($proyecto->orden_compra)
                                <span class="badge badge-light">{{ $proyecto->orden_compra }}</span>
                            @else
                                <span class="text-muted">No especificada</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">
                            <i class="fas fa-map-marker-alt text-danger"></i> Sitio:
                        </dt>
                        <dd class="col-sm-9">
                            @if($proyecto->sitio)
                                {{ $proyecto->sitio }}
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Fechas -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Cronograma
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha de Inicio</span>
                                    <span class="info-box-number">
                                        {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'No definida' }}
                                    </span>
                                    @if($proyecto->fecha_inicio)
                                        <small class="text-muted">
                                            {{ $proyecto->fecha_inicio->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha de Fin</span>
                                    <span class="info-box-number">
                                        {{ $proyecto->fecha_fin?->format('d/m/Y') ?? 'No definida' }}
                                    </span>
                                    @if($proyecto->fecha_fin)
                                        <small class="text-muted">
                                            {{ $proyecto->fecha_fin->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($proyecto->fecha_inicio && $proyecto->fecha_fin)
                        @php
                            $duracion = $proyecto->fecha_inicio->diffInDays($proyecto->fecha_fin);
                            $transcurrido = now()->between($proyecto->fecha_inicio, $proyecto->fecha_fin)
                                ? $proyecto->fecha_inicio->diffInDays(now())
                                : 0;
                            $progreso = $duracion > 0 ? min(100, ($transcurrido / $duracion) * 100) : 0;
                        @endphp

                        <div class="mt-3">
                            <label>Duración del proyecto: <strong>{{ $duracion }} días</strong></label>
                            <div class="progress">
                                <div class="progress-bar bg-{{ $progreso < 50 ? 'success' : ($progreso < 80 ? 'warning' : 'danger') }}"
                                     role="progressbar"
                                     style="width: {{ $progreso }}%">
                                    {{ number_format($progreso, 0) }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                Días transcurridos: {{ $transcurrido }} / {{ $duracion }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información Financiera -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Información Financiera
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-success mb-0">
                            <i class="fas fa-dollar-sign"></i>
                            {{ number_format($proyecto->monto, 2, '.', ',') }}
                        </h2>
                        <small class="text-muted">Monto del Proyecto</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-file-invoice"></i> Estado de Facturación:
                        </span>
                        <span class="badge badge-{{ $proyecto->es_facturado ? 'success' : 'warning' }} badge-lg">
                            @if($proyecto->es_facturado)
                                <i class="fas fa-check-circle"></i> FACTURADO
                            @else
                                <i class="fas fa-clock"></i> PENDIENTE
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Estado del Proyecto -->
            <div class="card card-{{ $proyecto->estado === 'Abierto' ? 'primary' : 'dark' }} card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-toggle-on"></i> Estado del Proyecto
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-{{ $proyecto->estado === 'Abierto' ? 'folder-open' : 'folder' }} fa-3x text-{{ $proyecto->estado === 'Abierto' ? 'primary' : 'dark' }}"></i>
                    </div>
                    <h3 class="text-{{ $proyecto->estado === 'Abierto' ? 'primary' : 'dark' }}">
                        {{ $proyecto->estado }}
                    </h3>
                    <p class="text-muted mb-0">
                        @if($proyecto->estado === 'Abierto')
                            El proyecto está activo y en desarrollo
                        @else
                            El proyecto ha sido finalizado
                        @endif
                    </p>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Acciones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('proyectos.update')
                        <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Editar Proyecto
                        </a>
                        @endcan

                        <a href="{{ route('proyectos.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>

                        @can('proyectos.delete')
                        <button type="button"
                                class="btn btn-danger btn-block"
                                data-toggle="modal"
                                data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Eliminar Proyecto
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Sistema -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-database"></i> Información del Sistema</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-2">ID del Proyecto:</dt>
                        <dd class="col-sm-4">{{ $proyecto->id }}</dd>

                        <dt class="col-sm-2">Fecha de Registro:</dt>
                        <dd class="col-sm-4">{{ $proyecto->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Última Actualización:</dt>
                        <dd class="col-sm-4">{{ $proyecto->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Actualizado hace:</dt>
                        <dd class="col-sm-4">{{ $proyecto->updated_at?->diffForHumans() ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    @can('proyectos.delete')
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el proyecto <strong>{{ $proyecto->codigo }}</strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong>
                        <ul class="mb-0">
                            <li>Se perderá toda la información del proyecto</li>
                            <li>Esta acción no se puede deshacer</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5em 0.75em;
    }
    .d-grid {
        display: grid;
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@stop
