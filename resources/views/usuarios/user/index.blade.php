@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
        <div>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter"></i> Filtros
            </button>
        </div>
    </div>
@stop

@section('content')
    @if(session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $users->total() }}</h3>
                    <p>Total Usuarios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $users->where('email_verified_at', '!=', null)->count() }}</h3>
                    <p>Verificados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $users->filter(fn($u) => $u->roles->count() > 0)->count() }}</h3>
                    <p>Con Roles</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $users->filter(fn($u) => $u->roles->count() === 0)->count() }}</h3>
                    <p>Sin Roles</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Usuarios</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text"
                           id="searchInput"
                           class="form-control float-right"
                           placeholder="Buscar usuario...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">#</th>
                            <th width="80" class="text-center">Avatar</th>
                            <th><i class="fas fa-user"></i> Usuario</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th class="text-center"><i class="fas fa-user-shield"></i> Roles</th>
                            <th width="150" class="text-center"><i class="fas fa-check-circle"></i> Estado</th>
                            <th width="200" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                    @foreach($users as $u)
                        <tr class="user-row" data-name="{{ strtolower($u->name ?? $u->email) }}" data-email="{{ strtolower($u->email) }}">
                            <td class="text-muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td class="text-center">
                                <img src="{{ $u->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name ?? $u->email) . '&background=random' }}"
                                     alt="{{ $u->name ?? $u->email }}"
                                     class="img-circle elevation-2"
                                     style="width: 40px; height: 40px;"
                                     data-toggle="tooltip"
                                     title="{{ $u->name ?? $u->email }}">
                            </td>
                            <td>
                                <strong>{{ $u->name ?? 'Sin nombre' }}</strong>
                                @if($u->email_verified_at)
                                    <i class="fas fa-check-circle text-success ml-1"
                                       data-toggle="tooltip"
                                       title="Email verificado"></i>
                                @endif
                            </td>
                            <td>
                                <a href="mailto:{{ $u->email }}" class="text-muted">
                                    <i class="fas fa-envelope"></i> {{ $u->email }}
                                </a>
                            </td>
                            <td class="text-center">
                                @forelse($u->roles as $r)
                                    <span class="badge badge-primary mr-1"
                                          data-toggle="tooltip"
                                          title="Rol asignado">
                                        <i class="fas fa-shield-alt"></i> {{ $r->name }}
                                    </span>
                                @empty
                                    <span class="badge badge-secondary"
                                          data-toggle="tooltip"
                                          title="Usuario sin roles asignados">
                                        <i class="fas fa-user"></i> Sin rol
                                    </span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                @if($u->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Activo
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @can('users.assign.roles')
                                    <a class="btn btn-sm btn-info"
                                       href="{{ route('users.roles.edit', $u) }}"
                                       data-toggle="tooltip"
                                       title="Gestionar roles del usuario">
                                        <i class="fas fa-user-shield"></i> Roles
                                    </a>
                                    @endcan

                                    <button type="button"
                                            class="btn btn-sm btn-secondary"
                                            data-toggle="modal"
                                            data-target="#userDetailModal{{ $u->id }}"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal de detalles del usuario -->
                        <div class="modal fade" id="userDetailModal{{ $u->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title">
                                            <i class="fas fa-user-circle"></i> Detalles del Usuario
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-4 text-center">
                                                <img src="{{ $u->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name ?? $u->email) . '&background=random&size=200' }}"
                                                     alt="{{ $u->name ?? $u->email }}"
                                                     class="img-circle elevation-2"
                                                     style="width: 150px; height: 150px;">
                                                <h4 class="mt-3">{{ $u->name ?? 'Sin nombre' }}</h4>
                                                <p class="text-muted">{{ $u->email }}</p>
                                            </div>
                                            <div class="col-md-8">
                                                <h5><i class="fas fa-info-circle"></i> Información</h5>
                                                <dl class="row">
                                                    <dt class="col-sm-4">ID:</dt>
                                                    <dd class="col-sm-8">{{ $u->id }}</dd>

                                                    <dt class="col-sm-4">Nombre:</dt>
                                                    <dd class="col-sm-8">{{ $u->name ?? 'No especificado' }}</dd>

                                                    <dt class="col-sm-4">Email:</dt>
                                                    <dd class="col-sm-8">{{ $u->email }}</dd>

                                                    <dt class="col-sm-4">Estado:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($u->email_verified_at)
                                                            <span class="badge badge-success">Verificado</span>
                                                        @else
                                                            <span class="badge badge-warning">Pendiente de verificación</span>
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-4">Roles:</dt>
                                                    <dd class="col-sm-8">
                                                        @forelse($u->roles as $r)
                                                            <span class="badge badge-primary">{{ $r->name }}</span>
                                                        @empty
                                                            <span class="text-muted">Sin roles asignados</span>
                                                        @endforelse
                                                    </dd>

                                                    <dt class="col-sm-4">Registrado:</dt>
                                                    <dd class="col-sm-8">{{ $u->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                                                    <dt class="col-sm-4">Última actualización:</dt>
                                                    <dd class="col-sm-8">{{ $u->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        @can('users.assign.roles')
                                        <a href="{{ route('users.roles.edit', $u) }}" class="btn btn-info">
                                            <i class="fas fa-user-shield"></i> Gestionar Roles
                                        </a>
                                        @endcan
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div id="noResults" class="text-center py-4" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">No se encontraron usuarios con ese criterio de búsqueda</p>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay usuarios registrados</p>
            </div>
            @endif
        </div>
        @if($users->hasPages())
        <div class="card-footer clearfix">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal de filtros -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title">
                        <i class="fas fa-filter"></i> Filtrar Usuarios
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="form-group">
                            <label><i class="fas fa-user-shield"></i> Por Rol:</label>
                            <select class="form-control" id="roleFilter">
                                <option value="">Todos los roles</option>
                                <option value="sin-rol">Sin rol asignado</option>
                                @foreach($users->pluck('roles')->flatten()->unique('id') as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Por Estado:</label>
                            <select class="form-control" id="statusFilter">
                                <option value="">Todos los estados</option>
                                <option value="verificado">Verificados</option>
                                <option value="pendiente">Pendientes</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clearFilters">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">
                        <i class="fas fa-check"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Usuarios</span>
                    <span class="info-box-number">Asigna roles a los usuarios para controlar sus permisos y acceso al sistema</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .small-box {
        border-radius: 0.25rem;
    }
    .small-box > .inner {
        padding: 15px;
    }
    .small-box .icon {
        font-size: 70px;
        top: 10px;
        right: 15px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .user-row {
        transition: background-color 0.2s;
    }
    .user-row:hover {
        background-color: #f8f9fa;
    }
</style>
@stop

@section('js')
<script>
    $(function() {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Búsqueda en tiempo real
        $('#searchInput').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            let visibleRows = 0;

            $('.user-row').each(function() {
                const name = $(this).data('name');
                const email = $(this).data('email');

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    $(this).show();
                    visibleRows++;
                } else {
                    $(this).hide();
                }
            });

            // Mostrar mensaje si no hay resultados
            if (visibleRows === 0) {
                $('#noResults').show();
            } else {
                $('#noResults').hide();
            }
        });

        // Limpiar filtros
        $('#clearFilters').click(function() {
            $('#filterForm')[0].reset();
            $('.user-row').show();
            $('#noResults').hide();
        });

        // Aplicar filtros (esta es una implementación básica)
        $('#roleFilter, #statusFilter').change(function() {
            // Aquí puedes implementar la lógica de filtrado más compleja
            // Por ahora solo es la estructura visual
        });
    });
</script>
@stop
