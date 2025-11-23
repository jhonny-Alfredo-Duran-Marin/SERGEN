@extends('adminlte::page')
@section('title','Editar Dotación')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-edit"></i> Editar Dotación</h1>
    <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
  </div>
@stop

@section('content')
<form method="POST" action="{{ route('dotaciones.update',$dotacione) }}" class="card card-outline card-warning">
  @csrf @method('PUT')
  <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> Datos</h3></div>
  <div class="card-body">
    @include('dotaciones.partials.form', ['dotacion'=>$dotacione,'items'=>$items,'personas'=>$personas,'mode'=>'edit'])
  </div>
</form>
@stop
