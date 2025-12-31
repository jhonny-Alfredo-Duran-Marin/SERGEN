@extends('adminlte::page')

@section('content')
<div class="row pt-4">
    <div class="col-md-6 mx-auto">
        <x-adminlte-card title="Detalle de Consumo #{{ $consumo->id }}" theme="navy" icon="fas fa-info-circle">
            <div class="list-group list-group-unbordered">
                <div class="list-group-item"><b>Ítem:</b> <span class="float-right">{{ $consumo->item->descripcion }}</span></div>
                <div class="list-group-item"><b>Proyecto:</b> <span class="float-right">{{ $consumo->proyecto->nombre ?? 'N/A' }}</span></div>
                <div class="list-group-item"><b>Persona Responsable:</b> <span class="float-right">{{ $consumo->persona->nombre ?? 'N/A' }}</span></div>
                <div class="list-group-item"><b>Cantidad:</b> <span class="float-right">{{ $consumo->cantidad_consumida }}</span></div>
                <div class="list-group-item"><b>Costo Unitario:</b> <span class="float-right">Bs. {{ number_format($consumo->precio_unitario, 2) }}</span></div>
                <div class="list-group-item bg-light text-xl">
                    <b>TOTAL CONSUMIDO:</b> <span class="float-right text-danger font-weight-bold text-xl">Bs. {{ number_format($consumo->cantidad_consumida * $consumo->precio_unitario, 2) }}</span>
                </div>
            </div>
            <x-slot name="footerSlot">
                <a href="{{ route('consumos.index') }}" class="btn btn-secondary">Volver</a>
            </x-slot>
        </x-adminlte-card>
    </div>
</div>
@stop
