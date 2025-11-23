@php use Illuminate\Support\Str; @endphp
@extends('adminlte::page')
@section('title','Gestión de Dotaciones')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1 class="m-0"><i class="fas fa-people-carry"></i> Gestión de Dotaciones</h1>

  <div class="input-group" style="max-width:560px;">
    <input id="search-local" type="text" class="form-control" placeholder="Buscar por persona, código o descripción…">
    <div class="input-group-append">
      <button id="clear-search" class="btn btn-outline-secondary" title="Limpiar"><i class="fas fa-eraser"></i></button>
    </div>
  </div>

  <a href="{{ route('dotaciones.create') }}" class="btn btn-success">
    <i class="fas fa-plus"></i> Nueva
  </a>
</div>
@stop

@section('content')
@if(session('status'))
  <x-adminlte-alert theme="success" dismissible>
    <i class="fas fa-check-circle"></i> {{ session('status') }}
  </x-adminlte-alert>
@endif

<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info"><div class="inner"><h3>{{ number_format($total) }}</h3><p>Total Dotaciones</p></div><div class="icon"><i class="fas fa-list"></i></div></div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success"><div class="inner"><h3>{{ number_format($totalUnidades) }}</h3><p>Total Unidades Entregadas</p></div><div class="icon"><i class="fas fa-sort-amount-up-alt"></i></div></div>
  </div>
</div>

<div class="card card-primary card-outline collapsed-card">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
    <div class="card-tools"><button class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button></div>
  </div>
  <div class="card-body">
    <form class="row g-2" method="GET" action="{{ route('dotaciones.index') }}">
      <div class="col-md-3">
        <label class="form-label">Texto</label>
        <input name="q" class="form-control" value="{{ request('q') }}" placeholder="Persona, código o descripción">
      </div>
      <div class="col-md-3">
        <label class="form-label">Ítem</label>
        <select name="item_id" class="form-control">
          <option value="">— Todos —</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}" @selected((string)request('item_id')===(string)$it->id)>{{ $it->codigo }} — {{ $it->descripcion }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Persona</label>
        <select name="persona_id" class="form-control">
          <option value="">— Todas —</option>
          @foreach($personas as $p)
            <option value="{{ $p->id }}" @selected((string)request('persona_id')===(string)$p->id)>{{ $p->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-1">
        <label class="form-label">Desde</label>
        <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
      </div>
      <div class="col-md-1">
        <label class="form-label">Hasta</label>
        <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
      </div>
    </form>
  </div>
</div>

<div class="card card-outline card-primary">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h3 class="card-title mb-0"><i class="fas fa-list"></i> Lista de Dotaciones</h3>
    <div class="card-tools">
      <span class="badge badge-primary">{{ $dotaciones->total() }} registros</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0" id="tabla-dot">
        <thead class="bg-light">
          <tr>
            <th width="60">#</th>
            <th>Fecha</th>
            <th>Persona</th>
            <th>Ítem</th>
            <th class="text-end">Cant.</th>
            <th width="200" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
        @forelse($dotaciones as $d)
          @php
            $search = Str::lower(Str::ascii(($d->persona->nombre ?? '').' '.($d->item->codigo ?? '').' '.($d->item->descripcion ?? '')));
          @endphp
          <tr data-search="{{ $search }}">
            <td class="text-muted">{{ $loop->iteration + ($dotaciones->currentPage()-1)*$dotaciones->perPage() }}</td>
            <td>{{ optional($d->fecha)->format('Y-m-d') }}</td>
            <td>{{ $d->persona->nombre ?? '—' }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                @php $thumb = $d->item->imagen_thumb ?? null; @endphp
                @if($thumb)
                  <img src="{{ Storage::disk('public')->url($thumb) }}" alt="" class="rounded border" style="width:36px;height:36px;object-fit:cover;">
                @endif
                <div>
                  <strong>{{ $d->item->codigo ?? '' }}</strong>
                  <div class="small text-muted">{{ $d->item->descripcion ?? '' }}</div>
                </div>
              </div>
            </td>
            <td class="text-end"><span class="badge badge-info">{{ $d->cantidad }}</span></td>
            <td class="text-center">
              <div class="btn-group">
                <a class="btn btn-sm btn-info" href="{{ route('dotaciones.show',$d) }}" data-toggle="tooltip" title="Ver"><i class="fas fa-eye"></i></a>
                <a class="btn btn-sm btn-warning" href="{{ route('dotaciones.edit',$d) }}" data-toggle="tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                <form action="{{ route('dotaciones.destroy',$d) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar dotación? Se repondrá el stock.')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5">
              <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
              <div class="text-muted">Sin registros</div>
            </td>
          </tr>
        @endforelse

        <tr id="no-results" style="display:none;">
          <td colspan="6" class="text-center py-5">
            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
            <div class="text-muted">Sin resultados para tu búsqueda</div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
  @if($dotaciones->hasPages())
    <div class="card-footer d-flex justify-content-end">
      {{ $dotaciones->links('pagination::bootstrap-4') }}
    </div>
  @endif
</div>
@stop

@section('js')
<script>
(function(){
  const input = document.getElementById('search-local');
  const clear = document.getElementById('clear-search');
  const rows  = Array.from(document.querySelectorAll('#tabla-dot tbody tr')).filter(tr => tr.id !== 'no-results');
  const noRes = document.getElementById('no-results');
  const norm  = s => (s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
  let t;
  const deb = (fn,ms=250)=> (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); };
  const apply=()=>{
    const q=norm(input.value); let vis=0;
    rows.forEach(tr=>{ const show = q==='' || (tr.dataset.search||'').includes(q); tr.style.display=show?'':'none'; if(show) vis++;});
    noRes.style.display = vis===0 ? '' : 'none';
  };
  input?.addEventListener('input', deb(apply,250));
  clear?.addEventListener('click', e=>{ e.preventDefault(); input.value=''; apply(); input.focus(); });
  $(function(){ $('[data-toggle="tooltip"]').tooltip(); });
})();
</script>
@stop
