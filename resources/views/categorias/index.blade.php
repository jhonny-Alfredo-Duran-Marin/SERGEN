@php use Illuminate\Support\Str; @endphp
@extends('adminlte::page')

@section('title','Gestión de Categorías')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-sitemap"></i> Gestión de Categorías
        </h1>

        {{-- Buscador reactivo (cliente) --}}
        <div class="input-group" style="max-width:520px;">
            <input id="search-categorias" type="text" class="form-control"
                   placeholder="Buscar descripción o estado…">
            <div class="input-group-append">
                <button id="clear-search" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="fas fa-eraser"></i>
                </button>
            </div>
        </div>

        <a href="{{ route('categorias.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Categoría
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

    {{-- Small box: total (muestra el total del paginador; si cargas todo con ->get(), cambiará con el filtro) --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="count-categorias">{{ $categorias instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categorias->total() : $categorias->count() }}</h3>
                    <p>Total Categorías</p>
                </div>
                <div class="icon"><i class="fas fa-folder"></i></div>
            </div>
        </div>
    </div>

    {{-- Lista --}}
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Categorías</h3>
            <div class="card-tools">
                <span class="badge badge-primary" id="badge-total">
                    {{ $categorias instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categorias->total() : $categorias->count() }} registros
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" id="tabla-categorias">
                    <thead class="bg-light">
                        <tr>
                            <th width="60">#</th>
                            <th><i class="fas fa-tag"></i> Descripción</th>
                            <th width="160"><i class="fas fa-toggle-on"></i> Estado</th>
                            <th width="170" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($categorias as $c)
                        <tr data-search="{{ Str::lower(Str::ascii(($c->descripcion ?? '').' '.($c->estado ?? ''))) }}">
                            <td class="text-muted">
                                {{-- numeración base (se actualizará al filtrar) --}}
                                {{ $loop->iteration + (method_exists($categorias,'currentPage') ? ($categorias->currentPage() - 1) * $categorias->perPage() : 0) }}
                            </td>
                            <td><strong class="col-descripcion">{{ $c->descripcion }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $c->estado==='Activo'?'success':'secondary' }}">
                                    <i class="fas fa-{{ $c->estado==='Activo'?'check':'minus' }}"></i>
                                    {{ $c->estado }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-sm btn-warning"
                                       href="{{ route('categorias.edit',$c) }}"
                                       data-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categorias.destroy',$c) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar la categoría «{{ $c->descripcion }}»?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar">
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

        {{-- Paginación (si usas paginate). Nota: el filtro cliente actúa sobre la página visible. --}}
        @if(method_exists($categorias,'hasPages') && $categorias->hasPages())
            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <small class="text-muted mb-2 mb-md-0">
                    Mostrando
                    <strong>{{ $categorias->firstItem() }}</strong> – <strong>{{ $categorias->lastItem() }}</strong>
                    de <strong>{{ $categorias->total() }}</strong> registros
                </small>
                <div>{{ $categorias->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>
@stop

@section('js')
<script>
(function(){
  const input = document.getElementById('search-categorias');
  const clear = document.getElementById('clear-search');
  const table = document.getElementById('tabla-categorias');
  if(!table) return;

  // filas reales (excluimos la fila "sin resultados")
  const allRows = Array.from(table.querySelectorAll('tbody tr')).filter(tr => tr.id !== 'no-results');
  const noRes  = document.getElementById('no-results');
  const badge  = document.getElementById('badge-total');
  const count  = document.getElementById('count-categorias');

  // normaliza: quita acentos y pasa a minúsculas
  const norm = s => (s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();

  // debounce básico
  let t; const debounce = (fn,ms=250)=>(...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); };

  const applyFilter = ()=>{
    const q = norm(input.value);
    let visible = 0;

    allRows.forEach(tr => {
      const haystack = tr.dataset.search || '';
      const show = q === '' || haystack.includes(q);
      tr.style.display = show ? '' : 'none';
      if (show) {
        visible++;
        // renumerar la primera celda
        const firstCell = tr.querySelector('td');
        if (firstCell) firstCell.textContent = visible;
      }
    });

    noRes.style.display = visible === 0 ? '' : 'none';

    // actualizar badges/contadores visibles
    if (badge) badge.textContent = `${visible} registro${visible===1?'':'s'}`;
    if (count) count.textContent = visible;
  };

  input?.addEventListener('input', debounce(applyFilter, 250));
  clear?.addEventListener('click', (e)=>{
    e.preventDefault();
    input.value=''; applyFilter(); input.focus();
  });

  // tooltips
  $(function(){ $('[data-toggle="tooltip"]').tooltip(); });
})();
</script>
@stop
