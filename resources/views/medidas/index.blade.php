@extends('adminlte::page')

@section('title','Gestión de Medidas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1 class="m-0"><i class="fas fa-ruler-combined"></i> Gestión de Medidas</h1>

  {{-- Buscador reactivo (cliente) --}}
  <div class="input-group" style="max-width:520px;">
    <input id="search-medidas" type="text" class="form-control"
           placeholder="Buscar descripción o símbolo…">
    <div class="input-group-append">
      <button id="clear-search" class="btn btn-outline-secondary" title="Limpiar">
        <i class="fas fa-eraser"></i>
      </button>
    </div>
  </div>

  <a href="{{ route('medidas.create') }}" class="btn btn-success">
    <i class="fas fa-plus"></i> Nueva Medida
  </a>
</div>
@stop

@section('content')
@if(session('status'))
  <x-adminlte-alert theme="success" dismissible>
    <i class="fas fa-check-circle"></i> {{ session('status') }}
  </x-adminlte-alert>
@endif

{{-- Contador (se actualiza al filtrar) --}}
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3 id="count-medidas">{{ count($medidas) }}</h3>
        <p>Total Medidas</p>
      </div>
      <div class="icon"><i class="fas fa-ruler"></i></div>
    </div>
  </div>
</div>

<div class="card card-outline card-primary">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h3 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Medidas</h3>
    <div class="card-tools"><span class="badge badge-primary" id="badge-total">{{ count($medidas) }} registros</span></div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0" id="tabla-medidas">
        <thead class="bg-light">
          <tr>
            <th width="60">#</th>
            <th><i class="fas fa-tag"></i> Descripción</th>
            <th width="220"><i class="fas fa-font"></i> Símbolo</th>
            <th width="170" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
        @forelse($medidas as $idx => $m)
          <tr data-search="{{ Str::lower(Str::ascii($m->descripcion.' '.$m->simbolo)) }}">
            <td class="text-muted">{{ $idx + 1 }}</td>
            <td><strong class="col-descripcion">{{ $m->descripcion }}</strong></td>
            <td><span class="badge badge-info col-simbolo">{{ $m->simbolo }}</span></td>
            <td class="text-center">
              <div class="btn-group" role="group">
                <a class="btn btn-sm btn-warning" href="{{ route('medidas.edit',$m) }}" data-toggle="tooltip" title="Editar">
                  <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('medidas.destroy',$m) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar la medida «{{ $m->descripcion }}»?')" class="d-inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center py-5 text-muted">No hay medidas</td></tr>
        @endforelse
          {{-- Fila “sin resultados” (se muestra cuando el filtro oculta todo) --}}
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
</div>
@stop

@section('js')
<script>
(function(){
  const input = document.getElementById('search-medidas');
  const clear = document.getElementById('clear-search');
  const table = document.getElementById('tabla-medidas');
  const rows  = Array.from(table.querySelectorAll('tbody tr')).filter(tr => tr.id !== 'no-results');
  const noRes = document.getElementById('no-results');
  const count = document.getElementById('count-medidas');
  const badge = document.getElementById('badge-total');

  // quitar acentos y pasar a minúsculas
  const norm = s => (s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();

  // debounce
  let t; const debounce = (fn,ms=250)=>(...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); };

  // filtrar
  const applyFilter = ()=>{
    const q = norm(input.value);
    let visible = 0;

    rows.forEach((tr, idx) => {
      const haystack = tr.dataset.search || '';
      const show = q === '' || haystack.includes(q);
      tr.style.display = show ? '' : 'none';
      if (show) {
        visible++;
        // renumerar visible
        tr.querySelector('td').textContent = visible;
      }
    });

    noRes.style.display = visible === 0 ? '' : 'none';
    count.textContent = visible;
    badge.textContent = `${visible} registro${visible===1?'':'s'}`;
  };

  input.addEventListener('input', debounce(applyFilter, 250));
  clear.addEventListener('click', function(e){
    e.preventDefault();
    input.value = '';
    applyFilter();
    input.focus();
  });

  // tooltips
  $(function(){ $('[data-toggle="tooltip"]').tooltip(); });
})();
</script>
@stop
