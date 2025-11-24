@extends('adminlte::page')

@section('title', 'Nueva Dotación')

@section('content_header')
    <h1><i class="fas fa-plus-circle"></i> Crear Nueva Dotación</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('dotaciones.store') }}">
                @csrf

                <!-- Datos Generales -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Datos Generales
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="persona_id">
                                        <i class="fas fa-user"></i> Persona
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="persona_id"
                                            id="persona_id"
                                            class="form-control @error('persona_id') is-invalid @enderror"
                                            required>
                                        <option value="">— Seleccionar persona —</option>
                                        @foreach($personas as $p)
                                            <option value="{{ $p->id }}" @selected(old('persona_id') == $p->id)>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha">
                                        <i class="fas fa-calendar"></i> Fecha de Dotación
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           name="fecha"
                                           id="fecha"
                                           class="form-control @error('fecha') is-invalid @enderror"
                                           value="{{ old('fecha', now()->format('Y-m-d')) }}"
                                           required>
                                    @error('fecha')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ítems -->
                @include('dotaciones.partials.form', ['items' => $items, 'dotacion' => null])

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Dotación
                        </button>
                        <a href="{{ route('dotaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
