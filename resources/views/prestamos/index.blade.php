@extends('adminlte::page')

@section('title', 'Préstamos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="m-0">
        <i class="fas fa-truck-loading text-primary"></i>
        Gestión de Préstamos
    </h1>
    <a href="{{ route('prestamos.create') }}" class="btn btn-success btn-lg shadow">
        <i class="fas fa-plus"></i> Nuevo Préstamo
    </a>
</div>
@stop

@section('content')
{{-- CARDS DE ESTADÍSTICAS --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-clipboard-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Préstamos</span>
                <span class="info-box-number">{{ $stats['total'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completos</span>
                <span class="info-box-number">{{ $stats['completos'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Activos</span>
                <span class="info-box-number">{{ $stats['activos'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Con Incidentes</span>
                <span class="info-box-number">{{ $stats['observados'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- BÚSQUEDA RÁPIDA --}}
<div class="card card-outline card-primary mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">
            <i class="fas fa-search"></i> Búsqueda Rápida
        </h3>
    </div>
    <div class="card-body">
        <div class="input-group input-group-lg">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="live-search" class="form-control"
                   placeholder="Buscar por código, persona, proyecto, kit..."
                   autocomplete="off">
            <button type="button" id="btn-limpiar" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> Limpiar
            </button>
        </div>
        <small class="text-muted">
            <i class="fas fa-info-circle"></i> La búsqueda se realiza en tiempo real sin recargar la página
        </small>
    </div>
</div>

{{-- FILTROS AVANZADOS --}}
<div class="card card-outline card-info mb-4">
    <div class="card-header bg-info text-white" data-card-widget="collapse">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> Filtros Avanzados
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="form-select" id="filtro-estado">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Observado">Observado</option>
                    <option value="Completo">Completo</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filtro-kit">
                    <option value="">Todos los kits</option>
                    @foreach(\App\Models\KitEmergencia::orderBy('nombre')->get() as $k)
                        <option value="{{ $k->id }}">{{ $k->codigo }} - {{ $k->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="date" class="form-control" id="filtro-fecha" placeholder="Fecha">
            </div>
        </div>
    </div>
</div>

{{-- TABLA BRUTAL --}}
<div class="card">
    <div class="card-header bg-gradient-primary text-white">
        <h3 class="card-title">
            <i class="fas fa-list"></i> Lista de Préstamos
        </h3>
        <div class="card-tools">
            <span class="badge badge-light">
                Mostrando <strong id="total-mostrando">{{ $prestamos->count() }}</strong> de {{ $prestamos->total() }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabla-prestamos">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Fecha</th>
                        <th>Persona / Proyecto</th>
                        <th>KIT</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-center">Pendiente</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestamos as $i => $p)
                        <tr data-estado="{{ $p->estado }}"
                            data-kit="{{ $p->kit_emergencia_id }}"
                            data-fecha="{{ $p->fecha->format('Y-m-d') }}">
                            <td>{{ $prestamos->firstItem() + $i }}</td>
                            <td>
                                <a href="{{ route('prestamos.show', $p) }}" class="text-primary font-weight-bold">
                                    {{ $p->codigo }}
                                </a>
                            </td>
                            <td>
                                <small>{{ $p->fecha->format('d/m/Y') }}</small><br>
                                <span class="text-muted text-xs">{{ $p->fecha->diffForHumans() }}</span>
                            </td>
                            <td>
                                @if($p->persona)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-info mr-2"></i>
                                        <strong>{{ $p->persona->nombre }}</strong>
                                    </div>
                                @elseif($p->proyecto)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-project-diagram text-success mr-2"></i>
                                        <strong>{{ $p->proyecto->codigo }}</strong>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($p->kit)
                                    <span class="badge badge-success">
                                        <i class="fas fa-box"></i> {{ $p->kit->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin kit</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info badge-pill">{{ $p->detalles->count() }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $pendiente = $p->detalles->sum(fn($d) => $d->cantidad_prestada - $d->cantidad_devuelta);
                                @endphp
                                @if($pendiente > 0)
                                    <span class="badge badge-danger">{{ $pendiente }}</span>
                                @else
                                    <i class="fas fa-check text-success"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->estado === 'Completo')
                                    <span class="badge badge-success">Completo</span>
                                @elseif($p->estado === 'Observado')
                                    <span class="badge badge-danger">Observado</span>
                                @else
                                    <span class="badge badge-warning">Activo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('prestamos.show', $p) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($p->estado !== 'Completo')
                                        <a href="{{ route('devoluciones.create', $p) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer clearfix">
        {{ $prestamos->appends(request()->query())->links() }}
    </div>
</div>
@stop

@section('css')
<style>
    .info-box { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.075) !important; }
    #live-search { font-size: 1.1rem; }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('live-search');
    const limpiarBtn = document.getElementById('btn-limpiar');
    const filas = document.querySelectorAll('#tabla-prestamos tbody tr');
    const totalMostrando = document.getElementById('total-mostrando');

    const filtrar = () => {
        const term = searchInput.value.toLowerCase().trim();
        const estado = document.getElementById('filtro-estado')?.value || '';
        const kit = document.getElementById('filtro-kit')?.value || '';
        const fecha = document.getElementById('filtro-fecha')?.value || '';

        let visibles = 0;

        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            const matchSearch = term === '' || texto.includes(term);
            const matchEstado = estado === '' || fila.dataset.estado === estado;
            const matchKit = kit === '' || fila.dataset.kit == kit;
            const matchFecha = fecha === '' || fila.dataset.fecha === fecha;

            if (matchSearch && matchEstado && matchKit && matchFecha) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        totalMostrando.textContent = visibles;
    };

    searchInput?.addEventListener('input', filtrar);
    limpiarBtn?.addEventListener('click', () => {
        searchInput.value = '';
        document.getElementById('filtro-estado').value = '';
        document.getElementById('filtro-kit').value = '';
        document.getElementById('filtro-fecha').value = '';
        filtrar();
    });

    document.querySelectorAll('#filtro-estado, #filtro-kit, #filtro-fecha').forEach(el => {
        el.addEventListener('change', filtrar);
    });

    // Inicial
    filtrar();
});
</script>
@stop
