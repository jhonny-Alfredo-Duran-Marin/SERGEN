@php
    $isEdit = isset($prestamo);
    $nextCode = $isEdit ? $prestamo->codigo : \App\Models\Prestamo::generateCode();
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <h5><i class="fas fa-exclamation-triangle"></i> Errores en el formulario:</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Información del Préstamo -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i> Información del Préstamo
        </h3>
        <div class="card-tools">
            <span class="badge badge-light badge-lg">
                <i class="fas fa-barcode"></i> Código: {{ $nextCode }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <input type="hidden" name="codigo" value="{{ $nextCode }}">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecha">
                        <i class="fas fa-calendar-alt"></i> Fecha y Hora del Préstamo
                        <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="fecha" id="fecha"
                        class="form-control @error('fecha') is-invalid @enderror" {{-- El formato Y-m-d\TH:i es obligatorio para datetime-local --}}
                        value="{{ old('fecha', $isEdit ? $prestamo->fecha->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                        required>
                    @error('fecha')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="persona_id">
                        <i class="fas fa-user"></i> Persona Responsable
                        <span class="text-danger">*</span>
                    </label>
                    <select name="persona_id" id="persona_id"
                        class="form-control select2 @error('persona_id') is-invalid @enderror" required>
                        <option value="">— Seleccionar persona —</option>
                        @foreach ($personas as $p)
                            <option value="{{ $p->id }}" @selected(old('persona_id', $prestamo->persona_id ?? '') == $p->id)>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('persona_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="proyecto_id">
                        <i class="fas fa-hard-hat"></i> Proyecto (Opcional)
                    </label>
                    <select name="proyecto_id" id="proyecto_id" class="form-control select2">
                        <option value="">— Sin proyecto específico —</option>
                        @foreach ($proyectos as $pr)
                            <option value="{{ $pr->id }}" @selected(old('proyecto_id', $prestamo->proyecto_id ?? '') == $pr->id)>
                                {{ $pr->codigo }} — {{ $pr->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selectores de Kits e Items -->
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-plus-square"></i> Agregar Kits e Ítems
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label>
                    <i class="fas fa-box-open"></i> Kits de Emergencia
                </label>
                <div class="input-group">
                    <select id="kit-select" class="form-control select2">
                        <option value="">— Seleccionar kit —</option>
                        @foreach (json_decode($kitsJson) as $k)
                            <option value="{{ $k->id }}">
                                {{ $k->codigo }} - {{ $k->nombre }}
                                (Bs {{ number_format($k->costo_total, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button type="button" id="btn-agregar-kit" class="btn btn-info">
                            <i class="fas fa-plus"></i> Agregar Kit
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">
                    Seleccione un kit y presione "Agregar Kit"
                </small>
            </div>

            <div class="col-md-6 position-relative">
                <label>
                    <i class="fas fa-search"></i> Buscar Ítem Individual
                </label>
                <input type="text" id="buscar-item" class="form-control"
                    placeholder="Buscar por código o descripción...">
                <div id="resultados" class="resultados-busqueda"></div>
                <small class="form-text text-muted">
                    Escriba para buscar items individuales
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Items Seleccionados -->
<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-list"></i> Items Seleccionados para el Préstamo
        </h3>
        <div class="card-tools">
            <span class="badge badge-success" id="count-items">
                0 items
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="tabla-items">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">Tipo</th>
                        <th width="15%">Código</th>
                        <th width="30%">Descripción</th>
                        <th width="12%" class="text-right">Costo Ref.</th>
                        <th width="10%" class="text-center">Stock</th>
                        <th width="13%" class="text-center">Cantidad</th>
                        <th width="10%" class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- Kits ya asignados (EDIT) --}}
                    @if ($isEdit && $prestamo->kits->count())
                        @foreach ($prestamo->kits as $kit)
                            @php
                                $costoKit = $kit->items->sum(fn($i) => $i->pivot->cantidad * $i->costo_unitario);
                            @endphp
                            <tr data-id="kit-{{ $kit->id }}" data-tipo="kit" data-costo="{{ $costoKit }}"
                                class="table-info">
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fas fa-box"></i> Kit
                                    </span>
                                </td>
                                <td><strong>{{ $kit->codigo }}</strong></td>
                                <td>
                                    <strong>{{ $kit->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        @foreach ($kit->items as $i)
                                            {{ $i->pivot->cantidad }}x {{ $i->descripcion }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </small>
                                    <input type="hidden" name="kits_seleccionados[]" value="{{ $kit->id }}">
                                </td>
                                <td class="text-right">
                                    <strong class="text-success">Bs {{ number_format($costoKit, 2) }}</strong>
                                </td>
                                <td class="text-center">—</td>
                                <td class="text-center">
                                    <span class="badge badge-info">1 kit</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remover"
                                        data-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Ítems sueltos (EDIT) --}}
                    @if ($isEdit)
                        @foreach ($prestamo->detalles as $d)
                            <tr data-id="item-{{ $d->item_id }}" data-tipo="item"
                                data-costo="{{ $d->costo_unitario }}">
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-cube"></i> Ítem
                                    </span>
                                </td>
                                <td>{{ $d->item->codigo }}</td>
                                <td>{{ $d->item->descripcion }}</td>
                                <td class="text-right">
                                    <strong class="text-success">Bs
                                        {{ number_format($d->costo_unitario, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">
                                        {{ $d->item->cantidad + $d->cantidad_prestada }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <input type="number" name="cant_item[{{ $d->item_id }}]"
                                        class="form-control form-control-sm cantidad-item text-center"
                                        value="{{ $d->cantidad_prestada }}" min="1"
                                        max="{{ $d->item->cantidad + $d->cantidad_prestada }}"
                                        oninput="actualizarTotales()" style="width: 80px; display: inline-block;">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remover"
                                        data-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Fila vacía --}}
                    <tr id="empty-row"
                        style="display: {{ $isEdit && ($prestamo->kits->count() > 0 || $prestamo->detalles->count() > 0) ? 'none' : '' }}">
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No hay items agregados. Use los selectores arriba para agregar kits o items individuales.
                            </p>
                        </td>
                    </tr>
                </tbody>

                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <th colspan="5" class="text-right">
                            <span class="text-muted">Total de items:</span>
                            <span class="badge badge-primary" id="total-filas">0</span>
                        </th>
                        <th colspan="2" class="text-right">
                            <span class="text-muted">Valor Total:</span>
                            <span class="text-success text-lg" id="txt-total-general">Bs 0.00</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Botones de Acción -->
<div class="card">
    <div class="card-body">
        <button type="submit" class="btn btn-{{ $isEdit ? 'warning' : 'success' }} btn-lg">
            <i class="fas fa-save"></i> {{ $isEdit ? 'Actualizar Préstamo' : 'Registrar Préstamo' }}
        </button>
        <a href="{{ route('prestamos.index') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-times"></i> Cancelar
        </a>
    </div>
</div>

@push('css')
    <style>
        .resultados-busqueda {
            position: absolute;
            top: 100%;
            left: 15px;
            right: 15px;
            z-index: 1050;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2px;
        }

        .hover-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .hover-item:hover {
            background: #f8f9fa;
        }

        .hover-item:last-child {
            border-bottom: none;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5em 0.75em;
        }

        .table-info {
            background-color: #d1ecf1 !important;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = {!! $itemsJson !!};
            const kits = {!! $kitsJson !!};

            const tbody = document.querySelector('#tabla-items tbody');
            const emptyRow = document.getElementById('empty-row');
            const kitSelect = document.getElementById('kit-select');
            const btnAddKit = document.getElementById('btn-agregar-kit');
            const searchInput = document.getElementById('buscar-item');
            const resultados = document.getElementById('resultados');
            const countBadge = document.getElementById('count-items');

            // ==============================
            // ACTUALIZAR TOTALES Y CONTADOR
            // ==============================
            window.actualizarTotales = () => {
                let total = 0;
                const filas = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.id !== 'empty-row');

                filas.forEach(fila => {
                    const costo = parseFloat(fila.dataset.costo) || 0;

                    if (fila.dataset.tipo === 'kit') {
                        total += costo;
                    } else {
                        const input = fila.querySelector('.cantidad-item');
                        const cant = input ? parseFloat(input.value) || 0 : 0;
                        total += cant * costo;
                    }
                });

                const totalFilas = document.getElementById('total-filas');
                const totalTxt = document.getElementById('txt-total-general');

                if (totalFilas) totalFilas.textContent = filas.length;
                if (totalTxt) totalTxt.textContent = `Bs ${total.toFixed(2)}`;
                if (countBadge) countBadge.textContent =
                    `${filas.length} ${filas.length === 1 ? 'item' : 'items'}`;

                // Mostrar/ocultar fila vacía
                if (emptyRow) {
                    emptyRow.style.display = filas.length === 0 ? '' : 'none';
                }
            };

            // ==============================
            // AGREGAR KIT
            // ==============================
            btnAddKit?.addEventListener('click', () => {
                const id = kitSelect.value;
                if (!id) {
                    alert('Por favor seleccione un kit');
                    return;
                }

                const kit = kits.find(k => k.id == id);
                if (!kit) return;

                if (document.querySelector(`tr[data-id="kit-${kit.id}"]`)) {
                    alert('Este kit ya fue agregado');
                    return;
                }

                const tr = document.createElement('tr');
                tr.dataset.id = `kit-${kit.id}`;
                tr.dataset.tipo = 'kit';
                tr.dataset.costo = kit.costo_total;
                tr.classList.add('table-info');

                tr.innerHTML = `
            <td><span class="badge badge-info"><i class="fas fa-box"></i> Kit</span></td>
            <td><strong>${kit.codigo}</strong></td>
            <td>
                <strong>${kit.nombre}</strong><br>
                <small class="text-muted">${kit.detalles.map(d => `${d.cantidad}x ${d.nombre}`).join(', ')}</small>
                <input type="hidden" name="kits_seleccionados[]" value="${kit.id}">
            </td>
            <td class="text-right"><strong class="text-success">Bs ${kit.costo_total.toFixed(2)}</strong></td>
            <td class="text-center">—</td>
            <td class="text-center"><span class="badge badge-info">1 kit</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remover" data-toggle="tooltip" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

                tbody.appendChild(tr);
                actualizarTotales();
                $(kitSelect).val('').trigger('change');
                $('[data-toggle="tooltip"]').tooltip();
            });

            // ==============================
            // BUSCADOR DE ITEMS
            // ==============================
            searchInput?.addEventListener('input', () => {
                const term = searchInput.value.toLowerCase().trim();
                resultados.innerHTML = '';

                if (!term) {
                    resultados.style.display = 'none';
                    return;
                }

                const found = items.filter(i =>
                    i.codigo.toLowerCase().includes(term) ||
                    i.nombre.toLowerCase().includes(term)
                ).slice(0, 8);

                if (found.length === 0) {
                    resultados.innerHTML =
                        '<div class="p-3 text-muted text-center">No se encontraron resultados</div>';
                    resultados.style.display = 'block';
                    return;
                }

                found.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'hover-item';
                    div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${item.codigo}</strong> — ${item.nombre}
                        <br>
                        <small class="text-muted">Stock: ${item.stock}</small>
                    </div>
                    <span class="text-success font-weight-bold">Bs ${item.costo.toFixed(2)}</span>
                </div>
            `;

                    div.onclick = () => {
                        agregarItem(item);
                        resultados.style.display = 'none';
                        searchInput.value = '';
                    };

                    resultados.appendChild(div);
                });

                resultados.style.display = 'block';
            });

            // Cerrar resultados al hacer click fuera
            document.addEventListener('click', (e) => {
                if (!searchInput?.contains(e.target) && !resultados?.contains(e.target)) {
                    resultados.style.display = 'none';
                }
            });

            function agregarItem(item) {
                if (document.querySelector(`tr[data-id="item-${item.id}"]`)) {
                    alert('Este item ya fue agregado');
                    return;
                }

                if (item.stock <= 0) {
                    alert('Este item no tiene stock disponible');
                    return;
                }

                const tr = document.createElement('tr');
                tr.dataset.id = `item-${item.id}`;
                tr.dataset.tipo = 'item';
                tr.dataset.costo = item.costo;

                tr.innerHTML = `
            <td><span class="badge badge-success"><i class="fas fa-cube"></i> Ítem</span></td>
            <td>${item.codigo}</td>
            <td>${item.nombre}</td>
            <td class="text-right"><strong class="text-success">Bs ${item.costo.toFixed(2)}</strong></td>
            <td class="text-center"><span class="badge badge-warning">${item.stock}</span></td>
            <td class="text-center">
                <input type="number" name="cant_item[${item.id}]"
                    class="form-control form-control-sm cantidad-item text-center"
                    value="1" min="1" max="${item.stock}"
                    oninput="actualizarTotales()"
                    style="width: 80px; display: inline-block;">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remover" data-toggle="tooltip" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

                tbody.appendChild(tr);
                actualizarTotales();
                $('[data-toggle="tooltip"]').tooltip();
            }

            // ==============================
            // REMOVER FILAS
            // ==============================
            tbody.addEventListener('click', e => {
                if (e.target.closest('.remover')) {
                    if (confirm('¿Eliminar este item del préstamo?')) {
                        e.target.closest('tr').remove();
                        actualizarTotales();
                    }
                }
            });

            // Inicializar
            actualizarTotales();
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
