
@extends('adminlte::page')
@section('title','Detalle de Dotación')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-eye"></i> Detalle de Dotación</h1>
    <div class="btn-group">
      <a href="{{ route('dotaciones.edit',$dotacione) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
      <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> Volver</a>
    </div>
  </div>
@stop

@section('content')
<div class="card card-outline card-primary">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3></div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Fecha</dt>   <dd class="col-sm-9">{{ optional($dotacione->fecha)->format('Y-m-d') }}</dd>
      <dt class="col-sm-3">Persona</dt> <dd class="col-sm-9">{{ $dotacione->persona?->nombre }}</dd>
      <dt class="col-sm-3">Ítem</dt>
      <dd class="col-sm-9">
        <strong>{{ $dotacione->item?->codigo }}</strong> — {{ $dotacione->item?->descripcion }}
      </dd>
      <dt class="col-sm-3">Cantidad</dt><dd class="col-sm-9"><span class="badge badge-info">{{ $dotacione->cantidad }}</span></dd>
    </dl>
  </div>
</div>
@stop
