<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label"><i class="fas fa-box"></i> Ítem <span class="text-danger">*</span></label>
    <select name="item_id" class="form-control" required>
      <option value="">— Seleccionar —</option>
      @foreach($items as $it)
        <option value="{{ $it->id }}" @selected((int)old('item_id', $dotacion->item_id ?? 0) === (int)$it->id)>
          {{ $it->codigo }} — {{ $it->descripcion }} (Stock: {{ $it->cantidad }})
        </option>
      @endforeach
    </select>
    @error('item_id') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="col-md-6 mb-3">
    <label class="form-label"><i class="fas fa-user"></i> Persona <span class="text-danger">*</span></label>
    <select name="persona_id" class="form-control" required>
      <option value="">— Seleccionar —</option>
      @foreach($personas as $p)
        <option value="{{ $p->id }}" @selected((int)old('persona_id', $dotacion->persona_id ?? 0) === (int)$p->id)>
          {{ $p->nombre }}
        </option>
      @endforeach
    </select>
    @error('persona_id') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label"><i class="fas fa-sort-numeric-up"></i> Cantidad <span class="text-danger">*</span></label>
    <input type="number" min="1" name="cantidad" class="form-control"
           value="{{ old('cantidad', $dotacion->cantidad ?? 1) }}" required>
    @error('cantidad') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label"><i class="fas fa-calendar-day"></i> Fecha <span class="text-danger">*</span></label>
    <input type="date" name="fecha" class="form-control"
           value="{{ old('fecha', optional($dotacion->fecha ?? now())->format('Y-m-d')) }}" required>
    @error('fecha') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="col-md-4 mb-3 d-flex align-items-end">
    <div class="text-muted">
      <i class="fas fa-info-circle"></i>
      Los ajustes de stock se aplican automáticamente al guardar.
    </div>
  </div>
</div>

<div class="d-flex gap-2">
  <button class="btn btn-primary">{{ ($mode ?? 'create') === 'create' ? 'Guardar' : 'Actualizar' }}</button>
  <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">Volver</a>
</div>
