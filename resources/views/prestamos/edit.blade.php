{{-- prestamos/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Editar Préstamo')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Préstamo {{ $prestamo->codigo }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-11 offset-md-0">
            <div class="callout callout-warning">
                <h5><i class="fas fa-info-circle"></i> Editando Préstamo:</h5>
                <p class="mb-0">
                    <strong class="text-lg">{{ $prestamo->codigo }}</strong> -
                    {{ $prestamo->persona->nombre }}
                </p>
            </div>

            <form method="POST" action="{{ route('prestamos.update', $prestamo) }}">
                @csrf
                @method('PUT')
                @include('prestamos.partials.form', [
                    'prestamo' => $prestamo,
                    'items' => $items,
                    'personas' => $personas,
                    'proyectos' => $proyectos
                ])
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    .callout {
        border-left-width: 5px;
    }
</style>
@stop
