@extends('adminlte::page')

@section('title', 'Roles de Usuario')

@section('content_header')
    <h1><i class="fas fa-user-cog"></i> Gestionar Roles del Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Información del Usuario -->
            <div class="card card-widget widget-user">
                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{ $user->name ?? 'Usuario sin nombre' }}</h3>
                    <h5 class="widget-user-desc">{{ $user->email }}</h5>
                </div>
                <div class="widget-user-image">
                    <img class="img-circle elevation-2"
                         src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? $user->email) . '&background=007bff&color=fff' }}"
                         alt="Usuario">
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <div class="description-block">
                                <h5 class="description-header" id="rolesCount">{{ count($selected) }}</h5>
                                <span class="description-text">ROLES ASIGNADOS</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                <h5 class="description-header">{{ $roles->count() }}</h5>
                                <span class="description-text">ROLES DISPONIBLES</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Roles -->
            <form method="POST" action="{{ route('users.roles.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield"></i> Roles del Usuario
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" id="selectAll">
                                <i class="fas fa-check-double"></i> Todos
                            </button>
                            <button type="button" class="btn btn-tool btn-sm" id="deselectAll">
                                <i class="fas fa-times"></i> Ninguno
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($roles->count() > 0)
                            <div class="callout callout-info">
                                <h5><i class="fas fa-info-circle"></i> Sobre los roles:</h5>
                                <p class="mb-0">Los roles determinan los permisos y el nivel de acceso del usuario en el sistema. Un usuario puede tener múltiples roles.</p>
                            </div>

                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-6 mb-3">
                                        <div class="card role-card {{ in_array($role->id, $selected) ? 'card-primary' : 'card-default' }}" data-role-id="{{ $role->id }}">
                                            <div class="card-body p-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input role-checkbox"
                                                           type="checkbox"
                                                           name="roles[]"
                                                           value="{{ $role->id }}"
                                                           id="role-{{ $role->id }}"
                                                           {{ in_array($role->id, $selected) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="role-{{ $role->id }}">
                                                        <strong class="d-block">{{ $role->name }}</strong>
                                                        <small class="text-muted">
                                                            <i class="fas fa-key"></i>
                                                            {{ $role->permissions_count ?? 0 }} permisos
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Importante:</strong> Los cambios se aplicarán inmediatamente. Asegúrate de asignar los roles correctos según las responsabilidades del usuario.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay roles disponibles. Primero debes
                                <a href="{{ route('roles.create') }}">crear roles</a>.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary" {{ $roles->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Actualizar Roles
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Usuarios
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
        width: 100%;
    }
    .role-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .role-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .card-primary {
        border-color: #007bff;
    }
    .widget-user-image img {
        width: 90px;
        height: 90px;
    }
</style>
@stop

@section('js')
<script>
    $(function() {
        // Función para actualizar el contador
        function updateCounter() {
            const count = $('.role-checkbox:checked').length;
            $('#rolesCount').text(count);
        }

        // Función para actualizar el estilo de las cards
        function updateCardStyle(checkbox) {
            const card = $(checkbox).closest('.role-card');
            if ($(checkbox).is(':checked')) {
                card.removeClass('card-default').addClass('card-primary');
            } else {
                card.removeClass('card-primary').addClass('card-default');
            }
        }

        // Seleccionar todos los roles
        $('#selectAll').click(function() {
            $('.role-checkbox').prop('checked', true);
            $('.role-card').removeClass('card-default').addClass('card-primary');
            updateCounter();
        });

        // Deseleccionar todos los roles
        $('#deselectAll').click(function() {
            $('.role-checkbox').prop('checked', false);
            $('.role-card').removeClass('card-primary').addClass('card-default');
            updateCounter();
        });

        // Actualizar contador y estilo al cambiar checkboxes
        $('.role-checkbox').change(function() {
            updateCounter();
            updateCardStyle(this);
        });

        // Permitir hacer clic en toda la card para marcar/desmarcar
        $('.role-card').click(function(e) {
            if (!$(e.target).is('input[type="checkbox"]') && !$(e.target).is('label')) {
                const checkbox = $(this).find('.role-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });
    });
</script>
@stop
