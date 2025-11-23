@extends('adminlte::page')
@section('title','Kit '.$kit->codigo)
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-first-aid"></i> {{ $kit->codigo }}</h1>
    <div class="btn-group">
      @can('kits.update')
      <a href="{{ route('kits.edit',$kit) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
      @endcan
      <a href="{{ route('kits.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> Volver</a>
    </div>
  </div>
@stop
@section('content')
<div class="card card-outline card-primary">
  <div class="card-body">
    <dl class="row">
      <dt class="col-sm-3">Código</dt><dd class="col-sm-9"><strong>{{ $kit->codigo }}</strong></dd>
      <dt class="col-sm-3">Nombre</dt><dd class="col-sm-9">{{ $kit->nombre ?? '—' }}</dd>
      <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9">{{ $kit->descripcion ?? '—' }}</dd>
    </dl>

    <h5 class="mt-3">Ítems</h5>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead><tr><th>Código</th><th>Descripción</th><th class="text-end">Cantidad</th></tr></thead>
        <tbody>
          @forelse($kit->items as $it)
            <tr>
              <td><strong>{{ $it->codigo }}</strong></td>
              <td>{{ $it->descripcion }}</td>
              <td class="text-end">{{ (int)$it->pivot->cantidad }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted p-4">Sin ítems</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@stop
