@extends('adminlte::page')

@section('title','Editar Medida')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit"></i> Editar Medida</h1>
        <a href="{{ route('medidas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
<form method="POST" action="{{ route('medidas.update', $medida) }}" class="card card-outline card-warning">
@csrf @method('PUT')
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-7 mb-3">
                <label class="form-label"><i class="fas fa-tag"></i> Descripción <span class="text-danger">*</span></label>
                <input name="descripcion" class="form-control" required
                       value="{{ old('descripcion', $medida->descripcion) }}">
                @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label"><i class="fas fa-font"></i> Símbolo <span class="text-danger">*</span></label>
                <input name="simbolo" class="form-control" required maxlength="10"
                       value="{{ old('simbolo', $medida->simbolo) }}">
                @error('simbolo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="card-footer d-flex gap-2">
        <button class="btn btn-warning"><i class="fas fa-save"></i> Actualizar</button>
        <a href="{{ route('medidas.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</form>
@stop
