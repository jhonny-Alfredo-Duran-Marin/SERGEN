<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i> Ítems Asignados
        </h3>
        <div class="card-tools">
            <button type="button" id="add-item" class="btn btn-success btn-sm shadow-sm" onclick="addItem()">
                <i class="fas fa-plus"></i> Añadir Ítem
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="callout callout-info mb-4">
            <h5><i class="fas fa-info-circle text-info"></i> Gestión de Dotación:</h5>
            <p class="mb-0 text-muted">Seleccione los productos, su origen y defina la fecha de la próxima renovación para el control de inventario.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle shadow-sm" id="tabla-items">
                <thead class="bg-navy text-white">
                    <tr>
                        <th style="width: 30%">Ítem</th>
                        <th style="width: 12%" class="text-center">Cantidad</th>
                        <th style="width: 20%">Estado Origen</th>
                        <th style="width: 28%">Siguiente Entrega</th>
                        <th style="width: 10%" class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @if (isset($dotacion) && $dotacion->items->count() > 0)
                        @foreach ($dotacion->items as $idx => $di)
                            <tr>
                                <td class="align-middle">
                                    <select class="form-control select2 item-select" name="items[{{ $idx }}][item_id]" data-row="{{ $idx }}" required>
                                        @foreach ($items as $it)
                                            <option value="{{ $it->id }}" data-stock="{{ $it->cantidad }}" @selected($di->item_id == $it->id)>
                                                {{ $it->codigo }} — {{ $it->descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $idx }}][dotacion_item_id]" value="{{ $di->id }}">
                                    <div class="mt-1">
                                        <span class="badge badge-secondary shadow-sm">Stock actual: <span class="stock-badge" data-row="{{ $idx }}">0</span></span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="number" min="1" class="form-control text-center cantidad-input shadow-sm" name="items[{{ $idx }}][cantidad]" data-row="{{ $idx }}" value="{{ $di->cantidad }}" required>
                                </td>
                                <td class="align-middle">
                                    <select class="form-control shadow-sm" name="items[{{ $idx }}][estado_item]" required>
                                        <option value="USO_PROPIO" @selected($di->estado_item == 'USO_PROPIO')>USO PROPIO</option>
                                        <option value="DE_VENTA" @selected($di->estado_item == 'DE_VENTA')>DE VENTA</option>
                                        <option value="COMPRADO" @selected($di->estado_item == 'COMPRADO')>COMPRADO</option>
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <input type="date" class="form-control shadow-sm" id="date_{{ $idx }}" name="items[{{ $idx }}][fecha_siguiente]" value="{{ $di->fecha_siguiente ? $di->fecha_siguiente->format('Y-m-d') : '' }}">
                                    <div class="btn-group btn-group-toggle w-100 mt-1 shadow-sm">
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate({{ $idx }}, 3)">
                                            <i class="fas fa-calendar-plus"></i> 3 Meses
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate({{ $idx }}, 6)">
                                            <i class="fas fa-calendar-plus"></i> 6 Meses
                                        </button>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm shadow-sm" onclick="this.closest('tr').remove()" title="Eliminar Ítem">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="no-items-row">
                            <td colspan="5" class="text-center text-muted py-5 bg-light">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="h5">No hay ítems agregados</p>
                                <p class="small">Haga clic en el botón verde para añadir productos a esta dotación</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
    let index = {{ isset($dotacion) ? $dotacion->items->count() : 0 }};

    function addItem() {
        $('#no-items-row').remove();

        let html = `
        <tr>
            <td class="align-middle">
                <select class="form-control item-select" name="items[${index}][item_id]" data-row="${index}" required>
                    <option value="">Seleccionar ítem...</option>
                    @foreach ($items as $it)
                        <option value="{{ $it->id }}" data-stock="{{ $it->cantidad }}">
                            {{ $it->codigo }} — {{ $it->descripcion }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-1">
                    <span class="badge badge-secondary shadow-sm">Stock: <span class="stock-badge" data-row="${index}">—</span></span>
                </div>
            </td>
            <td class="align-middle text-center">
                <input type="number" class="form-control text-center cantidad-input shadow-sm" min="1" name="items[${index}][cantidad]" data-row="${index}" placeholder="0" required>
            </td>
            <td class="align-middle">
                <select class="form-control shadow-sm" name="items[${index}][estado_item]" required>
                    <option value="USO_PROPIO">USO PROPIO</option>
                    <option value="DE_VENTA">DE VENTA</option>
                    <option value="COMPRADO">COMPRADO</option>
                </select>
            </td>
            <td class="align-middle">
                <input type="date" class="form-control shadow-sm" id="date_${index}" name="items[${index}][fecha_siguiente]">
                <div class="btn-group btn-group-toggle w-100 mt-1 shadow-sm">
                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate(${index}, 3)">
                        <i class="fas fa-calendar-plus"></i> 3 Meses
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate(${index}, 6)">
                        <i class="fas fa-calendar-plus"></i> 6 Meses
                    </button>
                </div>
            </td>
            <td class="align-middle text-center">
                <button type="button" class="btn btn-outline-danger btn-sm shadow-sm" onclick="this.closest('tr').remove()" title="Eliminar Ítem">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

        $('#tabla-items tbody').append(html);
        index++;
    }

    function calcDate(rowId, months) {
        const fechaDotacion = $('input[name="fecha"]').val();
        if (!fechaDotacion) {
            alert("⚠️ Por favor, seleccione primero la fecha principal de la dotación arriba.");
            return;
        }

        let date = new Date(fechaDotacion + 'T00:00:00');
        date.setMonth(date.getMonth() + months);

        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        let year = date.getFullYear();

        $(`#date_${rowId}`).val(`${year}-${month}-${day}`);
    }

    $(document).on('change', '.item-select', function() {
        const row = $(this).data('row');
        const stock = $(this).find('option:selected').data('stock');
        $(`.stock-badge[data-row="${row}"]`).text(stock || '—');
    });

    $(document).on('input', '.cantidad-input', function() {
        const row = $(this).data('row');
        const cantidad = parseInt($(this).val()) || 0;
        const stock = parseInt($(`.stock-badge[data-row="${row}"]`).text()) || 0;

        if (cantidad > stock) {
            $(this).addClass('is-invalid border-danger');
        } else {
            $(this).removeClass('is-invalid border-danger');
        }
    });

    $(function() {
        $('.item-select').each(function() {
            const row = $(this).data('row');
            const stock = $(this).find('option:selected').data('stock');
            $(`.stock-badge[data-row="${row}"]`).text(stock || '0');
        });
    });
</script>
@endpush
