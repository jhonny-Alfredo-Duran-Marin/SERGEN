@php
    $isEdit = isset($prestamo);
    $nextCode = $isEdit ? $prestamo->codigo : \App\Models\Prestamo::generateCode();

    /* ITEMS SUELTOS */
    $itemsJson = $items->map(fn($i) => [
        'id' => $i->id,
        'tipo' => 'item',
        'codigo' => $i->codigo,
        'nombre' => $i->descripcion,
        'stock' => $i->cantidad,
        'costo' => $i->costo_unitario,
    ])->toJson();

    /* KITS */
    $kitsJson = \App\Models\KitEmergencia::with('items')->get()->map(function($kit) {
        return [
            'id' => $kit->id,
            'codigo' => $kit->codigo,
            'nombre' => $kit->nombre,
            'items' => $kit->items->map(fn($item) => [
                'id' => $item->id,
                'codigo' => $item->codigo,
                'nombre' => $item->descripcion,
                'cantidad_kit' => $item->pivot->cantidad,
                'stock' => $item->cantidad,
                'costo' => $item->costo_unitario
            ])
        ];
    })->toJson();
@endphp

<div class="card card-primary card-outline">
    <div class="card-header bg-primary">
        <h3 class="card-title">{{ $isEdit ? 'Editar Préstamo' : 'Nuevo Préstamo' }}</h3>
        <div class="card-tools">
            <span class="badge badge-light fs-5">Código: <strong>{{ $nextCode }}</strong></span>
        </div>
    </div>

    <form method="POST" action="{{ $isEdit ? route('prestamos.update', $prestamo) : route('prestamos.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="codigo" value="{{ $nextCode }}">

        <div class="card-body">

            {{-- FECHA --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control"
                        value="{{ old('fecha', $isEdit ? $prestamo->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        required>
                </div>
            </div>

            {{-- PERSONA y PROYECTO --}}
            <div class="row">
                <div class="col-md-6">
                    <label>Persona <span class="text-danger">*</span></label>
                    <select name="persona_id" class="form-control select2" required>
                        <option value="">Seleccionar...</option>
                        @foreach($personas as $p)
                            <option value="{{ $p->id }}" @selected(old('persona_id', $prestamo->persona_id ?? '') == $p->id)>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Proyecto</label>
                    <select name="proyecto_id" class="form-control select2">
                        <option value="">Sin proyecto</option>
                        @foreach($proyectos as $pr)
                            <option value="{{ $pr->id }}" @selected(old('proyecto_id', $prestamo->proyecto_id ?? '') == $pr->id)>
                                {{ $pr->codigo }} — {{ $pr->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- KIT DE EMERGENCIA --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Kit de Emergencia</label>
                    <select id="kit-select" name="kit_emergencia_id" class="form-control select2">
                        <option value="">Ninguno</option>
                        @foreach(\App\Models\KitEmergencia::all() as $k)
                            <option value="{{ $k->id }}" @selected(old('kit_emergencia_id', $prestamo->kit_emergencia_id ?? '') == $k->id)>
                                {{ $k->codigo }} - {{ $k->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Nota</label>
                    <textarea name="nota" class="form-control" rows="2">{{ old('nota', $prestamo->nota ?? '') }}</textarea>
                </div>
            </div>

            {{-- PREVIEW DEL KIT --}}
            <div id="kit-preview" class="callout callout-info mt-3" style="display:none;">
                <h5>Ítems del kit:</h5>
                <ul id="kit-items-list" class="mb-0"></ul>
            </div>

            <hr>

            {{-- BUSCAR ÍTEM SUELTO --}}
            <label>Agregar ítem suelto</label>
            <input type="text" id="buscar-item" class="form-control" placeholder="Buscar código o nombre...">
            <div id="resultados"
                class="mt-2 border bg-white rounded shadow"
                style="max-height:300px; overflow-y:auto; display:none; position:absolute; width:95%; z-index:1000;"></div>

            {{-- TABLA --}}
            <div class="table-responsive mt-4">
                <table class="table table-bordered" id="tabla-items">
                    <thead class="thead-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Stock</th>
                            <th>Cantidad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- ITEMS EDITADOS --}}
                        @if($isEdit)
                            @foreach($prestamo->detalles as $d)
                                <tr data-id="{{ $d->item_id }}">
                                    <td><span class="badge badge-info">Ítem</span></td>
                                    <td>{{ $d->item->codigo }}</td>
                                    <td>{{ $d->item->descripcion }}</td>
                                    <td>Bs {{ number_format($d->costo_unitario,2) }}</td>
                                    <td>{{ $d->item->cantidad + $d->cantidad_prestada }}</td>
                                    <td>
                                        <input type="number" name="cant_item[{{ $d->item_id }}]"
                                            value="{{ $d->cantidad_prestada }}" min="1"
                                            class="form-control form-control-sm" style="width:80px;">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remover">Quitar</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">TOTAL ÍTEMS SUELTOS:</th>
                            <th id="total-items">0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        <div class="card-footer">
            <button class="btn btn-success float-right px-5">
                {{ $isEdit ? 'Actualizar' : 'Registrar' }}
            </button>
            <a href="{{ route('prestamos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

    </form>
</div>

@push('css')
<style>
    .hover-item:hover { background:#f0f0f0; cursor:pointer; }
    .callout { border-left:5px solid #17a2b8; }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const items = {!! $itemsJson !!};
    const kits  = {!! $kitsJson !!};

    const tbody = document.querySelector('#tabla-items tbody');
    const totalEl = document.getElementById('total-items');

    const kitSelect = document.getElementById('kit-select');
    const kitPreview = document.getElementById('kit-preview');
    const kitList = document.getElementById('kit-items-list');

    const searchInput = document.getElementById('buscar-item');
    const resultados = document.getElementById('resultados');

    const actualizarTotal = () =>
        totalEl.textContent = tbody.children.length;


    /* =============================
        MOSTRAR ITEMS DEL KIT
       ============================= */
    function mostrarKit(id) {

        kitList.innerHTML = "";

        if (!id) {
            kitPreview.style.display = "none";
            return;
        }

        const kit = kits.find(k => k.id == id);
        if (!kit) return;

        kit.items.forEach(i => {
            const li = document.createElement("li");
            li.innerHTML = `
                <strong>${i.cantidad_kit}x</strong> –
                ${i.codigo} — ${i.nombre}
                <span class="text-muted">(Bs ${i.costo})</span>
            `;
            kitList.appendChild(li);
        });

        kitPreview.style.display = "block";
    }

    /* =============================
        CARGAR KIT SELECCIONADO
       ============================= */
    kitSelect.addEventListener('change', () => {
        mostrarKit(kitSelect.value);
    });

    @if($isEdit && $prestamo->kit_emergencia_id)
        mostrarKit({{ $prestamo->kit_emergencia_id }});
    @endif

    /* =============================
        BUSCAR Y AGREGAR ITEM
       ============================= */
    searchInput.addEventListener('input', function () {

        const term = this.value.toLowerCase();
        resultados.innerHTML = "";

        if (!term) return resultados.style.display = 'none';

        const match = items.filter(i =>
            i.codigo.toLowerCase().includes(term) ||
            i.nombre.toLowerCase().includes(term)
        ).slice(0,10);

        if (!match.length) {
            resultados.innerHTML =
                `<div class="p-3 text-center text-muted">No encontrado</div>`;
            resultados.style.display = 'block';
            return;
        }

        match.forEach(m => {
            const div = document.createElement('div');
            div.className = "border-bottom p-2 hover-item";
            div.innerHTML =
                `<strong>${m.codigo}</strong> — ${m.nombre}
                (Bs ${m.costo}) <span class="text-muted">[Stock: ${m.stock}]</span>`;

            div.onclick = () => {
                agregarFila(m);
                resultados.style.display = 'none';
                searchInput.value = "";
            };

            resultados.appendChild(div);
        });

        resultados.style.display = 'block';
    });

    function agregarFila(item) {

        if (document.querySelector(`tr[data-id="${item.id}"]`)) return;

        const tr = document.createElement("tr");
        tr.dataset.id = item.id;

        tr.innerHTML = `
            <td><span class="badge badge-info">Ítem</span></td>
            <td>${item.codigo}</td>
            <td>${item.nombre}</td>
            <td>Bs ${item.costo}</td>
            <td>${item.stock}</td>
            <td><input type="number" name="cant_item[${item.id}]"
                class="form-control form-control-sm" min="1" value="1" style="width:80px;"></td>
            <td><button type="button" class="btn btn-danger btn-sm remover">Quitar</button></td>
        `;

        tbody.appendChild(tr);
        actualizarTotal();
    }

    tbody.addEventListener('click', e => {
        if (e.target.classList.contains('remover')) {
            e.target.closest('tr').remove();
            actualizarTotal();
        }
    });

    actualizarTotal();

});
</script>
@endpush
