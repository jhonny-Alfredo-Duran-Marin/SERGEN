@extends('adminlte::page')

@section('title', 'Nuevo Rol')

@section('content_header')
    <h1><i class="fas fa-user-shield"></i> Crear Nuevo Rol</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <!-- Información del Rol -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tag"></i> Información del Rol</h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> ¿Qué es un rol?</h5>
                            <p class="mb-0">Un rol es un conjunto de permisos agrupados que se asignan a usuarios para
                                controlar su acceso al sistema.</p>
                        </div>

                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-signature"></i> Nombre del Rol
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                placeholder="Ej: Administrador, Editor, Supervisor" required autofocus>
                            <small class="form-text text-muted">
                                Usa un nombre descriptivo y en singular. Ej: Administrador, no administradores.
                            </small>
                            @error('name')
                                <span class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Permisos -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-key"></i> Permisos del Rol
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" id="selectAll">
                                <i class="fas fa-check-double"></i> Seleccionar todos
                            </button>
                            <button type="button" class="btn btn-tool btn-sm" id="deselectAll">
                                <i class="fas fa-times"></i> Deseleccionar todos
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($permissions->count() > 0)
                            <div class="row">
                                @php
                                    $groupedPermissions = $permissions->groupBy(function ($perm) {
                                        return explode('.', $perm->name)[0] ?? 'otros';
                                    });
                                @endphp

                                @foreach ($groupedPermissions as $group => $perms)
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-secondary">
                                            <div class="card-header py-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-folder"></i>
                                                    <strong>{{ ucfirst($group) }}</strong>
                                                </h6>
                                                <div class="card-tools">
                                                    <span class="badge badge-light">{{ $perms->count() }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                @foreach ($perms as $perm)
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input permission-checkbox"
                                                            type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                            id="perm-{{ $perm->id }}">
                                                        <label class="custom-control-label" for="perm-{{ $perm->id }}">
                                                            {{ \App\Support\PermissionLabel::label($perm->name) }}

                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Nota:</strong> Selecciona cuidadosamente los permisos. Los usuarios con este rol
                                tendrán acceso a estas funcionalidades.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay permisos disponibles. Primero debes
                                <a href="{{ route('permissions.create') }}">crear permisos</a>.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Rol
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .callout {
            border-left-width: 5px;
        }

        .custom-control-label {
            cursor: pointer;
        }

        .card-secondary .card-header {
            background-color: #f8f9fa;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Seleccionar todos los permisos
            $('#selectAll').click(function() {
                $('.permission-checkbox').prop('checked', true);
            });

            // Deseleccionar todos los permisos
            $('#deselectAll').click(function() {
                $('.permission-checkbox').prop('checked', false);
            });
        });
    </script>
@stop
