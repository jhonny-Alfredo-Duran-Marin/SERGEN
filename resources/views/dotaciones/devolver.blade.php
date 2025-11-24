@extends('adminlte::page')

@section('title', 'Devolver Dotación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-undo"></i> Devolver Dotación</h1>

        <a href="{{ route('dotaciones.show', $dotacion) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')

@if(session('status'))
    <x-adminlte-alert theme="success" dismissible>
        <i class="fas fa-check-circle"></i> {{ session('status') }}
    </x-adminlte-alert>
@endif

<form method="POST" action="{{ route('dotaciones.devolver.store', $dotacion) }}" class="card card-outline card-primary">
    @csrf

    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-boxes"></i> Ítems asignados</h3>
    </div>

    <div class="card-body">

        <p><strong>Persona:</strong> {{ $dotacion->persona->nombre }}</p>
        <p><strong>Fecha entrega:</strong> {{ $dotacion->fecha }}</p>
        <hr>

        <table class="table table-bordered table-striped">
            <thead class="bg-light">
                <tr>
                    <th>Ítem</th>
                    <th style="width: 90px">Cantidad</th>
                    <th style="width: 160px">Estado devolución</th>
                    <th>Observación</th>
                </tr>
            </thead>

            <tbody>

            @foreach($dotacion->items as $index => $di)
                <tr>
                    <td>
                        <strong>{{ $di->item->codigo }}</strong><br>
                        <small>{{ $di->item->descripcion }}</small>
                    </td>

                    <td class="text-center">
                        <span class="badge bg-info">{{ $di->cantidad }}</span>
                    </td>

                    <td>
                        <input type="hidden"
                               name="items[{{ $index }}][dotacion_item_id]"
                               value="{{ $di->id }}">

                        <select name="items[{{ $index }}][estado]"
                                class="form-control" required>

                            <option value="OK">Devuelto OK</option>
                            <option value="BAJA">Baja (gastado)</option>
                            <option value="DANADO">Dañado</option>
                            <option value="PERDIDO">No devuelto (perdido)</option>

                        </select>
                    </td>

                    <td>
                        <textarea class="form-control" rows="2"
                                  name="items[{{ $index }}][observacion]"
                                  placeholder="Detalle adicional (opcional)"></textarea>
                    </td>

                </tr>
            @endforeach

            </tbody>
        </table>

    </div>

    <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Procesar devolución
        </button>
    </div>

</form>

@stop
