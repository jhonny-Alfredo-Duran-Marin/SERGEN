@extends('adminlte::page')

@section('title', "Préstamo $prestamo->codigo")

@section('content_header')
    <h1>
        <i class="fas fa-file-contract"></i>
        Préstamo {{ $prestamo->codigo }}

        <span class="badge badge-{{ $prestamo->estado == 'Activo' ? 'primary' : ($prestamo->estado == 'Observado' ? 'warning' : 'success') }}">
            {{ $prestamo->estado }}
        </span>
    </h1>

    <div class="float-right">
        <a href="{{ route('prestamos.edit', $prestamo) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>

        <a href="{{ route('prestamos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop


@section('content')

{{-- =======================================================
      INFORMACIÓN PRINCIPAL
======================================================= --}}
<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <strong>Fecha:</strong><br>
                {{ $prestamo->fecha->format('Y-m-d') }}
            </div>

            <div class="col-md-4">
                <strong>Persona:</strong><br>
                {{ $prestamo->persona->nombre }}
            </div>

            <div class="col-md-4">
                <strong>Nota:</strong><br>
                {{ $prestamo->nota ?: '—' }}
            </div>

        </div>
    </div>
</div>


{{-- =======================================================
      BOTONES DE ACCIÓN
======================================================= --}}
<div class="mb-3">

    <a href="{{ route('devoluciones.create', $prestamo) }}"
       class="btn btn-primary">
        <i class="fas fa-undo"></i> Registrar devolución
    </a>


 
</div>



{{-- =======================================================
      KIT ENTREGADO
======================================================= --}}
@if ($prestamo->kit_emergencia_id)
    <div class="card card-success card-outline">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-toolbox"></i>
                Kit entregado:
                {{ $prestamo->kit->codigo }} — {{ $prestamo->kit->nombre }}
            </h5>
        </div>

        <div class="card-body">

            <h6><strong>Ítems incluidos en el kit:</strong></h6>

            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Costo Unitario</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($prestamo->kit->items as $i)
                        <tr>
                            <td>{{ $i->codigo }}</td>
                            <td>{{ $i->descripcion }}</td>
                            <td>Bs {{ number_format($i->costo_unitario, 2) }}</td>
                            <td>{{ $i->pivot->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif


{{-- =======================================================
      ÍTEMS SUELTOS PRESTADOS
======================================================= --}}
<div class="card card-info card-outline mt-4">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-list"></i> Ítems sueltos prestados
        </h5>
    </div>

    <div class="card-body">

        @if ($prestamo->detalles->count() == 0)
            <p class="text-muted">No se prestaron ítems sueltos.</p>
        @else
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>C.U.</th>
                        <th>Prestado</th>
                        <th>Devuelto</th>
                        <th>Pendiente</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($prestamo->detalles as $d)
                        <tr>
                            <td>{{ $d->item->codigo }}</td>
                            <td>{{ $d->item->descripcion }}</td>
                            <td>Bs {{ number_format($d->item->costo_unitario, 2) }}</td>
                            <td>{{ $d->cantidad_prestada }}</td>
                            <td>{{ $d->cantidad_devuelta }}</td>
                            <td>{{ $d->cantidad_prestada - $d->cantidad_devuelta }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</div>

@stop
