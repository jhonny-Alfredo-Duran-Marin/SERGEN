@extends('adminlte::page')

@section('title', 'Editar Persona')

@section('content_header')
    <h1><i class="fas fa-user-edit"></i> Editar Persona</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('personas.update', $persona) }}">
                @csrf
                @method('PUT')

                <!-- Información Personal -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Información Personal
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> Editando:</h5>
                            <p class="mb-0">
                                <strong class="text-lg">{{ $persona->nombre }}</strong>
                                @if($persona->cargo)
                                    <br><small class="text-muted">{{ $persona->cargo }}</small>
                                @endif
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">
                                        <i class="fas fa-signature"></i> Nombre Completo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="nombre"
                                           id="nombre"
                                           class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $persona->nombre) }}"
                                           required
                                           autofocus>
                                    @error('nombre')
                                        <span class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cargo">
                                        <i class="fas fa-briefcase"></i> Cargo
                                    </label>
                                    <input type="text"
                                           name="cargo"
                                           id="cargo"
                                           class="form-control @error('cargo') is-invalid @enderror"
                                           value="{{ old('cargo', $persona->cargo) }}">
                                    @error('cargo')
                                        <span class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="celular">
                                        <i class="fas fa-phone"></i> Celular
                                    </label>
                                    <input type="text"
                                           name="celular"
                                           id="celular"
                                           class="form-control @error('celular') is-invalid @enderror"
                                           value="{{ old('celular', $persona->celular) }}">
                                    @error('celular')
                                        <span class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estado">
                                        <i class="fas fa-toggle-on"></i> Estado
                                    </label>
                                    <select name="estado"
                                            id="estado"
                                            class="form-control">
                                        <option value="1" @selected(old('estado', (int)$persona->estado) === 1)>
                                            Activo
                                        </option>
                                        <option value="0" @selected(old('estado', (int)$persona->estado) === 0)>
                                            Inactivo
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuario Vinculado -->
                @if($persona->user)
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-shield"></i> Usuario del Sistema
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-success">
                                    <i class="fas fa-link"></i> Vinculado
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user-circle"></i> Información del Usuario</h5>
                                    <dl class="row">
                                        <dt class="col-sm-4">Nombre:</dt>
                                        <dd class="col-sm-8">{{ $persona->user->name }}</dd>

                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">
                                            <a href="mailto:{{ $persona->user->email }}">
                                                {{ $persona->user->email }}
                                            </a>
                                        </dd>

                                        <dt class="col-sm-4">Estado:</dt>
                                        <dd class="col-sm-8">
                                            @if($persona->user->email_verified_at)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Verificado
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pendiente
                                                </span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-shield-alt"></i> Roles Asignados</h5>

                                    @if($roles->count() > 0)
                                        <div class="row">
                                            @foreach($roles as $r)
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                               class="custom-control-input"
                                                               name="roles[]"
                                                               value="{{ $r->id }}"
                                                               id="role-{{ $r->id }}"
                                                               {{ $persona->user->roles->contains('id', $r->id) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="role-{{ $r->id }}">
                                                            <strong>{{ $r->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-key"></i>
                                                                {{ $r->permissions_count ?? 0 }} permisos
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Nota:</strong> También puedes gestionar los roles desde el
                                            <a href="{{ route('users.roles.edit', $persona->user) }}">módulo de usuarios</a>.
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No hay roles disponibles. Puedes
                                            <a href="{{ route('roles.create') }}" target="_blank">crear roles aquí</a>.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-times"></i> Sin Usuario del Sistema
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-warning">
                                    <i class="fas fa-unlink"></i> No vinculado
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Esta persona no tiene usuario del sistema.</strong>
                                <p class="mb-0">
                                    Para crear un usuario vinculado a esta persona, puedes:
                                </p>
                                <ul class="mb-0">
                                    <li>Crear un usuario desde el <a href="{{ route('users.index') }}">módulo de usuarios</a></li>
                                    <li>O crear una nueva persona con usuario desde <a href="{{ route('personas.create') }}">aquí</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Persona
                            </button>
                            <a href="{{ route('personas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>

                        <button type="button"
                                class="btn btn-danger"
                                data-toggle="modal"
                                data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Eliminar Persona
                        </button>
                    </div>
                </div>
            </form>

            <!-- Información adicional -->
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información del Sistema</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $persona->id }}</dd>

                        <dt class="col-sm-3">Usuario ID:</dt>
                        <dd class="col-sm-9">
                            @if($persona->user_id)
                                {{ $persona->user_id }}
                            @else
                                <span class="text-muted">Sin usuario vinculado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Creado:</dt>
                        <dd class="col-sm-9">{{ $persona->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Última actualización:</dt>
                        <dd class="col-sm-9">{{ $persona->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar a <strong>{{ $persona->nombre }}</strong>?</p>

                    @if($persona->user)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Advertencia:</strong> Esta persona tiene un usuario del sistema vinculado
                            ({{ $persona->user->email }}). Al eliminar la persona, el usuario seguirá existiendo
                            pero quedará sin vinculación.
                        </div>
                    @endif

                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-circle"></i>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('personas.destroy', $persona) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
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
</style>
@stop
