@extends('adminlte::page')
@section('title', 'Editar kit')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit"></i> Editar kit — {{ $kit->codigo }}</h1>
        <a href="{{ route('kits.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Volver al listado
        </a>
    </div>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
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

    <form method="POST" action="{{ route('kits.update', $kit) }}" class="card card-outline card-warning">
        @csrf @method('PUT')
        <div class="card-body">
            @include('kits._form', [
                'kit' => $kit,
                'itemsAll' => $itemsAll,
                'itemsForJs' => $itemsForJs, // Asegúrate de que esto se pase
                'preselect' => $preselect, // Asegúrate de que esto se pase
            ])
        </div>
    </form>
@stop
