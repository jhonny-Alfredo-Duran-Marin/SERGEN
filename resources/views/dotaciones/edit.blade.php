@extends('adminlte::page')
@section('title','Editar Dotación')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Dotación</h1>
@stop

@section('content')

<form method="POST" action="{{ route('dotaciones.update', $dotacion) }}">
@csrf @method('PUT')

<div class="card">
    <div class="card-header"><strong>Datos generales</strong></div>
    <div class="card-body">

        <div class="mb-3">
            <label class="form-label">Persona</label>
            <select class="form-control" name="persona_id" required>
                @foreach($personas as $p)
                    <option value="{{ $p->id }}" @selected($dotacion->persona_id == $p->id)>
                        {{ $p->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha"
                   value="{{ $dotacion->fecha->format('Y-m-d') }}">
        </div>

    </div>
</div>

@include('dotaciones.partials.form', [
    'items' => $items,
    'dotacion' => $dotacion
])

<button class="btn btn-warning mt-4">Actualizar Dotación</button>
</form>

@stop
