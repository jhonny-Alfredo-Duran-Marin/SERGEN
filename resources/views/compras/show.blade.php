@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <h1>
        <i class="fas fa-info-circle"></i> Detalle de Compra #{{ $compra->id }}
    </h1>
@stop

@section('content')
    <x-adminlte-card title="Información de la Compra" theme="info" icon="fas fa-shopping-cart">

        {{-- Imagen --}}
        @if ($compra->imagen)
            <div class="mb-3 text-center">
                <img src="{{ asset('storage/' . $compra->imagen) }}"
                     class="img-fluid rounded shadow"
                     style="max-height:300px; object-fit:contain;">
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <p><strong>Descripción:</strong> {{ $compra->descripcion }}</p>
                <p><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
                <p><strong>Tipo:</strong> {{ $compra->tipo_compra }}</p>
            </div>

            <div class="col-md-6">
                <p><strong>Cantidad:</strong> {{ $compra->cantidad }}</p>
                <p><strong>Costo Total:</strong> ${{ number_format($compra->costo_total, 2) }}</p>
                <p><strong>Estado:</strong>
                    @if ($compra->estado_procesamiento === 'Pendiente')
                        <span class="badge badge-danger">Pendiente</span>
                    @else
                        <span class="badge badge-success">Resuelto</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="text-right mt-3">
            <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>

            <a href="{{ route('compras.edit', $compra) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>

    </x-adminlte-card>
@stop
