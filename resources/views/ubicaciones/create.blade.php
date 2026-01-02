@extends('adminlte::page')

@section('title', 'Crear Ubicación')

@section('content_header')
    <h1>Crear Nueva Ubicación</h1>
@stop

@section('content')
    @can('areas.create')
    <div class="card card-primary">
        <form action="{{ route('ubicaciones.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion') }}" required>
                    @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="area_id">Área</label>
                    <select name="area_id" class="form-control @error('area_id') is-invalid @enderror" required>
                        <option value="">-- Seleccione un Área --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->descripcion }} - {{ $area->sucursal->descripcion }} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Pasivo">Pasivo</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Guardar Registro</button>
                <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    @else
        <div class="alert alert-danger">No tienes permisos para crear ubicaciones.</div>
    @endcan
@stop
