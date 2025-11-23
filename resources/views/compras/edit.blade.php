{{-- resources/views/compras/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Editar Compra')

@section('content_header')
    <h1>
        <i class="fas fa-edit"></i> Editar Compra #{{ $compra->id }}
    </h1>
@stop

@section('content')
    <x-adminlte-card title="Actualizar Detalles" theme="warning" icon="fas fa-edit">
        <form action="{{ route('compras.update', $compra) }}" method="POST">
            @method('PUT')
            @include('compras._form', ['compra' => $compra])
        </form>
    </x-adminlte-card>
@stop
