@extends('adminlte::page')

@section('title','Nueva Categoría')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-plus-circle"></i> Crear Nueva Categoría</h1>
        @can('categorias.view')
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        @endcan
    </div>
@stop

@section('content')
<form method="POST" action="{{ route('categorias.store') }}" class="card card-outline card-success">
@csrf
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-7 mb-3">
                <label class="form-label"><i class="fas fa-tag"></i> Descripción <span class="text-danger">*</span></label>
                <input name="descripcion" class="form-control" value="{{ old('descripcion') }}"
                       placeholder="Ej.: Electrónica, Hogar, Ropa" required>
                @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label"><i class="fas fa-toggle-on"></i> Estado</label>
                <select name="estado" class="form-control">
                    <option value="Activo" @selected(old('estado','Activo')==='Activo')>Activo</option>
                    <option value="Pasivo" @selected(old('estado')==='Pasivo')>Pasivo</option>
                </select>
                @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer d-flex gap-2">
        @can('categorias.create')
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
        @endcan
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</form>
@stop
