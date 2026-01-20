<div class="card card-info card-outline shadow-sm">
    <div class="card-header bg-gradient-info">
        <h3 class="card-title">
            <i class="fas fa-box"></i> Ítems Asignados
        </h3>
    </div>

    <div class="card-body">
        <!-- Callout informativo -->
        <div class="callout callout-info mb-4 shadow-sm">
            <h5><i class="fas fa-info-circle text-info"></i> Gestión de Dotación:</h5>
            <p class="mb-0 text-muted">Busque y agregue productos, defina su origen y fecha de próxima renovación para el control de inventario.</p>
        </div>

        <!-- Buscador de Ítems -->
        <div class="card border-success mb-4 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-search"></i> Buscar y Agregar Ítem
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <label for="item-search" class="font-weight-bold">
                            <i class="fas fa-barcode"></i> Buscar por código o descripción:
                        </label>
                        <input type="text"
                               id="item-search"
                               class="form-control form-control-lg shadow-sm"
                               placeholder="Escriba el código o descripción del ítem..."
                               autocomplete="off">

                        <!-- Resultados de búsqueda -->
                        <div id="search-results" class="list-group mt-2 shadow" style="display: none; position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; width: calc(100% - 30px);"></div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success btn-lg btn-block shadow" onclick="clearSearch()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>

                <!-- Mensaje de ayuda -->
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb text-warning"></i>
                        Escriba al menos 2 caracteres para buscar. Presione Enter o haga clic en el resultado deseado.
                    </small>
                </div>
            </div>
        </div>

        <!-- Tabla de ítems -->
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
                            <tr data-item-id="{{ $di->item_id }}">
                                <td class="align-middle">
                                    <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $di->item_id }}">
                                    <input type="hidden" name="items[{{ $idx }}][dotacion_item_id]" value="{{ $di->id }}">

                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <span class="badge badge-primary badge-lg">{{ $di->item->codigo }}</span>
                                        </div>
                                        <div>
                                            <strong>{{ $di->item->descripcion }}</strong>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge badge-secondary shadow-sm">
                                            <i class="fas fa-warehouse"></i> Stock:
                                            <span class="stock-badge" data-row="{{ $idx }}">{{ $di->item->cantidad }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="number"
                                           min="1"
                                           class="form-control form-control-lg text-center cantidad-input shadow-sm"
                                           name="items[{{ $idx }}][cantidad]"
                                           data-row="{{ $idx }}"
                                           data-stock="{{ $di->item->cantidad }}"
                                           value="{{ $di->cantidad }}"
                                           required>
                                </td>
                                <td class="align-middle">
                                    <select class="form-control shadow-sm" name="items[{{ $idx }}][estado_item]" required>
                                        <option value="USO_PROPIO" @selected($di->estado_item == 'USO_PROPIO')>
                                            <i class="fas fa-user"></i> USO PROPIO
                                        </option>
                                        <option value="DE_VENTA" @selected($di->estado_item == 'DE_VENTA')>
                                            <i class="fas fa-shopping-cart"></i> DE VENTA
                                        </option>
                                        <option value="COMPRADO" @selected($di->estado_item == 'COMPRADO')>
                                            <i class="fas fa-dollar-sign"></i> COMPRADO
                                        </option>
                                    </select>
                                </td>
                                <td class="align-middle">
                                    <input type="date"
                                           class="form-control shadow-sm"
                                           id="date_{{ $idx }}"
                                           name="items[{{ $idx }}][fecha_siguiente]"
                                           value="{{ $di->fecha_siguiente ? $di->fecha_siguiente->format('Y-m-d') : '' }}">
                                    <div class="btn-group btn-group-toggle w-100 mt-1 shadow-sm">
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate({{ $idx }}, 3)">
                                            <i class="fas fa-calendar-plus"></i> 3M
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate({{ $idx }}, 6)">
                                            <i class="fas fa-calendar-plus"></i> 6M
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate({{ $idx }}, 12)">
                                            <i class="fas fa-calendar-plus"></i> 12M
                                        </button>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm shadow-sm"
                                            onclick="removeItem(this)"
                                            title="Eliminar Ítem">
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
                                <p class="small">Utilice el buscador para agregar productos a esta dotación</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Resumen -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="info-box bg-light shadow-sm">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-boxes"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Ítems</span>
                        <span class="info-box-number" id="total-items">
                            {{ isset($dotacion) ? $dotacion->items->count() : 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    let index = {{ isset($dotacion) ? $dotacion->items->count() : 0 }};
    let itemsData = @json($items); // Datos de todos los ítems
    let addedItemIds = new Set(); // Para evitar duplicados

    // Inicializar items ya agregados
    $(function() {
        @if(isset($dotacion) && $dotacion->items->count() > 0)
            @foreach($dotacion->items as $di)
                addedItemIds.add({{ $di->item_id }});
            @endforeach
        @endif

        updateTotalItems();
    });

    // Buscador de ítems con autocompletado
    $('#item-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const results = $('#search-results');

        if (searchTerm.length < 2) {
            results.hide().empty();
            return;
        }

        // Filtrar ítems
        const filteredItems = itemsData.filter(item => {
            const codigo = item.codigo.toLowerCase();
            const descripcion = item.descripcion.toLowerCase();
            return (codigo.includes(searchTerm) || descripcion.includes(searchTerm))
                   && !addedItemIds.has(item.id);
        });

        if (filteredItems.length === 0) {
            results.html('<div class="list-group-item text-muted"><i class="fas fa-search"></i> No se encontraron resultados</div>').show();
            return;
        }

        // Mostrar resultados
        let html = '';
        filteredItems.slice(0, 10).forEach(item => {
            const stockClass = item.cantidad > 10 ? 'success' : (item.cantidad > 0 ? 'warning' : 'danger');
            html += `
                <a href="#" class="list-group-item list-group-item-action search-result-item"
                   data-item-id="${item.id}"
                   data-codigo="${item.codigo}"
                   data-descripcion="${item.descripcion}"
                   data-stock="${item.cantidad}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-primary mr-2">${item.codigo}</span>
                            <strong>${item.descripcion}</strong>
                        </div>
                        <span class="badge badge-${stockClass}">
                            <i class="fas fa-warehouse"></i> Stock: ${item.cantidad}
                        </span>
                    </div>
                </a>
            `;
        });

        results.html(html).show();
    });

    // Agregar ítem al hacer clic en resultado
    $(document).on('click', '.search-result-item', function(e) {
        e.preventDefault();

        const itemId = $(this).data('item-id');
        const codigo = $(this).data('codigo');
        const descripcion = $(this).data('descripcion');
        const stock = $(this).data('stock');

        addItemToTable(itemId, codigo, descripcion, stock);

        // Limpiar búsqueda
        clearSearch();
    });

    // Agregar ítem con Enter
    $('#item-search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const firstResult = $('.search-result-item').first();
            if (firstResult.length) {
                firstResult.click();
            }
        }
    });

    // Función para agregar ítem a la tabla
    function addItemToTable(itemId, codigo, descripcion, stock) {
        // Verificar si ya está agregado
        if (addedItemIds.has(itemId)) {
            Swal.fire({
                icon: 'warning',
                title: 'Ítem Duplicado',
                text: 'Este ítem ya ha sido agregado a la dotación',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        $('#no-items-row').remove();

        const stockClass = stock > 10 ? 'success' : (stock > 0 ? 'warning' : 'danger');

        let html = `
        <tr data-item-id="${itemId}" class="item-row-new">
            <td class="align-middle">
                <input type="hidden" name="items[${index}][item_id]" value="${itemId}">

                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <span class="badge badge-primary badge-lg">${codigo}</span>
                    </div>
                    <div>
                        <strong>${descripcion}</strong>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge badge-${stockClass} shadow-sm">
                        <i class="fas fa-warehouse"></i> Stock:
                        <span class="stock-badge" data-row="${index}">${stock}</span>
                    </span>
                </div>
            </td>
            <td class="align-middle text-center">
                <input type="number"
                       class="form-control form-control-lg text-center cantidad-input shadow-sm"
                       min="1"
                       name="items[${index}][cantidad]"
                       data-row="${index}"
                       data-stock="${stock}"
                       placeholder="0"
                       value="1"
                       required>
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
                        <i class="fas fa-calendar-plus"></i> 3M
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate(${index}, 6)">
                        <i class="fas fa-calendar-plus"></i> 6M
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="calcDate(${index}, 12)">
                        <i class="fas fa-calendar-plus"></i> 12M
                    </button>
                </div>
            </td>
            <td class="align-middle text-center">
                <button type="button" class="btn btn-outline-danger btn-sm shadow-sm" onclick="removeItem(this)" title="Eliminar Ítem">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

        $('#tabla-items tbody').append(html);

        // Animación de entrada
        $('.item-row-new').hide().fadeIn(500).removeClass('item-row-new');

        // Marcar como agregado
        addedItemIds.add(itemId);
        index++;

        updateTotalItems();

        // Notificación
        toastr.success(`${codigo} agregado correctamente`, 'Ítem Agregado');
    }

    // Limpiar búsqueda
    function clearSearch() {
        $('#item-search').val('');
        $('#search-results').hide().empty();
        $('#item-search').focus();
    }

    // Cerrar resultados al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#item-search, #search-results').length) {
            $('#search-results').hide();
        }
    });

    // Función para eliminar ítem
    function removeItem(btn) {
        const row = $(btn).closest('tr');
        const itemId = parseInt(row.data('item-id'));

        Swal.fire({
            title: '¿Eliminar ítem?',
            text: "Se quitará este ítem de la dotación",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                row.fadeOut(300, function() {
                    $(this).remove();
                    addedItemIds.delete(itemId);
                    updateTotalItems();

                    // Si no quedan ítems, mostrar mensaje
                    if ($('#tabla-items tbody tr').length === 0) {
                        $('#tabla-items tbody').html(`
                            <tr id="no-items-row">
                                <td colspan="5" class="text-center text-muted py-5 bg-light">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p class="h5">No hay ítems agregados</p>
                                    <p class="small">Utilice el buscador para agregar productos a esta dotación</p>
                                </td>
                            </tr>
                        `);
                    }
                });

                toastr.info('Ítem eliminado', 'Dotación');
            }
        });
    }

    // Actualizar total de ítems
    function updateTotalItems() {
        const total = $('#tabla-items tbody tr').not('#no-items-row').length;
        $('#total-items').text(total);
    }

    // Calcular fecha
    function calcDate(rowId, months) {
        const fechaDotacion = $('input[name="fecha"]').val();

        if (!fechaDotacion) {
            toastr.warning('Por favor, seleccione primero la fecha principal de la dotación', 'Advertencia');
            return;
        }

        let date = new Date(fechaDotacion);

        if (isNaN(date.getTime())) {
            toastr.error('La fecha seleccionada no es válida', 'Error');
            return;
        }

        date.setMonth(date.getMonth() + months);

        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        let year = date.getFullYear();

        $(`#date_${rowId}`).val(`${year}-${month}-${day}`);
        toastr.success(`Fecha calculada: +${months} meses`, 'Fecha Actualizada');
    }

    // Validación de cantidad vs stock
    $(document).on('input', '.cantidad-input', function() {
        const cantidad = parseInt($(this).val()) || 0;
        const stock = parseInt($(this).data('stock')) || 0;
        const row = $(this).data('row');

        if (cantidad > stock) {
            $(this).addClass('is-invalid border-danger');
            toastr.warning(`La cantidad (${cantidad}) supera el stock disponible (${stock})`, 'Advertencia');
        } else {
            $(this).removeClass('is-invalid border-danger');
        }
    });
</script>
@endpush

@push('css')
<style>
    .search-result-item {
        transition: all 0.3s ease;
    }

    .search-result-item:hover {
        background-color: #e3f2fd;
        transform: translateX(5px);
    }

    #search-results {
        border-radius: 0.25rem;
        max-width: 100%;
    }

    .badge-lg {
        font-size: 0.9rem;
        padding: 0.4rem 0.6rem;
    }

    .item-row-new {
        background-color: #d4edda;
    }

    .table td {
        vertical-align: middle;
    }

    .cantidad-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>
@endpush
