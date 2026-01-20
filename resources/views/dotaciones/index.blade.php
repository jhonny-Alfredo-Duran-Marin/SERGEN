@extends('adminlte::page')

@section('title', 'Gestión de Dotaciones')

@section('content_header')
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="m-0">
                    <i class="fas fa-gift text-primary"></i> Gestión de Dotaciones
                </h1>
                <p class="text-muted mb-0">Control y seguimiento de dotaciones de equipos y materiales</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('dotaciones.create') }}" class="btn btn-success btn-lg shadow">
                    <i class="fas fa-plus-circle"></i> Nueva Dotación
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle"></i> <strong>¡Éxito!</strong> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Estadísticas Mejoradas -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-info shadow">
                <div class="inner">
                    <h3>{{ $dotaciones->total() }}</h3>
                    <p><strong>Total Dotaciones</strong></p>
                </div>
                <div class="icon">
                    <i class="fas fa-gift"></i>
                </div>
                <a href="{{ route('dotaciones.index') }}" class="small-box-footer">
                    Ver todas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-warning shadow">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'ABIERTA')->count() }}</h3>
                    <p><strong>Dotaciones Abiertas</strong></p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <a href="{{ route('dotaciones.index', ['estado' => 'ABIERTA']) }}" class="small-box-footer">
                    Ver abiertas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-success shadow">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'DEVUELTA')->count() }}</h3>
                    <p><strong>Dotaciones Devueltas</strong></p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('dotaciones.index', ['estado' => 'DEVUELTA']) }}" class="small-box-footer">
                    Ver devueltas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-primary shadow">
                <div class="inner">
                    <h3>{{ $dotaciones->where('estado_final', 'COMPLETADA')->count() }}</h3>
                    <p><strong>Dotaciones Completadas</strong></p>
                </div>
                <div class="icon">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <a href="{{ route('dotaciones.index', ['estado' => 'COMPLETADA']) }}" class="small-box-footer">
                    Ver completadas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros Modernos -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> <strong>Filtros de Búsqueda</strong>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dotaciones.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-search text-primary"></i> <strong>Buscar Persona:</strong>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       value="{{ request('q') }}"
                                       placeholder="Nombre de la persona">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-tag text-info"></i> <strong>Persona Específica:</strong>
                            </label>
                            <select name="persona_id" class="form-control select2">
                                <option value="">— Todas las personas —</option>
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
                            <label>
                                <i class="fas fa-calendar-alt text-success"></i> <strong>Desde:</strong>
                            </label>
                            <input type="date"
                                   name="desde"
                                   class="form-control"
                                   value="{{ request('desde') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-check text-warning"></i> <strong>Hasta:</strong>
                            </label>
                            <input type="date"
                                   name="hasta"
                                   class="form-control"
                                   value="{{ request('hasta') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="btn-group-vertical d-flex">
                            <button type="submit" class="btn btn-primary shadow-sm mb-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla Moderna -->
    <div class="card shadow">
        <div class="card-header bg-gradient-navy">
            <h3 class="card-title">
                <i class="fas fa-list"></i> <strong>Lista de Dotaciones</strong>
            </h3>
            <div class="card-tools">
                <span class="badge badge-light badge-lg">
                    <i class="fas fa-database"></i>
                    {{ $dotaciones->total() }} {{ Str::plural('registro', $dotaciones->total()) }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($dotaciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="60" class="text-center">#</th>
                                <th width="100">
                                    <i class="fas fa-hashtag"></i> ID
                                </th>
                                <th>
                                    <i class="fas fa-user"></i> Persona
                                </th>
                                <th width="180">
                                    <i class="fas fa-calendar"></i> Fecha y Hora
                                </th>
                                <th width="100" class="text-center">
                                    <i class="fas fa-boxes"></i> Ítems
                                </th>
                                <th width="150" class="text-center">
                                    <i class="fas fa-traffic-light"></i> Estado
                                </th>
                                <th width="260" class="text-center">
                                    <i class="fas fa-tools"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dotaciones as $d)
                                <tr>
                                    <td class="text-center text-muted font-weight-bold">
                                        {{ $loop->iteration + ($dotaciones->currentPage() - 1) * $dotaciones->perPage() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('dotaciones.show', $d) }}"
                                            class="badge badge-primary badge-lg">
                                            <i class="fas fa-link"></i> #{{ $d->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                     style="width: 35px; height: 35px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $d->persona->nombre }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-calendar-day text-primary"></i>
                                            <strong>{{ $d->fecha->format('d/m/Y') }}</strong>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="far fa-clock"></i> {{ $d->fecha->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info badge-lg">
                                            <i class="fas fa-box"></i> {{ $d->items->count() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $estados = [
                                                'ABIERTA' => ['class' => 'warning', 'icon' => 'folder-open'],
                                                'DEVUELTA' => ['class' => 'success', 'icon' => 'check-circle'],
                                                'COMPLETADA' => ['class' => 'primary', 'icon' => 'flag-checkered'],
                                            ];
                                            $estado = $estados[$d->estado_final] ?? ['class' => 'secondary', 'icon' => 'question'];
                                        @endphp
                                        <span class="badge badge-{{ $estado['class'] }} badge-lg">
                                            <i class="fas fa-{{ $estado['icon'] }}"></i>
                                            {{ $d->estado_final }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group d-flex justify-content-center" role="group">
                                            <a href="{{ route('dotaciones.show', $d) }}"
                                                class="btn btn-sm btn-info shadow-sm"
                                                data-toggle="tooltip"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('dotaciones.edit', $d) }}"
                                                class="btn btn-sm btn-warning shadow-sm"
                                                data-toggle="tooltip"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="{{ route('dotaciones.recibo', $d) }}"
                                                class="btn btn-sm btn-danger shadow-sm"
                                                target="_blank"
                                                data-toggle="tooltip"
                                                title="Generar PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger shadow-sm"
                                                onclick="confirmarEliminacion({{ $d->id }})"
                                                data-toggle="tooltip"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <form id="delete-form-{{ $d->id }}"
                                              action="{{ route('dotaciones.destroy', $d) }}"
                                              method="POST"
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No se encontraron dotaciones</h4>
                    <p class="text-muted">No hay dotaciones registradas con los criterios seleccionados</p>
                    <a href="{{ route('dotaciones.create') }}" class="btn btn-success btn-lg mt-3">
                        <i class="fas fa-plus-circle"></i> Crear Primera Dotación
                    </a>
                </div>
            @endif
        </div>

        @if ($dotaciones->hasPages())
            <div class="card-footer clearfix bg-light">
                <div class="float-right">
                    {{ $dotaciones->links() }}
                </div>
                <div class="float-left text-muted">
                    Mostrando {{ $dotaciones->firstItem() }} - {{ $dotaciones->lastItem() }} de {{ $dotaciones->total() }} registros
                </div>
            </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .small-box:hover {
            transform: translateY(-5px);
        }

        .small-box .inner {
            padding: 20px;
        }

        .small-box .icon {
            font-size: 80px;
            top: 15px;
            right: 20px;
            opacity: 0.3;
        }

        .badge-lg {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
        }

        .card {
            border-radius: 10px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .btn-group .btn {
            margin: 0 2px;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

            // Inicializar Select2 para persona
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: '— Seleccione una persona —',
                allowClear: true
            });
        });

        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Eliminar Dotación?',
                text: "Esta acción no se puede deshacer. Se eliminará permanentemente la dotación #" + id,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@stop
