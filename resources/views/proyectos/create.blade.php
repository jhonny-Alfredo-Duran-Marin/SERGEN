@extends('adminlte::page')

@section('title', 'Nuevo Proyecto')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Crear Nuevo Proyecto</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('proyectos.store') }}">
                @csrf
                @include('proyectos.partials.form', ['proyecto' => null, 'mode' => 'create'])
            </form>
        </div>
    </div>
@stop
