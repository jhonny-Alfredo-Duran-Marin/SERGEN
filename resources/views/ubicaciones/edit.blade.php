@extends('adminlte::page')

@section('title', 'Editar Ubicación')

@section('content_header')
    <h1>Editar Ubicación: {{ $ubicacion->descripcion }}</h1>
@stop

@section('content')
    @can('areas.update')
        <div class="card card-info">
            <form method="POST" action="{{ route('ubicaciones.update',$ubicacion) }}" class="card card-outline card-warning">
                 @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                            value="{{ old('descripcion', $ubicacion->descripcion) }}">
                        @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <select name="area_id" class="form-control">
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ $ubicacion->area_id == $area->id ? 'selected' : '' }}>
                                {{-- Cambiado de 'nombre' a 'descripcion' para ser consistente --}}
                                {{ $area->descripcion }} - {{ $area->sucursal->descripcion }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="Activo" {{ $ubicacion->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Pasivo" {{ $ubicacion->estado == 'Pasivo' ? 'selected' : '' }}>Pasivo</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Actualizar Cambios</button>
                    <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">Regresar</a>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-danger">No tienes permiso para editar este registro.</div>
    @endcan
@stop
