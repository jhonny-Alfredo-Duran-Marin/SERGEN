@php $isEdit = isset($kit) && $kit; @endphp

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label"><i class="fas fa-hashtag"></i> Código</label>
        {{-- Uso de $nextCode si es nuevo, o el código actual si es edición --}}
        <input type="text" class="form-control" value="{{ $isEdit ? $kit->codigo : $nextCode }}" disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nombre del kit <span class="text-danger">*</span></label>
        <input name="nombre" class="form-control" value="{{ old('nombre', $kit->nombre ?? '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Estado del Kit <span class="text-danger">*</span></label>
        <select name="estado" class="form-control" required>
            <option value="Activo" {{ old('estado', $kit->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
            <option value="Pasivo" {{ old('estado', $kit->estado ?? '') == 'Pasivo' ? 'selected' : '' }}>Pasivo</option>
            <option value="Observado" {{ old('estado', $kit->estado ?? '') == 'Observado' ? 'selected' : '' }}>Observado
            </option>
        </select>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Descripción del Kit</label>
        <textarea name="descripcion" class="form-control" rows="2" placeholder="Notas sobre el uso...">{{ old('descripcion', $kit->descripcion ?? '') }}</textarea>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="input-group mb-3">
            <input id="qItems" type="text" class="form-control" placeholder="Buscar ítems...">
        </div>

        <div id="boxResults" style="display:none; max-height: 250px; overflow-y: auto;" class="mb-4">
            <table class="table table-sm table-hover border">
                <thead class="bg-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tblResBody"></tbody>
            </table>
        </div>

        <h6><i class="fas fa-check-circle"></i> Ítems Seleccionados</h6>
        <table class="table table-sm align-middle border" id="tblSelected">
            <thead class="bg-light">
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th style="width:130px">Estado Ítem</th>
                    <th style="width:110px">Cantidad</th>
                    <th class="text-center">Quitar</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
    <a href="{{ route('kits.index') }}" class="btn btn-secondary">Cancelar</a>
</div>

@push('js')
    <script>
        (function() {
            const ITEMS = @json($itemsForJs ?? []);
            const PRESELECT = @json($preselect ?? []);
            const mapById = new Map(ITEMS.map(x => [x.id, x]));
            const selected = new Map();

            PRESELECT.forEach(r => {
                if (mapById.has(r.id)) selected.set(r.id, {
                    id: r.id,
                    cant: r.cant,
                    estado: r.estado || 'Activo'
                });
            });

            const q = document.getElementById('qItems'),
                boxRes = document.getElementById('boxResults'),
                tblRes = document.getElementById('tblResBody'),
                tblSel = document.querySelector('#tblSelected tbody');

            window.addItem = (id) => {
                if (selected.has(id)) return;
                selected.set(id, {
                    id,
                    cant: 1,
                    estado: 'Activo'
                });
                renderSelected();
                q.value = '';
                boxRes.style.display = 'none';
            };

            function renderSelected() {
                tblSel.innerHTML = '';
                selected.forEach((val, id) => {
                    const item = mapById.get(id);
                    const stockMax = item ? item.stock : val.cant; // Límite de seguridad

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
            <td><small class="badge border bg-light">${item.codigo}</small></td>
            <td>${item.desc}</td>
            <td>
                <select name="estados_items[]" class="form-control form-control-sm state-input" data-id="${id}">
                    <option value="Activo" ${val.estado === 'Activo' ? 'selected' : ''}>Activo</option>
                    <option value="Pasivo" ${val.estado === 'Pasivo' ? 'selected' : ''}>Pasivo</option>
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="cantidades[]"
                           class="form-control qty-input"
                           value="${val.cant}"
                           data-id="${id}"
                           min="1"
                           max="${stockMax}"
                           required>
                    <div class="input-group-append">
                        <span class="input-group-text">/ ${stockMax}</span>
                    </div>
                </div>
                <input type="hidden" name="item_ids[]" value="${id}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>`;
                    tblSel.appendChild(tr);
                });
            }

            // Reemplaza el evento de input por este más robusto:
            tblSel.addEventListener('input', e => {
                if (e.target.classList.contains('qty-input')) {
                    const id = parseInt(e.target.dataset.id);
                    const maxVal = parseInt(e.target.getAttribute('max'));
                    let currentVal = parseInt(e.target.value);

                    if (currentVal > maxVal) {
                        alert('Stock insuficiente. El máximo disponible para este ítem es: ' + maxVal);
                        e.target.value = maxVal;
                        currentVal = maxVal;
                    }

                    if (currentVal < 1 && e.target.value !== "") {
                        e.target.value = 1;
                        currentVal = 1;
                    }

                    if (selected.has(id)) {
                        selected.get(id).cant = e.target.value;
                    }
                }
            });
            
            window.removeItem = (id) => {
                selected.delete(id);
                renderSelected();
            };

            q.addEventListener('input', () => {
                const val = q.value.toLowerCase();
                if (!val) {
                    boxRes.style.display = 'none';
                    return;
                }
                const res = ITEMS.filter(i => (i.codigo + i.desc).toLowerCase().includes(val)).slice(0, 10);
                tblRes.innerHTML = res.map(i => `
            <tr>
                <td>${i.codigo}</td><td>${i.desc}</td><td>${i.stock}</td>
                <td class="text-center"><button type="button" onclick="addItem(${i.id})" class="btn btn-sm btn-primary">+</button></td>
            </tr>`).join('');
                boxRes.style.display = res.length ? '' : 'none';
            });

            tblSel.addEventListener('input', e => {
                if (e.target.classList.contains('qty-input')) {
                    const id = parseInt(e.target.dataset.id);
                    const maxVal = parseInt(e.target.getAttribute('max'));
                    let currentVal = parseInt(e.target.value);

                    if (currentVal > maxVal) {
                        alert('No puedes superar el stock disponible (' + maxVal + ')');
                        e.target.value = maxVal;
                        currentVal = maxVal;
                    }

                    if (currentVal < 1) e.target.value = 1;

                    selected.get(id).cant = e.target.value;
                }
            });

            tblSel.addEventListener('change', e => {
                const id = parseInt(e.target.dataset.id);
                if (e.target.classList.contains('state-input')) selected.get(id).estado = e.target.value;
            });

            renderSelected();
        })();
    </script>
@endpush
