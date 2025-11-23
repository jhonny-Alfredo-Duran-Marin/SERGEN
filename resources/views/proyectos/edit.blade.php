@extends('adminlte::page')

@section('title','Editar Proyecto')

@section('content_header')
    <h1>Editar Proyecto</h1>
@stop

@section('content')
<form method="POST" action="{{ route('proyectos.update',$proyecto) }}" class="card p-3">
    @csrf @method('PUT')
    @include('proyectos.partials.form', ['proyecto' => $proyecto, 'mode' => 'edit'])
</form>
@stop
