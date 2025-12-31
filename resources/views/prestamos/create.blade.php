{{-- prestamos/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Nuevo Préstamo')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Crear Nuevo Préstamo</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-11 offset-md-0">
            <form method="POST" action="{{ route('prestamos.store') }}">
                @csrf
                @include('prestamos.partials.form', [
                    'items' => $items,
                    'personas' => $personas,
                    'proyectos' => $proyectos
                ])
            </form>
        </div>
    </div>
@stop
