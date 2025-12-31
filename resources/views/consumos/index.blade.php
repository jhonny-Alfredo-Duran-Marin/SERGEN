@extends('adminlte::page')

@section('title', 'Control de Consumos')

@section('content_header')
    <h1><i class="fas fa-chart-line text-primary"></i> Control de Consumos</h1>
@stop

@section('content')
    {{-- Estadísticas Rápidas --}}
    <div class="row">
        <x-adminlte-info-box title="Gasto Total" text="Bs. {{ number_format($totalDinero, 2) }}" icon="fas fa-money-bill-wave"
            theme="danger" col=6 shadow />
        <x-adminlte-info-box title="Items Consumidos" text="{{ $totalItems }} u." icon="fas fa-boxes"
            theme="info" col=6 shadow />
    </div>

    {{-- Panel de Filtros --}}
    <x-adminlte-card title="Filtros de Búsqueda" theme="primary" icon="fas fa-filter" collapsible shadow>
        <form action="{{ route('consumos.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-select2 name="persona_id" label="Por Persona">
                        <option value="">— Todas —</option>
                        @foreach ($personas as $p)
                            <option value="{{ $p->id }}" @selected(request('persona_id') == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="col-md-4">
                    <x-adminlte-select2 name="proyecto_id" label="Por Proyecto">
                        <option value="">— Todos —</option>
                        @foreach ($proyectos as $pj)
                            <option value="{{ $pj->id }}" @selected(request('proyecto_id') == $pj->id)>{{ $pj->descripcion }}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="col-md-5">
                    <label>&nbsp;</label>
                    <div class="d-flex justify-content-start align-items-center">
                        <button type="submit" class="btn btn-primary shadow-sm mx-1">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('consumos.index') }}" class="btn btn-secondary shadow-sm mx-1">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                        {{-- BOTÓN DE REPORTE GENERAL (DE TODO EL PROYECTO FILTRADO) --}}
                        <a href="{{ route('consumos.pdf', request()->all()) }}" class="btn btn-danger shadow-sm mx-1" target="_blank">
                            <i class="fas fa-file-invoice"></i> Reporte General
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </x-adminlte-card>

    {{-- Tabla de Resultados --}}
    <x-adminlte-card title="Detalle de Consumos Realizados" theme="navy" icon="fas fa-list-ul" shadow>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-navy">
                    <tr>
                        <th class="px-3" width="120">Fecha</th>
                        <th>Material / Ítem</th>
                        <th>Destino (Proyecto / Persona)</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">P. Unitario</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center" width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($consumos as $c)
                        <tr>
                            <td class="align-middle px-3">{{ $c->created_at->format('d/m/Y') }}</td>
                            <td class="align-middle">
                                <strong>{{ $c->item->descripcion }}</strong><br>
                                <small class="text-muted text-uppercase">{{ $c->item->codigo }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info shadow-sm">{{ $c->proyecto->descripcion ?? 'Gasto General' }}</span><br>
                                <small class="text-muted"><i class="fas fa-user-tag"></i> {{ $c->persona->nombre ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center align-middle font-weight-bold">{{ $c->cantidad_consumida }}</td>
                            <td class="text-right align-middle text-muted small">Bs. {{ number_format($c->precio_unitario, 2) }}</td>
                            <td class="text-right align-middle text-bold text-navy">
                                Bs. {{ number_format($c->cantidad_consumida * $c->precio_unitario, 2) }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    {{-- Ver detalle --}}
                                    <a href="{{ route('consumos.show', $c) }}" class="btn btn-sm btn-info shadow-sm mx-1"
                                        data-toggle="tooltip" title="Ver Detalle Completo">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- RECIBO INDIVIDUAL (SOLO DE ESTE ÍTEM) --}}
                                    <a href="{{ route('consumos.recibo', $c) }}" class="btn btn-sm btn-danger shadow-sm mx-1"
                                        target="_blank" data-toggle="tooltip" title="Imprimir Vale de Entrega">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                No se encontraron registros de consumo con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($consumos->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $consumos->links() }}
            </div>
        @endif
    </x-adminlte-card>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
