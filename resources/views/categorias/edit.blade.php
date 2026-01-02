@extends('adminlte::page')

@section('title','Editar Categoría')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit"></i> Editar Categoría</h1>
        @can('categorias.view')
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        @endcan
    </div>
@stop

@section('content')
<form method="POST" action="{{ route('categorias.update',$categoria) }}" class="card card-outline card-warning">
@csrf @method('PUT')
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-7 mb-3">
                <label class="form-label"><i class="fas fa-tag"></i> Descripción <span class="text-danger">*</span></label>
                <input name="descripcion" class="form-control" required
                       value="{{ old('descripcion', $categoria->descripcion) }}">
                @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label"><i class="fas fa-toggle-on"></i> Estado</label>
                <select name="estado" class="form-control">
                    <option value="Activo" @selected(old('estado', $categoria->estado)==='Activo')>Activo</option>
                    <option value="Pasivo" @selected(old('estado', $categoria->estado)==='Pasivo')>Pasivo</option>
                </select>
                @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer d-flex gap-2">
        @can('categorias.update')
        <button class="btn btn-warning"><i class="fas fa-save"></i> Actualizar</button>
        @endcan
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</form>
@stop
