@php use Illuminate\Support\Str; @endphp
@extends('adminlte::page')

@section('title', 'Gestión de Items')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-box-open"></i> Gestión de Items</h1>
        <a href="{{ route('items.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Item
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
                    <h3 id="count-items">
                        {{ isset($total) ? number_format($total) : (method_exists($items, 'total') ? $items->total() : $items->count()) }}
                    </h3>
                    <p>Total Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ isset($activos) ? number_format($activos) : '—' }}</h3>
                    <p>Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ isset($bajoStock) ? number_format($bajoStock) : '—' }}</h3>
                    <p>Bajo Stock</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ isset($pasivos) ? number_format($pasivos) : '—' }}</h3>
                    <p>Pasivos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Búsqueda rápida en tiempo real -->
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search"></i> Búsqueda Rápida
            </h3>
        </div>
        <div class="card-body">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <input id="search-items" type="text" class="form-control form-control-lg"
                    placeholder="Buscar por código, descripción, fabricante, categoría, área...">
                <div class="input-group-append">
                    <button id="clear-search" class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> La búsqueda se realiza en tiempo real sin recargar la página
            </small>
        </div>
    </div>

    <!-- Filtros avanzados colapsables -->
    <div class="card card-primary card-outline collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros Avanzados</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('items.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-search"></i> Búsqueda de Texto:</label>
                            <input name="q" class="form-control" value="{{ request('q') }}"
                                placeholder="Código, descripción, fabricante">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-sitemap"></i> Categoría:</label>
                            <select name="categoria_id" class="form-control">
                                <option value="">— Todas las categorías —</option>
                                @foreach ($categorias as $c)
                                    <option value="{{ $c->id }}" @selected((string) request('categoria_id') === (string) $c->id)>
                                        {{ $c->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-tags"></i> Tipo:</label>
                            <select name="tipo" class="form-control">
                                <option value="">— Ambos —</option>
                                <option value="Herramienta" @selected(request('tipo') === 'Herramienta')>
                                    Herramienta
                                </option>
                                <option value="Material" @selected(request('tipo') === 'Material')>
                                    Material
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Estado:</label>
                            <select name="estado" class="form-control">
                                <option value="">— Todos —</option>
                                <option value="Activo" @selected(request('estado') === 'Activo')>
                                    Activo
                                </option>
                                <option value="Pasivo" @selected(request('estado') === 'Pasivo')>
                                    Pasivo
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de items -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Items</h3>
            <div class="card-tools">
                <span class="badge badge-primary" id="badge-total">
                    {{ method_exists($items, 'total') ? $items->total() : $items->count() }}
                    {{ $items->count() == 1 ? 'item' : 'items' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" id="tabla-items">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="80" class="text-center">Imagen</th>
                                <th><i class="fas fa-barcode"></i> Código</th>
                                <th><i class="fas fa-tag"></i> Descripción</th>
                                <th><i class="fas fa-sitemap"></i> Cat.</th>
                                <th><i class="fas fa-ruler"></i> Med.</th>
                                <th><i class="fas fa-layer-group"></i> Área</th>
                                <th class="text-right"><i class="fas fa-boxes"></i> Stock</th>
                                <th class="text-right"><i class="fas fa-cubes"></i> Pzs</th>
                                <th class="text-right"><i class="fas fa-dollar-sign"></i> Costo</th>
                                <th class="text-center"><i class="fas fa-tags"></i> Tipo</th>
                                <th class="text-center"><i class="fas fa-toggle-on"></i> Estado</th>
                                <th width="200" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $it)
                                @php
                                    $low = isset($lowStockThreshold) ? $lowStockThreshold : 3;
                                    $search = Str::lower(
                                        Str::ascii(
                                            ($it->codigo ?? '') .
                                                ' ' .
                                                ($it->descripcion ?? '') .
                                                ' ' .
                                                ($it->fabricante ?? '') .
                                                ' ' .
                                                ($it->categoria->descripcion ?? '') .
                                                ' ' .
                                                ($it->medida->simbolo ?? '') .
                                                ' ' .
                                                ($it->area->descripcion ?? '') .
                                                ' ' .
                                                ($it->tipo ?? '') .
                                                ' ' .
                                                ($it->estado ?? ''),
                                        ),
                                    );
                                @endphp
                                <tr data-search="{{ $search }}">
                                    <td class="text-muted">
                                        {{ $loop->iteration + (method_exists($items, 'currentPage') ? ($items->currentPage() - 1) * $items->perPage() : 0) }}
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('items.show', $it) }}" data-toggle="tooltip"
                                            title="Ver detalle">
                                            <img src="{{ $it->thumb_url ?? 'https://via.placeholder.com/60x60?text=—' }}"
                                                alt="Imagen" class="rounded border elevation-1"
                                                style="width:60px;height:60px;object-fit:cover;" loading="lazy"
                                                decoding="async">
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('items.show', $it) }}" class="text-primary font-weight-bold"
                                            data-toggle="tooltip" title="Ver detalle">
                                            <i class="fas fa-external-link-alt"></i> {{ $it->codigo }}
                                        </a>
                                        @if ($it->fabricante)
                                            <div class="small text-muted">
                                                <i class="fas fa-industry"></i> {{ $it->fabricante }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <strong>{{ $it->descripcion }}</strong>
                                        {{-- Cambiamos $it->ubicacion por la relación --}}
                                        @if ($it->ubicacion_relacion)
                                            <div class="small text-muted">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                {{ $it->ubicacion_relacion->descripcion }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge badge-light border" data-toggle="tooltip" title="Categoría">
                                            {{ $it->categoria?->descripcion ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-info" data-toggle="tooltip"
                                            title="{{ $it->medida?->descripcion ?? 'Sin medida' }}">
                                            {{ $it->medida?->simbolo ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-light border" data-toggle="tooltip" title="Área">
                                            {{ $it->area?->descripcion ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="text-right">
                                        <span class="badge badge-{{ $it->cantidad <= $low ? 'danger' : 'success' }}"
                                            data-toggle="tooltip"
                                            title="{{ $it->cantidad <= $low ? 'Stock bajo' : 'Stock normal' }}">
                                            <i
                                                class="fas fa-{{ $it->cantidad <= $low ? 'exclamation-triangle' : 'check' }}"></i>
                                            {{ $it->cantidad }}
                                        </span>
                                    </td>

                                    <td class="text-right">{{ $it->piezas ?? 0 }}</td>

                                    <td class="text-right">
                                        <strong class="text-success">
                                            ${{ number_format($it->costo_unitario, 2, '.', ',') }}
                                        </strong>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge badge-{{ $it->tipo === 'Herramienta' ? 'warning' : 'dark' }}">
                                            <i class="fas fa-{{ $it->tipo === 'Herramienta' ? 'tools' : 'box' }}"></i>
                                            {{ $it->tipo }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <span
                                            class="badge badge-{{ $it->estado === 'Activo' ? 'success' : 'secondary' }}">
                                            <i
                                                class="fas fa-{{ $it->estado === 'Activo' ? 'check-circle' : 'times-circle' }}"></i>
                                            {{ $it->estado }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-sm btn-info" href="{{ route('items.show', $it) }}"
                                                data-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a class="btn btn-sm btn-warning" href="{{ route('items.edit', $it) }}"
                                                data-toggle="tooltip" title="Editar item">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('items.destroy', $it) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de eliminar el item «{{ $it->codigo }} - {{ $it->descripcion }}»?\n\nEsta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" data-toggle="tooltip"
                                                    title="Eliminar item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Fila "sin resultados" para el filtro cliente -->
                            <tr id="no-results" style="display:none;">
                                <td colspan="13" class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron items con ese criterio de búsqueda</p>
                                    <button type="button" class="btn btn-secondary" id="reset-from-no-results">
                                        <i class="fas fa-eraser"></i> Limpiar búsqueda
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="7" class="text-right">
                                    <strong>Resumen:</strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-info">
                                        <i class="fas fa-boxes"></i>
                                        {{ $items->sum('cantidad') }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-secondary">
                                        {{ $items->sum('piezas') }}
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">
                                        ${{ number_format($items->sum(function ($i) {return $i->cantidad * $i->costo_unitario;}),2,'.',',') }}
                                    </strong>
                                </td>
                                <td colspan="3" class="text-muted">
                                    <small>Valor total de inventario</small>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay items registrados</p>
                    <a href="{{ route('items.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Crear primer item
                    </a>
                </div>
            @endif
        </div>

        @if (method_exists($items, 'hasPages') && $items->hasPages())
            <div class="card-footer clearfix">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Mostrando <strong>{{ $items->firstItem() }}</strong> –
                            <strong>{{ $items->lastItem() }}</strong>
                            de <strong>{{ $items->total() }}</strong> registros
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $items->appends(request()->only('q', 'categoria_id', 'tipo', 'estado'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Inventario</span>
                    <span class="info-box-number">Administra items, controla stock, categorías y costos del
                        inventario</span>
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

        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }

        .elevation-1 {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script>
        (function() {
            const input = document.getElementById('search-items');
            const clear = document.getElementById('clear-search');
            const resetBtn = document.getElementById('reset-from-no-results');
            const table = document.getElementById('tabla-items');

            if (!table) return;

            const rows = Array.from(table.querySelectorAll('tbody tr')).filter(tr => tr.id !== 'no-results');
            const noRes = document.getElementById('no-results');
            const badge = document.getElementById('badge-total');
            const count = document.getElementById('count-items');

            // Normalizar texto (remover acentos y convertir a minúsculas)
            const norm = s => (s || '').toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();

            let debounceTimer;
            const debounce = (fn, ms = 250) => (...args) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fn(...args), ms);
            };

            const applyFilter = () => {
                const q = norm(input.value);
                let visible = 0;

                rows.forEach(tr => {
                    const haystack = tr.dataset.search || '';
                    const show = q === '' || haystack.includes(q);
                    tr.style.display = show ? '' : 'none';

                    if (show) {
                        visible++;
                        const firstCell = tr.querySelector('td');
                        if (firstCell) firstCell.textContent = visible;
                    }
                });

                // Mostrar/ocultar mensaje de "sin resultados"
                if (noRes) {
                    noRes.style.display = visible === 0 && q !== '' ? '' : 'none';
                }

                // Actualizar badges de conteo
                if (badge) {
                    badge.textContent = `${visible} ${visible === 1 ? 'item' : 'items'}`;
                }
                if (count) {
                    count.textContent = visible;
                }
            };

            // Event listeners
            if (input) {
                input.addEventListener('input', debounce(applyFilter, 250));

                // Focus en el input al cargar la página
                input.focus();
            }

            if (clear) {
                clear.addEventListener('click', (e) => {
                    e.preventDefault();
                    input.value = '';
                    applyFilter();
                    input.focus();
                });
            }

            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    input.value = '';
                    applyFilter();
                    input.focus();
                });
            }

            // Inicializar tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        })();
    </script>
@stop
