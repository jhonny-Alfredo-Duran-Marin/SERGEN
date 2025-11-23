@extends('adminlte::page')

@section('title', 'Panel de Control Total')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-crown text-warning"></i> MODO DIOS ACTIVADO
    </h1>
    <div>
        <span class="badge badge-danger badge-lg animate__animated animate__pulse animate__infinite">
            SUPER ADMIN
        </span>
    </div>
</div>
@stop

@section('content')
{{-- ESTADÍSTICAS BRUTALES --}}
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-gradient-danger">
            <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Ítems</span>
                <span class="info-box-number">{{ $stats['items_total'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-gradient-warning">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Stock Crítico</span>
                <span class="info-box-number">{{ $stats['items_bajo_stock'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-gradient-primary">
            <span class="info-box-icon"><i class="fas fa-truck-loading"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Préstamos Activos</span>
                <span class="info-box-number">{{ $stats['prestamos_activos'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-history"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Movimientos Hoy</span>
                <span class="info-box-number">{{ $stats['movimientos_hoy'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- ÚLTIMOS MOVIMIENTOS --}}
    <div class="col-lg-4">
        <x-adminlte-card title="Últimos Movimientos" theme="dark" icon="fas fa-exchange-alt">
            <div class="list-group list-group-flush">
                @foreach($ultimosMovimientos as $m)
                <a href="{{ route('movimientos.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $m->item->codigo }}</strong><br>
                        <small class="text-muted">{{ $m->user->name }} • {{ $m->fecha->format('H:i') }}</small>
                    </div>
                    <span class="badge {{ $m->tipo=='Ingreso' ? 'bg-success' : 'bg-danger' }} badge-pill">
                        {{ $m->tipo == 'Ingreso' ? '+' : '-' }}{{ $m->cantidad }}
                    </span>
                </a>
                @endforeach
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('movimientos.index') }}" class="small">Ver todos →</a>
            </div>
        </x-adminlte-card>
    </div>

    {{-- ÚLTIMAS COMPRAS --}}
    <div class="col-lg-4">
        <x-adminlte-card title="Últimas Compras" theme="success" icon="fas fa-shopping-cart">
            <div class="list-group list-group-flush">
                @foreach($ultimasCompras as $c)
                <div class="list-group-item d-flex justify-content-between">
                    <div>
                        <strong>#{{ $c->id }}</strong> {{ $c->proveedor }}<br>
                        <small class="text-muted">{{ $c->fecha }}</small>
                    </div>
                    <span class="text-success font-weight-bold">Bs. {{ number_format($c->total, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('compras.index') }}" class="small">Ver compras →</a>
            </div>
        </x-adminlte-card>
    </div>

    {{-- STOCK CRÍTICO --}}
    <div class="col-lg-4">
        <x-adminlte-card title="COMPRAR YA!" theme="danger" icon="fas fa-bell">
            <ul class="list-group">
                @foreach($stockCritico as $i)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>{{ $i->codigo }}</strong> {{ $i->descripcion }}</span>
                    <span class="badge badge-danger badge-pill animate__animated animate__flash animate__infinite">
                        {{ $i->cantidad }}
                    </span>
                </li>
                @endforeach
            </ul>
        </x-adminlte-card>
    </div>
</div>
@stop
