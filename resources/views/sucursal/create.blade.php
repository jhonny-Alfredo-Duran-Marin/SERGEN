@extends('adminlte::page')

@section('title', 'Nueva Sucursal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-plus-circle"></i> Crear Nueva Sucursal
        </h1>
        <a href="{{ route('sucursal.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <form method="POST" action="{{ route('sucursal.store') }}" class="card card-outline card-success">
        @csrf

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Información Básica
            </h3>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Descripción --}}
                <div class="col-md-7 mb-3">
                    <label class="form-label">
                        <i class="fas fa-tag"></i> Descripción
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        name="descripcion"
                        class="form-control"
                        value="{{ old('descripcion') }}"
                        placeholder="Ej.: La Villa, Los Lotes"
                        required
                    >
                    @error('descripcion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Estado --}}
                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        <i class="fas fa-toggle-on"></i> Estado
                    </label>
                    <select name="estado" class="form-control">
                        <option value="Activo" @selected(old('estado', 'Activo') === 'Activo')>Activo</option>
                        <option value="Pasivo" @selected(old('estado') === 'Pasivo')>Pasivo</option>
                    </select>
                    @error('estado')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <button class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>
            <a href="{{ route('sucursal.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
@stop
