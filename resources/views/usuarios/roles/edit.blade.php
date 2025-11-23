@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Rol</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('roles.update', $role) }}">
                @csrf
                @method('PUT')

                <!-- Información del Rol -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tag"></i> Información del Rol
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> Rol Actual:</h5>
                            <p class="mb-0">Estás editando: <strong class="text-lg">{{ $role->name }}</strong></p>
                        </div>

                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-signature"></i> Nombre del Rol
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $role->name) }}"
                                   placeholder="Ej: Administrador, Editor, Supervisor"
                                   required
                                   autofocus>
                            <small class="form-text text-muted">
                                Usa un nombre descriptivo y en singular.
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
                            <span class="badge badge-success">
                                <span id="selectedCount">{{ count($selected) }}</span> seleccionados
                            </span>
                            <button type="button" class="btn btn-tool btn-sm" id="selectAll">
                                <i class="fas fa-check-double"></i> Todos
                            </button>
                            <button type="button" class="btn btn-tool btn-sm" id="deselectAll">
                                <i class="fas fa-times"></i> Ninguno
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($permissions->count() > 0)
                            <div class="row">
                                @php
                                    $groupedPermissions = $permissions->groupBy(function($perm) {
                                        return explode('.', $perm->name)[0] ?? 'otros';
                                    });
                                @endphp

                                @foreach($groupedPermissions as $group => $perms)
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-secondary">
                                            <div class="card-header py-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-folder"></i>
                                                    <strong>{{ ucfirst($group) }}</strong>
                                                </h6>
                                                <div class="card-tools">
                                                    @php
                                                        $groupSelected = $perms->whereIn('id', $selected)->count();
                                                    @endphp
                                                    <span class="badge badge-light">
                                                        {{ $groupSelected }}/{{ $perms->count() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                @foreach($perms as $perm)
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input permission-checkbox"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $perm->id }}"
                                                               id="perm-{{ $perm->id }}"
                                                               {{ in_array($perm->id, $selected) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="perm-{{ $perm->id }}">
                                                            <code class="text-sm">{{ $perm->name }}</code>
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
                                <strong>Advertencia:</strong> Los cambios en los permisos afectarán inmediatamente a todos los usuarios con este rol.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay permisos disponibles.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Rol
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>

                        @can('roles.delete')
                        <button type="button"
                                class="btn btn-danger"
                                data-toggle="modal"
                                data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Eliminar Rol
                        </button>
                        @endcan
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
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $role->id }}</dd>

                        <dt class="col-sm-4">Permisos asignados:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-info">{{ count($selected) }}</span>
                        </dd>

                        <dt class="col-sm-4">Creado:</dt>
                        <dd class="col-sm-8">{{ $role->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Última actualización:</dt>
                        <dd class="col-sm-8">{{ $role->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    @can('roles.delete')
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
                    <p>¿Estás seguro de que deseas eliminar el rol <strong>{{ $role->name }}</strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-circle"></i>
                        Esta acción no se puede deshacer y puede afectar a los usuarios que tienen este rol asignado.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
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
    @endcan
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
        // Función para actualizar el contador
        function updateCounter() {
            const count = $('.permission-checkbox:checked').length;
            $('#selectedCount').text(count);
        }

        // Seleccionar todos los permisos
        $('#selectAll').click(function() {
            $('.permission-checkbox').prop('checked', true);
            updateCounter();
        });

        // Deseleccionar todos los permisos
        $('#deselectAll').click(function() {
            $('.permission-checkbox').prop('checked', false);
            updateCounter();
        });

        // Actualizar contador al cambiar checkboxes
        $('.permission-checkbox').change(function() {
            updateCounter();
        });
    });
</script>
@stop
