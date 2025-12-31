@extends('adminlte::page')

@section('title', 'Gestión de Préstamos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 text-navy font-weight-bold">
            <i class="fas fa-truck-loading mr-2"></i>Gestión de Préstamos
        </h1>
        <a href="{{ route('prestamos.create') }}" class="btn btn-success btn-lg shadow-sm">
            <i class="fas fa-plus-circle mr-1"></i> Nuevo Préstamo
        </a>
    </div>
@stop

@section('content')
    {{-- CARDS DE ESTADÍSTICAS --}}
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Préstamos</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['completos'] }}</h3>
                    <p>Devoluciones Completas</p>
                </div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['activos'] }}</h3>
                    <p>Préstamos Activos</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['observados'] }}</h3>
                    <p>Con Incidentes</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    {{-- FILTROS Y BÚSQUEDA --}}
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small font-weight-bold text-muted">BÚSQUEDA RÁPIDA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-right-0"><i
                                class="fas fa-search text-primary"></i></span>
                        <input type="text" id="live-search" class="form-control border-left-0"
                            placeholder="Código, Persona, Proyecto...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted">ESTADO</label>
                    <select class="form-select w-100" id="filtro-estado">
                        <option value="">Todos</option>
                        <option value="Activo">Activo</option>
                        <option value="Observado">Observado</option>
                        <option value="Completo">Completo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted">FECHA</label>
                    <input type="date" class="form-control" id="filtro-fecha">
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-navy d-flex align-items-center">
            <h3 class="card-title mr-3"><i class="fas fa-list mr-2"></i>Lista de Registros</h3>
            <span class="badge badge-light shadow-sm">Mostrando <span id="total-mostrando">{{ $prestamos->count() }}</span>
                de {{ $prestamos->total() }}</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabla-prestamos">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4">Código</th>
                            <th>Fecha</th>
                            <th>Persona / Destino</th>
                            <th class="text-center">Kits Asignados</th>
                            <th class="text-center">Ítems Sueltos</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center pr-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos as $p)
                            <tr data-estado="{{ $p->estado }}" data-fecha="{{ $p->fecha->format('Y-m-d') }}">
                                <td class="pl-4">
                                    <a href="{{ route('prestamos.show', $p) }}" class="font-weight-bold text-primary">
                                        {{ $p->codigo }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $p->fecha->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $p->fecha->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <i
                                                class="fas {{ $p->proyecto ? 'fa-hard-hat text-success' : 'fa-user-circle text-info' }} fa-lg"></i>
                                        </div>
                                        <div>
                                            <span class="d-block font-weight-bold">{{ $p->persona->nombre }}</span>
                                            @if ($p->proyecto)
                                                <span class="badge badge-pill bg-light border text-muted small">
                                                    <i class="fas fa-briefcase fa-xs mr-1"></i>{{ $p->proyecto->codigo }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{-- INDICADOR DE KITS --}}
                                    @if ($p->kits->count() > 0)
                                        <div class="d-flex flex-wrap justify-content-center" style="gap: 2px;">
                                            @foreach ($p->kits as $kit)
                                                <span class="badge badge-success shadow-xs" title="{{ $kit->nombre }}">
                                                    <i class="fas fa-box fa-xs mr-1"></i>{{ $kit->codigo }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small italic">Ninguno</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-secondary px-3">
                                        {{ $p->detalles->count() }} <small>ítem(s)</small>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge py-2 px-3 badge-{{ $p->estado == 'Activo' ? 'primary' : ($p->estado == 'Observado' ? 'danger' : 'success') }}">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center acciones-gap">

                                        {{-- Ver --}}
                                        <a href="{{ route('prestamos.show', $p) }}" class="btn btn-sm btn-light shadow-sm"
                                            title="Ver Detalle">
                                            <i class="fas fa-eye text-navy"></i>
                                        </a>

                                        {{-- Editar (SIEMPRE visible, excepto si quieres bloquearlo) --}}
                                        @if ($p->estado !== 'Completo')
                                            <a href="{{ route('prestamos.edit', $p) }}"
                                                class="btn btn-sm btn-warning shadow-sm" title="Editar Préstamo">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif


                                        {{-- Devolución (solo si NO está completo) --}}
                                        @if ($p->estado !== 'Completo')
                                            <a href="{{ route('devoluciones.create', $p) }}"
                                                class="btn btn-sm btn-success shadow-sm" title="Registrar Devolución">
                                                <i class="fas fa-undo-alt"></i>
                                            </a>
                                        @endif

                                        {{-- Imprimir --}}
                                        <a href="{{ route('prestamos.imprimir', $p->id) }}" target="_blank"
                                            class="btn btn-sm btn-primary shadow-sm" title="Imprimir Recibo">
                                            <i class="fas fa-print"></i>
                                        </a>

                                    </div>
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-center mt-2">
                {!! $prestamos->onEachSide(1)->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .text-navy {
            color: #001f3f;
        }

        .bg-navy {
            background-color: #001f3f;
            color: white;
        }

        .table td {
            vertical-align: middle !important;
        }

        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-weight: 600;
        }

        .btn-group.gap-1>.btn {
            margin-right: 4px;
        }

        .btn-group.gap-1>.btn:last-child {
            margin-right: 0;
        }

        .acciones-gap>.btn {
            margin: 0 4px;
            border-radius: 6px !important;
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('live-search');
            const filtroEstado = document.getElementById('filtro-estado');
            const filtroFecha = document.getElementById('filtro-fecha');
            const filas = document.querySelectorAll('#tabla-prestamos tbody tr');
            const totalMostrando = document.getElementById('total-mostrando');

            const filtrar = () => {
                const term = searchInput.value.toLowerCase().trim();
                const estado = filtroEstado.value;
                const fecha = filtroFecha.value;

                let visibles = 0;

                filas.forEach(fila => {
                    const contenido = fila.textContent.toLowerCase();
                    const dataEstado = fila.dataset.estado;
                    const dataFecha = fila.dataset.fecha;

                    const matchTerm = term === '' || contenido.includes(term);
                    const matchEstado = estado === '' || dataEstado === estado;
                    const matchFecha = fecha === '' || dataFecha === fecha;

                    if (matchTerm && matchEstado && matchFecha) {
                        fila.style.display = '';
                        visibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                totalMostrando.textContent = visibles;
            };

            searchInput.addEventListener('input', filtrar);
            filtroEstado.addEventListener('change', filtrar);
            filtroFecha.addEventListener('change', filtrar);
        });
    </script>
@stop
