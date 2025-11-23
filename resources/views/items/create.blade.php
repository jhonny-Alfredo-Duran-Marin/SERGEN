@extends('adminlte::page')

@section('title', 'Nuevo Item')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Crear Nuevo Item</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                @csrf
                @include('items.partials.form', [
                    'item' => null,
                    'categorias' => $categorias,
                    'medidas' => $medidas,
                    'mode' => 'create',
                ])
            </form>
        </div>
    </div>
@stop
