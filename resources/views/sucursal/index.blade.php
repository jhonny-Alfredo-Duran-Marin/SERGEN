@php use Illuminate\Support\Str; @endphp

@extends('adminlte::page')

@section('title', 'Gestión de Sucursales')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-sitemap"></i> Gestión de Sucursales
        </h1>

        {{-- Buscador (GET para filtrar en servidor, y también usado por el filtro JS) --}}
        <form method="GET" action="{{ route('sucursal.index') }}" class="form-inline" style="max-width:520px;">
            <div class="input-group w-100">
                <input
                    id="search-sucursales"
                    type="text"
                    name="q"
                    class="form-control"
                    placeholder="Buscar descripción o estado…"
                    value="{{ $q }}"
                >
                <div class="input-group-append">
                    <button id="clear-search" class="btn btn-outline-secondary" type="button" title="Limpiar">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>
            </div>
        </form>

        <a href="{{ route('sucursal.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Sucursal
        </a>
    </div>
@stop

@section('content')
    {{-- Alertas --}}
    @if(session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    {{-- Small box: total sucursales --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="count-sucursales">
                        {{ $sucursales->total() }}
                    </h3>
                    <p>Total Sucursales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista --}}
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-list"></i> Lista de Sucursales
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary" id="badge-total">
                    {{ $sucursales->total() }} registros
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tabla-sucursales">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">#</th>
                            <th>
                                <i class="fas fa-tag"></i> Descripción
                            </th>
                            <th width="160">
                                <i class="fas fa-toggle-on"></i> Estado
                            </th>
                            <th width="170" class="text-center">
                                <i class="fas fa-cog"></i> Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sucursales as $c)
                            <tr data-search="{{ Str::lower(Str::ascii(($c->descripcion ?? '').' '.($c->estado ?? ''))) }}">
                                <td class="text-muted">
                                    {{ $loop->iteration + ($sucursales->currentPage() - 1) * $sucursales->perPage() }}
                                </td>
                                <td>
                                    <strong class="col-descripcion">{{ $c->descripcion }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $c->estado === 'Activo' ? 'success' : 'secondary' }}">
                                        <i class="fas fa-{{ $c->estado === 'Activo' ? 'check' : 'minus' }}"></i>
                                        {{ $c->estado }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-sm btn-warning"
                                           href="{{ route('sucursal.edit', $c) }}"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('sucursal.destroy', $c) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar la Sucursal «{{ $c->descripcion }}»?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                    data-toggle="tooltip" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <div class="text-muted">Sin registros</div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Fila "sin resultados" para el filtro cliente --}}
                        <tr id="no-results" style="display:none;">
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <div class="text-muted">Sin resultados para tu búsqueda</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación --}}
        @if($sucursales->hasPages())
            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <small class="text-muted mb-2 mb-md-0">
                    Mostrando
                    <strong>{{ $sucursales->firstItem() }}</strong> – <strong>{{ $sucursales->lastItem() }}</strong>
                    de <strong>{{ $sucursales->total() }}</strong> registros
                </small>
                <div>
                    {{ $sucursales->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
@stop

@section('js')
<script>
(function() {
    const input = document.getElementById('search-sucursales');
    const clear = document.getElementById('clear-search');
    const table = document.getElementById('tabla-sucursales');
    if (!table || !input) return;

    const allRows = Array.from(table.querySelectorAll('tbody tr')).filter(tr => tr.id !== 'no-results');
    const noRes  = document.getElementById('no-results');
    const badge  = document.getElementById('badge-total');
    const count  = document.getElementById('count-sucursales');

    const norm = s => (s || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    let t;
    const debounce = (fn, ms = 250) => (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };

    const applyFilter = () => {
        const q = norm(input.value);
        let visible = 0;

        allRows.forEach(tr => {
            const haystack = norm(tr.dataset.search || '');
            const show = q === '' || haystack.includes(q);
            tr.style.display = show ? '' : 'none';

            if (show) {
                visible++;
                const firstCell = tr.querySelector('td');
                if (firstCell) {
                    firstCell.textContent = visible;
                }
            }
        });

        if (noRes) {
            noRes.style.display = visible === 0 ? '' : 'none';
        }

        if (badge) {
            badge.textContent = `${visible} registro${visible === 1 ? '' : 's'}`;
        }
        if (count) {
            count.textContent = visible;
        }
    };

    input.addEventListener('input', debounce(applyFilter, 250));

    if (clear) {
        clear.addEventListener('click', function(e) {
            e.preventDefault();
            input.value = '';
            applyFilter();
            input.focus();
        });
    }

    // Inicializar con lo que venga en el input (por si viene de un GET ?q=...)
    applyFilter();

    // tooltips de Bootstrap
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
})();
</script>
@stop
