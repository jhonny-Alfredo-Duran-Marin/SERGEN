@extends('adminlte::page')

@section('title', 'Gestión Estratégica | Ser.Gen')

@section('content_header')
<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-end">
        <div>
            <span class="text-uppercase text-muted smallest font-weight-bold" style="letter-spacing: 2px;">Constructora Ser.Gen</span>
            <h1 class="text-navy font-weight-bold m-0" style="font-size: 2rem; letter-spacing: -1px;">Consola de <span class="text-primary">Administración Central</span></h1>
        </div>
        <div class="badge badge-navy px-3 py-2 rounded-pill shadow-sm">
            <i class="fas fa-user-shield mr-1"></i> MODO: ADMINISTRADOR GLOBAL
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- KPIs PRINCIPALES --}}
    <div class="row">
        @php
            $kpis = [
                ['label' => 'Catálogo de Ítems', 'val' => $stats['items_total'], 'icon' => 'fa-boxes', 'color' => '#001f3f'],
                ['label' => 'Órdenes de Compra', 'val' => $stats['compras_mes'], 'icon' => 'fa-shopping-cart', 'color' => '#28a745'],
                ['label' => 'Préstamos Vigentes', 'val' => $stats['prestamos_activos'], 'icon' => 'fa-hand-hat', 'color' => '#007bff'],
                ['label' => 'Incidentes Reportados', 'val' => $stats['incidentes_total'], 'icon' => 'fa-exclamation-triangle', 'color' => '#dc3545'],
            ];
        @endphp
        @foreach($kpis as $k)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 card-kpi">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="smallest text-muted text-uppercase font-weight-bold mb-1">{{ $k['label'] }}</div>
                            <div class="h2 mb-0 font-weight-bold text-navy">{{ $k['val'] }}</div>
                        </div>
                        <div class="icon-box-fancy shadow-sm" style="color: {{ $k['color'] }};">
                            <i class="fas {{ $k['icon'] }} fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        {{-- COLUMNA IZQUIERDA: GESTIÓN DE SALIDAS (Préstamos y Consumos) --}}
        <div class="col-lg-8">
            {{-- PRÉSTAMOS RECIENTES --}}
            <div class="card border-0 shadow-sm mb-4 rounded-xl overflow-hidden">
                <div class="card-header bg-navy border-0">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-handshake mr-2"></i>Historial Reciente de Préstamos</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 custom-table">
                        <thead class="bg-light smallest text-uppercase">
                            <tr>
                                <th class="pl-4">Código</th>
                                <th>Destino / Proyecto</th>
                                <th class="text-center">Estado</th>
                                <th class="text-right pr-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosPrestamos as $p)
                            <tr>
                                <td class="pl-4 py-3"><strong class="text-navy">{{ $p->codigo }}</strong></td>
                                <td>
                                    <span class="d-block font-weight-bold small text-dark">{{ $p->persona->nombre ?? 'N/A' }}</span>
                                    <small class="text-muted">{{ $p->proyecto->descripcion ?? 'Gasto General' }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $p->estado == 'Completo' ? 'badge-success-soft' : 'badge-primary-soft' }} px-3 py-1 rounded-pill">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                                <td class="text-right pr-4 align-middle">
                                    <a href="{{ route('prestamos.show', $p) }}" class="btn btn-xs btn-outline-primary rounded-pill">Detalle</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ÚLTIMOS CONSUMOS DIRECTOS --}}
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden">
                <div class="card-header bg-light border-0">
                    <h6 class="m-0 font-weight-bold text-navy"><i class="fas fa-chart-pie mr-2 text-primary"></i>Consumos Directos de Material</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <tbody class="small">
                            @foreach($ultimosConsumos as $con)
                            <tr>
                                <td class="pl-4 py-3"><strong>{{ $con->item->descripcion }}</strong></td>
                                <td><span class="badge badge-light border">{{ $con->proyecto->descripcion ?? 'Gasto General' }}</span></td>
                                <td class="text-right text-navy font-weight-bold pr-4">{{ $con->cantidad_consumida }} un.</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: ADQUISICIONES Y ACTIVIDAD --}}
        <div class="col-lg-4">
            {{-- COMPRAS RECIENTES --}}
            <div class="card border-0 shadow-sm mb-4 rounded-xl overflow-hidden">
                <div class="card-header bg-success border-0 text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-basket mr-2"></i>Últimas Adquisiciones</h6>
                </div>
                <div class="card-body p-0">
                    @foreach($ultimasCompras as $c)
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center hover-soft">
                        <div>
                            <span class="d-block font-weight-bold text-navy small">OC-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <small class="text-muted">{{ Str::limit($c->proveedor, 20) }}</small>
                        </div>
                        <div class="text-right">
                            <span class="text-success font-weight-bold">Bs. {{ number_format($c->total, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- HISTORIAL DE ACCIONES (MOVIMIENTOS) --}}
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-navy">Historial de Acciones</h6>
                </div>
                <div class="card-body p-0">
                    <div style="max-height: 350px; overflow-y: auto;">
                        @foreach($ultimosMovimientos as $m)
                        <div class="px-3 py-2 border-bottom d-flex align-items-center">
                            <div class="avatar-action mr-3 bg-{{ $m->tipo == 'Ingreso' ? 'success' : 'danger' }}-light text-{{ $m->tipo == 'Ingreso' ? 'success' : 'danger' }}">
                                <i class="fas fa-{{ $m->tipo == 'Ingreso' ? 'plus' : 'minus' }} font-xs"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="d-block font-weight-bold text-navy smallest">{{ $m->item->codigo }}</span>
                                <small class="text-muted smallest">{{ $m->user->name }} • {{ $m->fecha->format('H:i') }}</small>
                            </div>
                            <div class="text-right font-weight-bold smallest">
                                {{ $m->cantidad }} u.
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .bg-navy { background-color: #001f3f !important; color: white !important; }
    .text-navy { color: #001f3f !important; }
    .rounded-xl { border-radius: 12px !important; }
    .smallest { font-size: 11px; }
    .icon-box-fancy { height: 45px; width: 45px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 10px; }
    .badge-success-soft { background: #e6f4ea; color: #1e7e34; }
    .badge-primary-soft { background: #e8f0fe; color: #1a73e8; }
    .bg-success-light { background: rgba(40,167,69,0.1); }
    .bg-danger-light { background: rgba(220,53,69,0.1); }
    .avatar-action { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .hover-soft:hover { background: #fbfcfe; cursor: pointer; }
    .custom-table td { vertical-align: middle !important; border-top: 1px solid #f2f2f2; }
    .card-kpi { transition: transform 0.3s ease; }
    .card-kpi:hover { transform: translateY(-5px); }
</style>
@stop
