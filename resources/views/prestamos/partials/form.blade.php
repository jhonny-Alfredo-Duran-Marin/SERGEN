{{-- resources/views/prestamos/partials/form.blade.php --}}
@php
    $isEdit = isset($prestamo);
    $nextCode = $isEdit ? $prestamo->codigo : \App\Models\Prestamo::generateCode();

    // ITEMS SUELTOS
    $itemsJson = $items->map(fn($i) => [
        'id' => $i->id,
        'tipo' => 'item',
        'codigo' => $i->codigo,
        'nombre' => $i->descripcion,
        'stock' => $i->cantidad
    ])->toJson();

    // KITS DE EMERGENCIA
    $kitsJson = \App\Models\KitEmergencia::with('items')->get()->map(function($kit) {
        return [
            'id' => $kit->id,
            'codigo' => $kit->codigo,
            'nombre' => $kit->nombre,
            'items' => $kit->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'codigo' => $item->codigo,
                    'nombre' => $item->descripcion,
                    'cantidad_kit' => $item->pivot->cantidad,
                    'stock' => $item->cantidad
                ];
            })->toArray()
        ];
    })->toJson();
@endphp

<div class="card card-primary card-outline">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            {{ $isEdit ? 'Editar Préstamo' : 'Nuevo Préstamo' }}
        </h3>
        <div class="card-tools">
            <span class="badge badge-light fs-5">Código: <strong>{{ $nextCode }}</strong></span>
        </div>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('prestamos.update', $prestamo) : route('prestamos.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="codigo" value="{{ $nextCode }}">

        <div class="card-body">
            <!-- FECHA -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control" required
                               value="{{ old('fecha', $isEdit ? $prestamo->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <!-- PERSONA Y PROYECTO -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Persona (quien recibe) <span class="text-danger">*</span></label>
                        <select name="persona_id" class="form-control select2" required style="width: 100%;">
                            <option value="">Seleccionar persona...</option>
                            @foreach($personas as $p)
                                <option value="{{ $p->id }}" {{ old('persona_id', $prestamo->persona_id ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Proyecto (opcional)</label>
                        <select name="proyecto_id" class="form-control select2" style="width: 100%;">
                            <option value="">Sin proyecto</option>
                            @foreach($proyectos as $pr)
                                <option value="{{ $pr->id }}" {{ old('proyecto_id', $prestamo->proyecto_id ?? '') == $pr->id ? 'selected' : '' }}>
                                    {{ $pr->codigo }} — {{ $pr->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- KIT DE EMERGENCIA -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kit de Emergencia (opcional)</label>
                        <select id="kit-select" name="kit_emergencia_id" class="form-control select2" style="width: 100%;">
                            <option value="">Ningún kit</option>
                            @foreach(\App\Models\KitEmergencia::all() as $k)
                                <option value="{{ $k->id }}" {{ old('kit_emergencia_id', $prestamo->kit_emergencia_id ?? '') == $k->id ? 'selected' : '' }}>
                                    {{ $k->codigo }} — {{ $k->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nota</label>
                        <textarea name="nota" rows="2" class="form-control" placeholder="Opcional...">{{ old('nota', $prestamo->nota ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- VISTA PREVIA DEL KIT -->
            <div id="kit-preview" class="callout callout-info" style="display:none;">
                <h5>Ítems incluidos en el kit:</h5>
                <ul id="kit-items-list" class="list-unstyled mb-0"></ul>
            </div>

            <hr>

            <!-- BUSCAR ÍTEM SUELTO -->
            <div class="form-group">
                <label>Agregar ítem suelto</label>
                <div class="input-group">
                    <input type="text" id="buscar-item" class="form-control" placeholder="Código o nombre..." autocomplete="off">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default" id="limpiar-busqueda">Limpiar</button>
                    </div>
                </div>
                <div id="resultados" class="mt-2 border bg-white rounded shadow"
                     style="max-height:300px; overflow-y:auto; display:none; position:absolute; width:95%; z-index:1000;"></div>
            </div>

            <!-- TABLA DE ÍTEMS SELECCIONADOS -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered" id="tabla-items">
                    <thead class="thead-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th>Cantidad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($isEdit && $prestamo->detalles->count())
                            @foreach($prestamo->detalles as $d)
                                <tr data-id="{{ $d->item_id }}">
                                    <td><span class="badge badge-info">Ítem</span></td>
                                    <td>{{ $d->item->codigo }}</td>
                                    <td>{{ $d->item->descripcion }}</td>
                                    <td>{{ $d->item->cantidad + $d->cantidad_prestada }}</td>
                                    <td>
                                        <input type="number" name="cant_item[{{ $d->item_id }}]"
                                               value="{{ $d->cantidad_prestada }}" min="1" class="form-control form-control-sm" style="width:80px;">
                                    </td>
                                    <td><button type="button" class="btn btn-danger btn-sm remover">Quitar</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">TOTAL:</th>
                            <th id="total-items">0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success float-right px-4">
                {{ $isEdit ? 'Actualizar' : 'Registrar' }} Préstamo
            </button>
            <a href="{{ route('prestamos.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>

@push('css')
<style>
    .hover-item:hover { background:#f8f9fa !important; cursor:pointer; }
    .callout { border-left: 5px solid #17a2b8; }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = {!! $itemsJson !!};
    const kits = {!! $kitsJson !!};
    const tbody = document.querySelector('#tabla-items tbody');
    const totalEl = document.getElementById('total-items');
    const kitSelect = document.getElementById('kit-select');
    const kitPreview = document.getElementById('kit-preview');
    const kitList = document.getElementById('kit-items-list');
    const searchInput = document.getElementById('buscar-item');
    const resultados = document.getElementById('resultados');

    const actualizarTotal = () => totalEl.textContent = tbody.children.length;

    const agregarFila = (id, codigo, nombre, stock, cantidad = 1) => {
        if (document.querySelector(`tr[data-id="${id}"]`)) return;

        const tr = document.createElement('tr');
        tr.dataset.id = id;
        tr.innerHTML = `
            <td><span class="badge badge-info">Ítem</span></td>
            <td>${codigo}</td>
            <td>${nombre}</td>
            <td>${stock}</td>
            <td><input type="number" name="cant_item[${id}]" value="${cantidad}" min="1" class="form-control form-control-sm" style="width:80px;"></td>
            <td><button type="button" class="btn btn-danger btn-sm remover">Quitar</button></td>
        `;
        tbody.appendChild(tr);
        actualizarTotal();
    };

    // KIT CHANGE
    kitSelect.addEventListener('change', function() {
        const kitId = this.value;
        kitList.innerHTML = '';
        if (!kitId) {
            kitPreview.style.display = 'none';
            return;
        }

        const kit = kits.find(k => k.id == kitId);
        if (!kit) return;

        kit.items.forEach(i => {
            const li = document.createElement('li');
            li.innerHTML = `<strong>${i.cantidad_kit}x</strong> ${i.codigo} — ${i.nombre} (Stock: ${i.stock})`;
            kitList.appendChild(li);
            agregarFila(i.id, i.codigo, i.nombre, i.stock, i.cantidad_kit);
        });

        kitPreview.style.display = 'block';
    });

    // BUSCAR ÍTEM
    searchInput.addEventListener('input', function() {
        const term = this.value.trim().toLowerCase();
        resultados.innerHTML = '';
        if (!term) { resultados.style.display = 'none'; return; }

        const matches = items.filter(i =>
            i.codigo.toLowerCase().includes(term) ||
            i.nombre.toLowerCase().includes(term)
        ).slice(0, 10);

        if (!matches.length) {
            resultados.innerHTML = '<div class="p-3 text-center text-muted">No encontrado</div>';
            resultados.style.display = 'block';
            return;
        }

        matches.forEach(m => {
            const div = document.createElement('div');
            div.className = 'p-3 border-bottom hover-item';
            div.innerHTML = `<strong>${m.codigo}</strong> — ${m.nombre} (Stock: ${m.stock})`;
            div.onclick = () => {
                agregarFila(m.id, m.codigo, m.nombre, m.stock);
                searchInput.value = '';
                resultados.style.display = 'none';
            };
            resultados.appendChild(div);
        });
        resultados.style.display = 'block';
    });

    // QUITAR FILA
    tbody.addEventListener('click', e => {
        if (e.target.classList.contains('remover')) {
            e.target.closest('tr').remove();
            actualizarTotal();
        }
    });

    document.getElementById('limpiar-busqueda').addEventListener('click', () => {
        searchInput.value = '';
        resultados.style.display = 'none';
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#buscar-item') && !e.target.closest('#resultados')) {
            resultados.style.display = 'none';
        }
    });

    // INIT
    actualizarTotal();
    @if($isEdit && $prestamo->kit_emergencia_id)
        kitSelect.value = "{{ $prestamo->kit_emergencia_id }}";
        kitSelect.dispatchEvent(new Event('change'));
    @endif
});
</script>
@endpush
