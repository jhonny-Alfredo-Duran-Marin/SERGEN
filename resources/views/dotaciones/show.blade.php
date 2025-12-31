@extends('adminlte::page')
@section('title', 'Detalle de Dotación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-eye"></i> Detalle de Dotación #{{ $dotacion->id }}</h1>

        <div>
            @if($dotacion->estado_final === 'ABIERTA')
                <a href="{{ route('dotaciones.devolver.form', $dotacion) }}" class="btn btn-success">
                    <i class="fas fa-undo"></i> Procesar Devolución
                </a>
            @endif

            <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Volver
            </a>

            <a href="{{ route('dotaciones.pdf', $dotacion) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            {{-- Tarjeta de Información General --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información General</h3>
                </div>
                <div class="card-body">
                    <p><strong><i class="fas fa-user"></i> Persona:</strong><br> {{ $dotacion->persona->nombre }}</p>
                    <p><strong><i class="fas fa-calendar-alt"></i> Fecha de Registro:</strong><br> {{ $dotacion->fecha }}</p>
                    <p><strong><i class="fas fa-info-circle"></i> Estado Final:</strong><br>
                        <span class="badge @if($dotacion->estado_final == 'ABIERTA') badge-warning @else badge-success @endif">
                            {{ $dotacion->estado_final }}
                        </span>
                    </p>
                    @if($dotacion->nota)
                        <p><strong><i class="fas fa-comment"></i> Nota:</strong><br> {{ $dotacion->nota }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Tarjeta de Ítems --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ítems Asignados en esta Dotación</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover m-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Ítem</th>
                                <th class="text-center">Cant.</th>
                                <th>Estado Origen</th>
                                <th>Siguiente Entrega</th>
                                <th>Estado Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dotacion->items as $it)
                                <tr>
                                    <td>
                                        <strong>{{ $it->item->codigo }}</strong><br>
                                        <small>{{ $it->item->descripcion }}</small>
                                    </td>
                                    <td class="text-center align-middle">{{ $it->cantidad }}</td>
                                    <td class="align-middle">
                                        <span class="badge badge-secondary">{{ $it->estado_item }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($it->fecha_siguiente)
                                            {{ \Carbon\Carbon::parse($it->fecha_siguiente)->format('d/m/Y') }}
                                            @if(\Carbon\Carbon::parse($it->fecha_siguiente)->isPast() && $dotacion->estado_final == 'ABIERTA')
                                                <span class="badge badge-danger ml-1">VENCIDO</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No programada</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        {{ $it->estado_item_devolucion ?? 'EN USO' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
