<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i> Ítems Asignados
        </h3>
        <div class="card-tools">
            <button type="button"
                    id="add-item"
                    class="btn btn-success btn-sm"
                    onclick="addItem()">
                <i class="fas fa-plus"></i> Añadir Ítem
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="callout callout-info">
            <h5><i class="fas fa-info-circle"></i> Sobre los items:</h5>
            <p class="mb-0">Agregue los items que se asignarán a la persona. El stock se descontará automáticamente del inventario.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabla-items">
                <thead class="bg-light">
                    <tr>
                        <th width="60%">Ítem</th>
                        <th width="20%">Cantidad</th>
                        <th width="10%" class="text-center">Stock</th>
                        <th width="10%" class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @if($dotacion)
                        @foreach($dotacion->items as $idx => $di)
                        <tr>
                            <td>
                                <select class="form-control item-select"
                                        name="items[{{ $idx }}][item_id]"
                                        data-row="{{ $idx }}"
                                        required>
                                    @foreach($items as $it)
                                        <option value="{{ $it->id }}"
                                                data-stock="{{ $it->cantidad }}"
                                                @selected($di->item_id == $it->id)>
                                            {{ $it->codigo }} — {{ $it->descripcion }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden"
                                       name="items[{{ $idx }}][dotacion_item_id]"
                                       value="{{ $di->id }}">
                            </td>

                            <td>
                                <input type="number"
                                       min="1"
                                       class="form-control cantidad-input"
                                       name="items[{{ $idx }}][cantidad]"
                                       data-row="{{ $idx }}"
                                       value="{{ $di->cantidad }}"
                                       required>
                            </td>

                            <td class="text-center align-middle">
                                <span class="badge badge-info stock-badge" data-row="{{ $idx }}">
                                    {{ $items->firstWhere('id', $di->item_id)->cantidad ?? 0 }}
                                </span>
                            </td>

                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="this.closest('tr').remove()"
                                        data-toggle="tooltip"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr id="no-items-row">
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay ítems agregados. Haga clic en "Añadir Ítem"</p>
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
let index = {{ $dotacion ? $dotacion->items->count() : 0 }};

function addItem() {
    // Remover mensaje de "sin ítems"
    $('#no-items-row').remove();

    let html = `
    <tr>
        <td>
            <select class="form-control item-select"
                    name="items[${index}][item_id]"
                    data-row="${index}"
                    required>
                <option value="">— Seleccionar ítem —</option>
                @foreach($items as $it)
                    <option value="{{ $it->id }}" data-stock="{{ $it->cantidad }}">
                        {{ $it->codigo }} — {{ $it->descripcion }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number"
                   class="form-control cantidad-input"
                   min="1"
                   name="items[${index}][cantidad]"
                   data-row="${index}"
                   required>
        </td>

        <td class="text-center align-middle">
            <span class="badge badge-info stock-badge" data-row="${index}">—</span>
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-danger btn-sm"
                    onclick="this.closest('tr').remove()"
                    data-toggle="tooltip"
                    title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
    `;

    $('#tabla-items tbody').append(html);
    index++;

    // Inicializar tooltips y eventos
    $('[data-toggle="tooltip"]').tooltip();
    updateStockBadges();
}

// Actualizar badge de stock al cambiar item
$(document).on('change', '.item-select', function() {
    const row = $(this).data('row');
    const stock = $(this).find('option:selected').data('stock');
    $(`.stock-badge[data-row="${row}"]`).text(stock || '—');
});

// Validar cantidad vs stock
$(document).on('input', '.cantidad-input', function() {
    const row = $(this).data('row');
    const cantidad = parseInt($(this).val()) || 0;
    const stock = parseInt($(`.stock-badge[data-row="${row}"]`).text()) || 0;

    if (cantidad > stock) {
        $(this).addClass('is-invalid');
        if (!$(this).next('.invalid-feedback').length) {
            $(this).after('<div class="invalid-feedback">Cantidad supera el stock disponible</div>');
        }
    } else {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});

// Inicializar badges al cargar
function updateStockBadges() {
    $('.item-select').each(function() {
        const row = $(this).data('row');
        const stock = $(this).find('option:selected').data('stock');
        $(`.stock-badge[data-row="${row}"]`).text(stock || '—');
    });
}

$(function() {
    updateStockBadges();
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@push('css')
<style>
    .callout {
        border-left-width: 5px;
    }
</style>
@endpush
