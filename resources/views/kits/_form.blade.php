@php $isEdit = isset($kit) && $kit; @endphp

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label"><i class="fas fa-hashtag"></i> Código</label>
        <input type="text" class="form-control" value="Se autogenerará (KIT-0001, …)" disabled>
        <small class="text-muted">El código se genera al guardar.</small>
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label">Nombre del kit <span class="text-danger">*</span></label>
        <input name="nombre" class="form-control" value="{{ old('nombre', $kit->nombre ?? '') }}" required>
        @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label"><i class="fas fa-align-left"></i> Descripción</label>
        <textarea name="descripcion" rows="2" class="form-control"
                  placeholder="Breve detalle del contenido o uso del kit…">{{ old('descripcion', $kit->descripcion ?? '') }}</textarea>
        @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<hr>

<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="fas fa-toolbox"></i> Ítems del kit</h3>
        <small class="text-muted">El buscador es local y reactivo (no hace más consultas).</small>
    </div>

    <div class="card-body">
        <div class="mb-2">
            <label class="form-label"><i class="fas fa-search"></i> Buscar ítems</label>
            <div class="input-group">
                <input id="qItems" type="text" class="form-control"
                       placeholder="Escribe código / descripción / fabricante y presiona Enter para agregar…">
                <button id="btnFind" type="button" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <small class="text-muted">Se filtra en memoria (máx. 1000). Usa ↑/↓ y Enter para seleccionar.</small>
        </div>

        <div id="boxResults" class="mt-3" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0"><i class="fas fa-list"></i> Resultados</h6>
                <button type="button" id="btnHideResults" class="btn btn-sm btn-light">Ocultar</button>
            </div>

            <div class="table-responsive border rounded">
                <table class="table table-sm table-hover mb-0" id="tblResults">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:160px">Código</th>
                            <th>Descripción</th>
                            <th style="width:100px" class="text-end">Stock</th>
                            <th style="width:80px">Med</th>
                            <th style="width:80px" class="text-center">Agregar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <h6 class="mb-2"><i class="fas fa-check-circle"></i> Seleccionados</h6>
            <div class="table-responsive border rounded">
                <table class="table table-sm align-middle mb-0" id="tblSelected">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:160px">Código</th>
                            <th>Descripción</th>
                            <th style="width:120px" class="text-end">Stock</th>
                            <th style="width:80px">Med</th>
                            <th style="width:130px" class="text-end">Cantidad</th>
                            <th style="width:90px" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Ítems en el kit</th>
                            <th class="text-end" id="ftTotal">0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <small class="text-muted">Puedes escribir libremente; se limita al stock máximo automáticamente.</small>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button class="btn btn-primary">
        <i class="fas fa-save"></i> {{ $isEdit ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('kits.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Cancelar
    </a>
</div>

@push('js')
<script>
(function () {
    const ITEMS     = @json($itemsForJs ?? []);
    const PRESELECT = @json($preselect ?? []);

    const mapById  = new Map(ITEMS.map(x => [x.id, x]));
    const selected = new Map();
    PRESELECT.forEach(r => {
        if (mapById.has(r.id)) selected.set(r.id, { id: r.id, cant: Math.max(1, r.cant) });
    });

    const q        = document.getElementById('qItems');
    const btn      = document.getElementById('btnFind');
    const boxRes   = document.getElementById('boxResults');
    const tblRes   = document.querySelector('#tblResults tbody');
    const btnHide  = document.getElementById('btnHideResults');
    const tblSel   = document.querySelector('#tblSelected tbody');
    const ftTotal  = document.getElementById('ftTotal');

    const norm = s => (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    function renderResults(rows) {
        tblRes.innerHTML = '';
        rows.slice(0, 25).forEach((r, idx) => {
            const tr = document.createElement('tr');
            tr.tabIndex = 0;
            tr.innerHTML = `
                <td><span class="badge bg-light text-dark border">${r.codigo}</span></td>
                <td>${r.desc}</td>
                <td class="text-end">${r.stock}</td>
                <td>${r.med ?? '—'}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary btn-add" data-id="${r.id}">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>`;
            tr.addEventListener('dblclick', () => addItem(r.id));
            tr.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addItem(r.id); } });
            tblRes.appendChild(tr);
            if (idx === 0) tr.focus();
        });
        boxRes.style.display = rows.length ? '' : 'none';
    }

    function recomputeTotal() {
        let total = 0;
        selected.forEach(s => total += (parseInt(s.cant, 10) || 0));
        ftTotal.textContent = total;
    }

    function renderSelected() {
        tblSel.innerHTML = '';
        selected.forEach(({ id, cant }) => {
            const i = mapById.get(id);
            const tr = document.createElement('tr');

            const tdCode = `<td><span class="badge bg-light text-dark border">${i.codigo}</span></td>`;
            const tdDesc = `<td>${i.desc}</td>`;
            const tdStk  = `<td class="text-end">${i.stock}</td>`;
            const tdMed  = `<td>${i.med ?? '—'}</td>`;

            const tdCant = `
                <td class="text-end">
                    <input type="number"
                           class="form-control form-control-sm qty-input"
                           value="${cant}"
                           data-id="${id}"
                           data-max="${Math.max(1, i.stock)}"
                           inputmode="numeric"
                           style="width:110px; display:inline-block;">
                    <input type="hidden" name="item_ids[]" value="${id}">
                    <input type="hidden" name="cantidades[]" class="hidden-cant" value="${cant}">
                </td>`;

            const tdDel  = `
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-del" data-id="${id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>`;

            tr.innerHTML = tdCode + tdDesc + tdStk + tdMed + tdCant + tdDel;
            tblSel.appendChild(tr);
        });
        recomputeTotal();
    }

    function addItem(id) {
        if (!mapById.has(id)) return;
        if (!selected.has(id)) {
            const base = mapById.get(id);
            const cant = Math.min(1, base.stock) || 1;
            selected.set(id, { id, cant });
            renderSelected();
        }
    }

    let t;
    const deb = (fn, ms = 200) => { clearTimeout(t); t = setTimeout(fn, ms); };

    function applyFilter() {
        const nq = norm(q.value);
        if (!nq) { boxRes.style.display = 'none'; tblRes.innerHTML = ''; return; }
        const parts = nq.split(/\s+/).filter(Boolean);
        const out = ITEMS.filter(it => {
            const hay = norm(`${it.codigo} ${it.desc}`);
            return parts.every(p => hay.includes(p));
        });
        renderResults(out);
    }

    q.addEventListener('input', () => deb(applyFilter, 160));
    q.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            const first = tblRes.querySelector('tr .btn-add');
            if (first) { addItem(parseInt(first.dataset.id)); e.preventDefault(); }
        }
    });
    btn.addEventListener('click', applyFilter);
    btnHide.addEventListener('click', () => { boxRes.style.display = 'none'; });

    tblRes.addEventListener('click', e => {
        const addBtn = e.target.closest('.btn-add');
        if (addBtn) addItem(parseInt(addBtn.dataset.id));
    });

    // Permitir escribir libre y sólo capear por arriba mientras tipea
    tblSel.addEventListener('input', e => {
        if (!e.target.classList.contains('qty-input')) return;

        const inp  = e.target;
        const tr   = inp.closest('tr');
        const hid  = tr.querySelector('.hidden-cant');
        const max  = parseInt(inp.dataset.max, 10) || 999999;

        let v = inp.value.trim();

        if (/^\d+$/.test(v)) {
            let n = parseInt(v, 10);
            if (n > max) { n = max; inp.value = n; }
            hid.value = n;
        } else {
            // vacío, '-', etc. -> hidden a 0 temporalmente
            hid.value = 0;
        }

        const code = tr.querySelector('td .badge').textContent.trim();
        const item = ITEMS.find(x => x.codigo === code);
        if (item && selected.has(item.id)) {
            selected.get(item.id).cant = parseInt(hid.value, 10) || 0;
        }
        recomputeTotal();
    });

    // Al salir del input: normalizo a 1..max
    tblSel.addEventListener('blur', e => {
        if (!e.target.classList.contains('qty-input')) return;

        const inp  = e.target;
        const tr   = inp.closest('tr');
        const hid  = tr.querySelector('.hidden-cant');
        const max  = parseInt(inp.dataset.max, 10) || 999999;

        let n = parseInt(inp.value, 10);
        if (isNaN(n) || n < 1) n = 1;
        if (n > max) n = max;

        inp.value = n;
        hid.value = n;

        const code = tr.querySelector('td .badge').textContent.trim();
        const item = ITEMS.find(x => x.codigo === code);
        if (item && selected.has(item.id)) {
            selected.get(item.id).cant = n;
        }
        recomputeTotal();
    }, true);

    // Quitar fila
    tblSel.addEventListener('click', e => {
        const delBtn = e.target.closest('.btn-del');
        if (!delBtn) return;
        const id = parseInt(delBtn.dataset.id);
        selected.delete(id);
        renderSelected();
    });

    // Normalizo todas las cantidades justo antes de enviar
    const formEl = document.querySelector('form');
    if (formEl) {
        formEl.addEventListener('submit', () => {
            document.querySelectorAll('.qty-input').forEach(inp => {
                const tr  = inp.closest('tr');
                const hid = tr.querySelector('.hidden-cant');
                const max = parseInt(inp.dataset.max, 10) || 999999;

                let n = parseInt(inp.value, 10);
                if (isNaN(n) || n < 1) n = 1;
                if (n > max) n = max;

                inp.value = n;
                hid.value = n;
            });
        });
    }

    renderSelected();
})();
</script>
@endpush
