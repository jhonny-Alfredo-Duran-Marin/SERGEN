@extends('adminlte::page')

@section('title','Nueva Medida')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-plus-circle"></i> Crear Nueva Medida</h1>
        <a href="{{ route('medidas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
<form method="POST" action="{{ route('medidas.store') }}" class="card card-outline card-success">
@csrf
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-7 mb-3">
                <label class="form-label"><i class="fas fa-tag"></i> Descripción <span class="text-danger">*</span></label>
                <input name="descripcion" class="form-control" value="{{ old('descripcion') }}" required
                       placeholder="Ej.: Kilogramo, Metro, Unidad">
                @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label"><i class="fas fa-font"></i> Símbolo <span class="text-danger">*</span></label>
                <input name="simbolo" class="form-control" value="{{ old('simbolo') }}" required
                       placeholder="Ej.: kg, m, u" maxlength="10">
                @error('simbolo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer d-flex gap-2">
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
        <a href="{{ route('medidas.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</form>
@stop
