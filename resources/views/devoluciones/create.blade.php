@extends('adminlte::page')
@section('title', 'Devolución — ' . $prestamo->codigo)

@section('content_header')
<h1><i class="fas fa-undo text-primary"></i> Devolución — {{ $prestamo->codigo }}</h1>
@stop

@section('content')

<form method="POST" action="{{ route('devoluciones.store', $prestamo) }}">
@csrf

{{-- ===================== ÍTEMS SUELTOS ===================== --}}
<div class="card card-primary mb-4">
    <div class="card-header"><strong>Ítems sueltos del préstamo</strong></div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>Ítem</th>
                    <th>Prestado</th>
                    <th>Devuelto</th>
                    <th>Pendiente</th>
                    <th>OK</th>
                    <th>Dañado</th>
                    <th>Perdido</th>
                    <th>Consumido</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($itemsSueltos as $data)
                <tr data-pendiente="{{ $data['pendiente'] }}" class="item-row">
                    <td>
                        <strong>{{ $data['item']->codigo }}</strong><br>
                        <small>{{ $data['item']->descripcion }}</small>
                    </td>
                    <td>{{ $data['prestado'] }}</td>
                    <td class="text-success">{{ $data['devuelto'] }}</td>
                    <td class="text-danger font-weight-bold">{{ $data['pendiente'] }}</td>

                    @foreach (['ok','dañado','perdido','consumido'] as $estado)
                    <td>
                        <input type="number"
                            name="items[{{ $data['item_id'] }}][{{ $estado }}]"
                            class="form-control form-control-sm qty"
                            min="0" max="{{ $data['pendiente'] }}" value="0">
                    </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-3">
                        <i class="fas fa-check-circle"></i> No hay ítems sueltos pendientes de devolución
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== KITS ===================== --}}
@foreach ($kitsConPendientes as $kitData)
<div class="card card-warning mb-4">
    <div class="card-header">
        <strong>Kit: {{ $kitData['kit']->codigo }} — {{ $kitData['kit']->nombre }}</strong>
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>Ítem del kit</th>
                    <th>Prestado</th>
                    <th>Devuelto</th>
                    <th>Pendiente</th>
                    <th>OK</th>
                    <th>Dañado</th>
                    <th>Perdido</th>
                    <th>Consumido</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($kitData['items'] as $itemData)
                <tr data-pendiente="{{ $itemData['pendiente'] }}" class="item-row">
                    <td>
                        <strong>{{ $itemData['item']->codigo }}</strong><br>
                        <small>{{ $itemData['item']->descripcion }}</small>
                    </td>
                    <td>{{ $itemData['prestado'] }}</td>
                    <td class="text-success">{{ $itemData['devuelto'] }}</td>
                    <td class="text-danger font-weight-bold">{{ $itemData['pendiente'] }}</td>

                    @foreach (['ok','dañado','perdido','consumido'] as $estado)
                    <td>
                        <input type="number"
                            name="kits[{{ $kitData['kit_id'] }}][{{ $itemData['item_id'] }}][{{ $estado }}]"
                            class="form-control form-control-sm qty"
                            min="0" max="{{ $itemData['pendiente'] }}" value="0">
                    </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- Mensaje si no hay nada pendiente --}}
@if (empty($itemsSueltos) && empty($kitsConPendientes))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>No hay items pendientes de devolución.</strong>
        Todos los items han sido devueltos, están dañados, perdidos o consumidos.
    </div>
    <a href="{{ route('prestamos.show', $prestamo) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al préstamo
    </a>
@else
    <button type="submit" class="btn btn-success btn-lg float-right">
        <i class="fas fa-save"></i> Guardar devolución
    </button>
@endif

</form>
@stop

@push('js')
<script>
document.querySelectorAll('.item-row').forEach(row => {
    const max = parseInt(row.dataset.pendiente);
    row.querySelectorAll('.qty').forEach(input => {
        input.addEventListener('input', () => {
            let sum = 0;
            row.querySelectorAll('.qty').forEach(i => sum += (+i.value || 0));
            if (sum > max) {
                alert('La suma supera el pendiente (' + max + ')');
                input.value = 0;
            }
        });
    });
});
</script>
@endpush
