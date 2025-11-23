{{-- resources/views/prestamos/partials/select_persona.blade.php --}}
<select name="persona_id" class="form-select form-select-lg" required>
    <option value="">Seleccionar persona...</option>
    @foreach($personas as $p)
        <option value="{{ $p->id }}" {{ $selected == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
    @endforeach
</select>
