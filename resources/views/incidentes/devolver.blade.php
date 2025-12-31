@extends('adminlte::page')

@section('content')
<div class="row pt-3">
    <div class="col-md-10 offset-md-1">

        {{-- SECCIÓN DE ERRORES: ESTO TE MOSTRARÁ POR QUÉ NO REGISTRA --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible shadow">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> ¡Atención! El registro no se completó:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('incidentes.devolver.store', $incidente) }}">
            @csrf
            <div class="card card-primary card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title">Registrar Devolución: <strong>{{ $incidente->codigo }}</strong></h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light text-center">
                            <tr>
                                <th>Item / Tipo Original</th>
                                <th>Pendiente</th>
                                <th width="150">Cantidad</th>
                                <th width="200">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incidente->items as $it)
                                @php
                                    $pivId = $it->pivot->id;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $it->codigo }}</strong> ({{ $it->pivot->estado_item }})
                                        <input type="hidden" name="items[{{ $pivId }}][item_id]" value="{{ $it->id }}">
                                        <input type="hidden" name="items[{{ $pivId }}][tipo]" value="{{ $it->tipo_mapeado }}">
                                    </td>
                                    <td class="text-center font-weight-bold text-danger">{{ $it->pendiente_real }}</td>
                                    <td>
                                        <input type="number" name="items[{{ $pivId }}][cantidad_devuelta]"
                                               class="form-control" min="0" max="{{ $it->pendiente_real }}" value="0">
                                    </td>
                                    <td>
                                        <select name="items[{{ $pivId }}][resultado]" class="form-control">
                                            <option value="DEVUELTO_OK">DEVUELTO_OK</option>
                                            <option value="DEVUELTO_DANADO">DEVUELTO_DANADO</option>
                                            <option value="REPARABLE">REPARABLE</option>
                                            <option value="NO_RECUPERADO">NO_RECUPERADO</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right text-muted">
                    <small class="float-left">Verifica que al menos una cantidad sea mayor a 0.</small>
                    <button type="submit" class="btn btn-success shadow">Guardar Devolución</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
