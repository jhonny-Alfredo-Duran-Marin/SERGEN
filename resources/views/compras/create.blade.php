{{-- resources/views/compras/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Nueva Compra')

@section('content_header')
    <h1>
        <i class="fas fa-plus"></i> Registrar Nueva Compra
    </h1>
@stop

@section('content')
    <x-adminlte-card title="Detalles de la Compra" theme="success" icon="fas fa-shopping-cart">
        <form action="{{ route('compras.store') }}" method="POST">
            @include('compras._form')
        </form>
    </x-adminlte-card>
@stop
