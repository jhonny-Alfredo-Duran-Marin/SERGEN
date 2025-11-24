@extends('adminlte::page')

@section('title', 'Registrar Devolución')

@section('content_header')
    <h1><i class="fas fa-undo"></i> Registrar Devolución</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Callout informativo -->
            <div class="callout callout-info">
                <h5><i class="fas fa-info-circle"></i> Incidente:</h5>
                <p class="mb-0">
                    <strong class="text-lg">{{ $incidente->codigo }}</strong> -
                    {{ $incidente->persona->nombre }} -
                    {{ $incidente->fecha_incidente->format('d/m/Y') }}
                </p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="fas fa-exclamation-triangle"></i> Hubo problemas con la devolución:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('incidentes.devolver.store', $incidente) }}">
                @csrf

                <!-- Items involucrados -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i> Items Involucrados en el Incidente
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-warning">
                            <h5><i class="fas fa-info-circle"></i> Instrucciones:</h5>
                            <p class="mb-0">
                                Marque los items que se van a devolver, indique la cantidad y el resultado de la devolución.
                                Los items marcados como "DEVUELTO_OK" se sumarán automáticamente al inventario.
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">
                                            <i class="fas fa-check"></i>
                                        </th>
                                        <th width="30%">Item</th>
                                        <th width="10%" class="text-center">Afectado</th>
                                        <th width="15%">Cantidad a Devolver</th>
                                        <th width="20%">Resultado</th>
                                        <th width="20%">Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidente->items as $it)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                       class="custom-control-input devolver-check"
                                                       name="items[{{ $loop->index }}][devolver]"
                                                       value="1"
                                                       id="check-{{ $loop->index }}"
                                                       data-row="{{ $loop->index }}">
                                                <label class="custom-control-label" for="check-{{ $loop->index }}"></label>
                                            </div>
                                            <input type="hidden"
                                                   name="items[{{ $loop->index }}][item_id]"
                                                   value="{{ $it->id }}">
                                        </td>

                                        <td>
                                            <strong>{{ $it->codigo }}</strong><br>
                                            <small class="text-muted">{{ $it->descripcion }}</small>
                                        </td>

                                        <td class="text-center align-middle">
                                            <span class="badge badge-danger">
                                                {{ $it->pivot->cantidad }}
                                            </span>
                                        </td>

                                        <td>
                                            <input type="number"
                                                   min="1"
                                                   max="{{ $it->pivot->cantidad }}"
                                                   name="items[{{ $loop->index }}][cantidad_devuelta]"
                                                   class="form-control cantidad-input"
                                                   data-row="{{ $loop->index }}"
                                                   placeholder="0"
                                                   disabled>
                                            <small class="form-text text-muted">
                                                Máximo: {{ $it->pivot->cantidad }}
                                            </small>
                                        </td>

                                        <td>
                                            <select name="items[{{ $loop->index }}][resultado]"
                                                    class="form-control resultado-select"
                                                    data-row="{{ $loop->index }}"
                                                    disabled>
                                                <option value="">— Seleccione —</option>
                                                <option value="DEVUELTO_OK">
                                                    <i class="fas fa-check"></i> Devuelto OK
                                                </option>
                                                <option value="DEVUELTO_DANADO">
                                                    Devuelto Dañado
                                                </option>
                                                <option value="NO_RECUPERADO">
                                                    No Recuperado
                                                </option>
                                                <option value="REPARABLE">
                                                    Reparable
                                                </option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="text"
                                                   name="items[{{ $loop->index }}][observacion]"
                                                   class="form-control observacion-input"
                                                   data-row="{{ $loop->index }}"
                                                   placeholder="Opcional"
                                                   disabled>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Registrar Devolución
                        </button>
                        <a href="{{ route('incidentes.show', $incidente) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    .callout {
        border-left-width: 5px;
    }
    .table tr:hover {
        background-color: #f8f9fa;
    }
</style>
@stop

@section('js')
<script>
$(function() {
    // Habilitar/deshabilitar campos según checkbox
    $('.devolver-check').on('change', function() {
        const row = $(this).data('row');
        const checked = $(this).is(':checked');

        $(`.cantidad-input[data-row="${row}"]`).prop('disabled', !checked);
        $(`.resultado-select[data-row="${row}"]`).prop('disabled', !checked);
        $(`.observacion-input[data-row="${row}"]`).prop('disabled', !checked);

        // Si se desmarca, limpiar campos
        if (!checked) {
            $(`.cantidad-input[data-row="${row}"]`).val('');
            $(`.resultado-select[data-row="${row}"]`).val('');
            $(`.observacion-input[data-row="${row}"]`).val('');
        }
    });

    // Validar cantidad al enviar
    $('form').on('submit', function(e) {
        let valid = true;
        let mensaje = '';

        $('.devolver-check:checked').each(function() {
            const row = $(this).data('row');
            const cantidad = $(`.cantidad-input[data-row="${row}"]`).val();
            const resultado = $(`.resultado-select[data-row="${row}"]`).val();

            if (!cantidad || cantidad <= 0) {
                valid = false;
                mensaje = 'Debe ingresar una cantidad válida para los items marcados';
                return false;
            }

            if (!resultado) {
                valid = false;
                mensaje = 'Debe seleccionar un resultado para los items marcados';
                return false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert(mensaje);
        }
    });
});
</script>
@stop
