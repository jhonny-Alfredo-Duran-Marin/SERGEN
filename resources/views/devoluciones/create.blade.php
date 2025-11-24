@extends('adminlte::page')
@section('title','Devolución — '.$prestamo->codigo)

@section('content_header')
<h1 class="mb-3">
    <i class="fas fa-undo-alt text-primary"></i> Devolución — {{ $prestamo->codigo }}
</h1>
@stop

@section('content')

<form method="POST" action="{{ route('devoluciones.store',$prestamo) }}">
@csrf

{{-- ===============================
    ÍTEMS DEL KIT
   =============================== --}}
@if($prestamo->kit)
<div class="card card-success mb-4">
    <div class="card-header bg-success text-white">
        <strong><i class="fas fa-box"></i> Ítems del Kit</strong>
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Ítem</th>
                    <th class="text-center">Prestado</th>
                    <th style="width:130px" class="text-center">Cant. a devolver</th>
                    <th style="width:180px" class="text-center">Estado</th>
                    <th style="width:120px" class="text-center">Consumido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestamo->kit->items as $it)
                    <tr>
                        <td>
                            <strong>{{ $it->codigo }}</strong><br>
                            <small class="text-muted">{{ $it->descripcion }}</small>
                            <input type="hidden" name="items[]" value="{{ $it->id }}">
                            <input type="hidden" name="es_kit[]" value="1">
                            <input type="hidden" name="prestado[]" value="{{ $it->pivot->cantidad }}">
                        </td>

                        <td class="text-center">{{ $it->pivot->cantidad }}</td>

                        <td>
                            <input type="number" name="devolver[]" value="0" min="0"
                                   max="{{ $it->pivot->cantidad }}"
                                   class="form-control form-control-sm text-center">
                        </td>

                        <td>
                            <select name="estado[]" class="form-control form-control-sm">
                                <option value="ok">OK (completo)</option>
                                <option value="dañado">Dañado</option>
                                <option value="faltante">Incompleto / faltante</option>
                            </select>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="consumido[]" value="{{ $it->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif



{{-- ===============================
    ÍTEMS SUELTOS
   =============================== --}}
<div class="card card-primary mb-4">
    <div class="card-header bg-primary text-white">
        <strong><i class="fas fa-tags"></i> Ítems sueltos prestados</strong>
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Ítem</th>
                    <th class="text-center">Prestado</th>
                    <th style="width:130px" class="text-center">Cant. a devolver</th>
                    <th style="width:180px" class="text-center">Estado</th>
                    <th style="width:120px" class="text-center">Consumido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prestamo->detalles as $d)
                    @if(!$prestamo->kit || !$prestamo->kit->items->pluck('id')->contains($d->item_id))
                    <tr>
                        <td>
                            <strong>{{ $d->item->codigo }}</strong><br>
                            <small class="text-muted">{{ $d->item->descripcion }}</small>

                            <input type="hidden" name="items[]" value="{{ $d->item_id }}">
                            <input type="hidden" name="es_kit[]" value="0">
                            <input type="hidden" name="prestado[]" value="{{ $d->cantidad_prestada }}">
                        </td>

                        <td class="text-center">{{ $d->cantidad_prestada }}</td>

                        <td>
                            <input type="number" name="devolver[]" value="0" min="0"
                                   max="{{ $d->cantidad_prestada }}"
                                   class="form-control form-control-sm text-center">
                        </td>

                        <td>
                            <select name="estado[]" class="form-control form-control-sm">
                                <option value="ok">OK (completo)</option>
                                <option value="dañado">Dañado</option>
                                <option value="faltante">Incompleto / faltante</option>
                            </select>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="consumido[]" value="{{ $d->item_id }}">
                        </td>

                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<button type="submit" class="btn btn-success btn-lg float-right">
    <i class="fas fa-save"></i> Guardar devolución
</button>

</form>

@stop
