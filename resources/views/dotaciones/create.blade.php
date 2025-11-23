@extends('adminlte::page')
@section('title','Nueva Dotación')

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-plus-circle"></i> Nueva Dotación</h1>
    <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
  </div>
@stop

@section('content')
<form method="POST" action="{{ route('dotaciones.store') }}" class="card card-outline card-success">
  @csrf
  <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> Datos</h3></div>
  <div class="card-body">
    @include('dotaciones.partials.form', ['dotacion'=>null,'items'=>$items,'personas'=>$personas,'mode'=>'create'])
  </div>
</form>
@stop
