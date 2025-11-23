{{-- resources/views/prestamos/partials/select_proyecto.blade.php --}}
<select name="proyecto_id" class="form-select form-select-lg" required>
    <option value="">Seleccionar proyecto...</option>
    @foreach($proyectos as $pr)
        <option value="{{ $pr->id }}" {{ $selected == $pr->id ? 'selected' : '' }}>
            {{ $pr->codigo }} — {{ $pr->descripcion }}
        </option>
    @endforeach
</select>
