@extends('adminlte::page')
@section('title', 'Detalle de Dotación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-eye"></i> Detalle de Dotación</h1>

        <div>
            <a href="{{ route('dotaciones.devolver.form', $dotacion) }}">

                <i class="fas fa-undo"></i> Devolver
            </a>

            <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">
                Volver
            </a>
            <a href="{{ route('dotaciones.pdf', $dotacion) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
            </a>

        </div>
    </div>
@stop

@section('content')

    <div class="card card-outline card-primary">
        <div class="card-body">

            <strong>Persona:</strong> {{ $dotacion->persona->nombre }}<br>
            <strong>Fecha:</strong> {{ $dotacion->fecha }}<br>
            <strong>Estado:</strong> <span class="badge badge-info">{{ $dotacion->estado_final }}</span>

            <hr>

            <h4>Ítems asignados</h4>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ítem</th>
                        <th>Cantidad</th>
                        <th>Estado Final</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dotacion->items as $it)
                        <tr>
                            <td>{{ $it->item->codigo }} — {{ $it->item->descripcion }}</td>
                            <td>{{ $it->cantidad }}</td>
                            <td>{{ $it->estado_final ?? 'PENDIENTE' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@stop
