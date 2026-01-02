@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <h1><i class="fas fa-shopping-bag"></i> Detalle de Compra #{{ $compra->id }}</h1>
@stop

@section('content')
    <div class="row">
        {{-- Bloque de Imágenes --}}
        <div class="col-md-4">
            <x-adminlte-card title="Evidencia de Compra" theme="dark" icon="fas fa-image" outline collapsible>
                @if ($compra->imagen)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $compra->imagen) }}" class="img-fluid rounded shadow-sm border"
                            style="max-height: 250px; cursor: pointer;" onclick="window.open(this.src)">
                    </div>
                @else
                    <div class="text-center p-4 bg-light border">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Sin imagen de factura</p>
                    </div>
                @endif
            </x-adminlte-card>

            <x-adminlte-card title="Código QR de Pago" theme="purple" icon="fas fa-qrcode" outline collapsible>
                @if ($compra->qr)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $compra->qr) }}" class="img-fluid rounded shadow-sm border"
                            style="max-height: 250px; cursor: pointer;" onclick="window.open(this.src)">
                    </div>
                @else
                    <div class="text-center p-4 bg-light border">
                        <i class="fas fa-qrcode fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Sin QR registrado</p>
                    </div>
                @endif
            </x-adminlte-card>
        </div>

        {{-- Datos Técnicos --}}
        <div class="col-md-8">
            <x-adminlte-card title="Información General" theme="info" icon="fas fa-list-alt" header-class="text-bold">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-primary">Descripción</h5>
                            <span class="description-text">{{ $compra->descripcion }}</span>
                        </div>
                        <hr>
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-primary">Fecha de Compra</h5>
                            <span class="description-text">{{ $compra->fecha_compra->format('d/m/Y') }}</span>
                        </div>
                        <hr>
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-primary">Tipo de Compra</h5>
                            <span class="badge badge-secondary" style="font-size: 1rem">{{ $compra->tipo_compra }}</span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-success">Costo Total</h5>
                            <span class="description-text" style="font-size: 1.2rem; font-weight: bold;">
                                $ {{ number_format($compra->costo_total, 2) }}
                            </span>
                        </div>
                        <hr>
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-info">Cantidad</h5>
                            <span class="description-text">{{ $compra->cantidad }} unidades</span>
                        </div>
                        <hr>
                        <div class="description-block text-left p-2">
                            <h5 class="description-header text-warning">Estado de Procesamiento</h5>
                            @if ($compra->estado_procesamiento === 'Pendiente')
                                <span class="badge badge-danger p-2"><i class="fas fa-clock"></i> Pendiente</span>
                            @else
                                <span class="badge badge-success p-2"><i class="fas fa-check-circle"></i> Resuelto</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 border-top pt-3">
                        <p class="text-muted small"><i class="fas fa-user"></i> Registrado por:
                            {{ $compra->user->name ?? 'Sistema' }}</p>
                    </div>
                </div>

                <x-slot name="footerSlot">
                    <div class="d-flex justify-content-between">
                        @can('compras.index')
                            <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a la lista
                            </a>
                        @endcan
                           @can('compras.update')
                        <a href="{{ route('compras.edit', $compra) }}" class="btn btn-warning shadow">
                            <i class="fas fa-edit"></i> Editar información
                        </a>
                        @endcan
                    </div>
                </x-slot>
            </x-adminlte-card>
        </div>
    </div>
@stop
