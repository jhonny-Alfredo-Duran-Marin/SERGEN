@extends('adminlte::page')

@section('title', 'Nueva Persona')

@section('content_header')
    <h1><i class="fas fa-user-plus"></i> Crear Nueva Persona</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('personas.store') }}">
                @csrf

                <!-- Información Personal -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Información Personal
                        </h3>
                    </div>
                    <div class="card-body">
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
                                           value="{{ old('nombre') }}"
                                           placeholder="Ej: Juan Pérez García"
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
                                           value="{{ old('cargo') }}"
                                           placeholder="Ej: Gerente">
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
                                           value="{{ old('celular') }}"
                                           placeholder="Ej: 71234567">
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
                                        <option value="1" @selected(old('estado', '1') === '1')>
                                            Activo
                                        </option>
                                        <option value="0" @selected(old('estado') === '0')>
                                            Inactivo
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crear Usuario -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield"></i> Crear Usuario del Sistema
                        </h3>
                        <div class="card-tools">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="chk-create-user"
                                       name="create_user"
                                       value="1"
                                       {{ old('create_user') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="chk-create-user">
                                    Crear usuario
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> ¿Qué es esto?</h5>
                            <p class="mb-0">Si marcas esta opción, además de crear la persona, se creará un usuario del sistema que podrá iniciar sesión y tendrá acceso según los roles que le asignes.</p>
                        </div>

                        <div id="user-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_name">
                                            <i class="fas fa-user-circle"></i> Nombre de Usuario
                                        </label>
                                        <input type="text"
                                               name="user_name"
                                               id="user_name"
                                               class="form-control @error('user_name') is-invalid @enderror"
                                               value="{{ old('user_name') }}"
                                               placeholder="Opcional, puede ser diferente al nombre">
                                        <small class="form-text text-muted">
                                            Si lo dejas vacío, se usará el email como identificador.
                                        </small>
                                        @error('user_name')
                                            <span class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">
                                            <i class="fas fa-envelope"></i> Email
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="usuario@ejemplo.com">
                                        <small class="form-text text-muted">
                                            Se usará para iniciar sesión.
                                        </small>
                                        @error('email')
                                            <span class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">
                                            <i class="fas fa-key"></i> Contraseña
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   name="password"
                                                   id="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Mínimo 8 caracteres">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary"
                                                        type="button"
                                                        id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password_confirmation">
                                            <i class="fas fa-key"></i> Confirmar Contraseña
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               class="form-control"
                                               placeholder="Repite la contraseña">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Roles -->
                            <div class="form-group">
                                <label class="d-block">
                                    <i class="fas fa-shield-alt"></i> Roles del Usuario
                                    <small class="text-muted">(Opcional)</small>
                                </label>

                                @if($roles->count() > 0)
                                    <div class="row">
                                        @foreach($roles as $r)
                                            <div class="col-md-4 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           name="roles[]"
                                                           value="{{ $r->id }}"
                                                           id="role-{{ $r->id }}">
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
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No hay roles disponibles. Puedes
                                        <a href="{{ route('roles.create') }}" target="_blank">crear roles aquí</a>.
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Nota:</strong> Puedes asignar roles ahora o más tarde desde el módulo de usuarios.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Persona
                        </button>
                        <a href="{{ route('personas.index') }}" class="btn btn-secondary">
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
</style>
@stop

@section('js')
<script>
    $(function() {
        const chk = document.getElementById('chk-create-user');
        const box = document.getElementById('user-fields');

        function toggleUserBox() {
            if (chk.checked) {
                $(box).slideDown(300);
            } else {
                $(box).slideUp(300);
            }
        }

        chk?.addEventListener('change', toggleUserBox);
        toggleUserBox();

        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Auto-fill user_name from nombre if empty
        $('#nombre').on('blur', function() {
            if ($('#chk-create-user').is(':checked') && !$('#user_name').val()) {
                $('#user_name').val($(this).val());
            }
        });
    });
</script>
@stop
