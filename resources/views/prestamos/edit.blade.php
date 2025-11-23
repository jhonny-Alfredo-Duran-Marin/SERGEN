@extends('adminlte::page')
@section('title','Editar préstamo '.$prestamo->codigo)

@section('content_header')
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="fas fa-edit"></i> Editar préstamo <small class="text-muted">{{ $prestamo->codigo }}</small></h1>
    <a href="{{ route('prestamos.show',$prestamo) }}" class="btn btn-secondary"><i class="fas fa-eye"></i> Ver</a>
  </div>
@stop

@section('content')
<form method="POST" action="{{ route('prestamos.update',$prestamo) }}" class="card card-outline card-warning">
  @csrf @method('PUT')
  <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle"></i> Datos</h3></div>
  <div class="card-body">
    @include('prestamos.partials.form', ['prestamo'=>$prestamo,'items'=>$items,'personas'=>$personas,'proyectos'=>$proyectos])
  </div>
</form>
@stop
