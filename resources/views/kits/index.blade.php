@extends('adminlte::page')
@section('title','Kits de emergencia')
@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-first-aid"></i> Kits de emergencia</h1>
    @can('kits.create')
      <a href="{{ route('kits.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo</a>
    @endcan
  </div>
@stop
@section('content')
@if(session('status'))
  <x-adminlte-alert theme="success" dismissible><i class="fas fa-check-circle"></i> {{ session('status') }}</x-adminlte-alert>
@endif

<div class="card card-outline card-primary">
  <div class="card-body">
    <form class="row g-2 mb-3">
      <div class="col-md-6">
        <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar por código o nombre…">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="bg-light">
          <tr><th>#</th><th>Código</th><th>Nombre</th><th class="text-end"># Ítems</th><th style="width:200px">Acciones</th></tr>
        </thead>
        <tbody>
          @forelse($kits as $k)
            <tr>
              <td class="text-muted">{{ $loop->iteration + ($kits->currentPage()-1)*$kits->perPage() }}</td>
              <td><a href="{{ route('kits.show',$k) }}"><strong>{{ $k->codigo }}</strong></a></td>
              <td>{{ $k->nombre ?? '—' }}</td>
              <td class="text-end">{{ $k->items_count }}</td>
              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-info" href="{{ route('kits.show',$k) }}"><i class="fas fa-eye"></i></a>
                @can('kits.update')
                <a class="btn btn-sm btn-warning" href="{{ route('kits.edit',$k) }}"><i class="fas fa-edit"></i></a>
                @endcan
                @can('kits.delete')
                <form method="POST" action="{{ route('kits.destroy',$k) }}" onsubmit="return confirm('¿Eliminar {{ $k->codigo }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted p-4">Sin registros</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $kits->links() }}</div>
  </div>
</div>
@stop
