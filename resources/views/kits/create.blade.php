{{-- resources/views/kits/create.blade.php --}}
@extends('adminlte::page')
@section('title', 'Nuevo kit')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-plus-circle"></i> Nuevo kit de emergencia</h1>
        <a href="{{ route('kits.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible><i class="fas fa-check-circle"></i>
            {{ session('status') }}</x-adminlte-alert>
    @endif
    @if ($errors->any())
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            <ul class="m-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <form method="POST" action="{{ route('kits.store') }}" class="card card-outline card-success">
        @csrf
        <div class="card-body">
            @include('kits._form', [
                'kit' => null,
                'itemsAll' => $itemsAll,
                'nextCode' => $nextCode,
                'itemsForJs' => $itemsForJs,
            ])
        </div>
    </form>
@stop
