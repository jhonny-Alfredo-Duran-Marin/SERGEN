@extends('adminlte::page')

@section('title', 'Gestión de Personas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-address-book"></i> Gestión de Personas</h1>
        <a href="{{ route('personas.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Persona
        </a>
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
                <h3>{{ $total }}</h3>
                <p>Total Personas</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activos }}</h3>
                <p>Activos</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $conUsuario }}</h3>
                <p>Con Usuario</p>
            </div>
            <div class="icon"><i class="fas fa-user-shield"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $sinUsuario }}</h3>
                <p>Sin Usuario</p>
            </div>
            <div class="icon"><i class="fas fa-user-times"></i></div>
        </div>
    </div>
</div>


    <!-- Filtros de búsqueda -->
    <div class="card card-primary card-outline collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('personas.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-search"></i> Buscar:</label>
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   value="{{ request('q') }}"
                                   placeholder="Nombre, celular o cargo">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-user-shield"></i> Usuario:</label>
                            <select name="filter" class="form-control">
                                <option value="">— Todos —</option>
                                <option value="con" @selected(request('filter')==='con')>Con usuario</option>
                                <option value="sin" @selected(request('filter')==='sin')>Sin usuario</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><i class="fas fa-toggle-on"></i> Estado:</label>
                            <select name="estado" class="form-control">
                                <option value="">— Todos —</option>
                                <option value="1" @selected(request('estado')==='1')>Activos</option>
                                <option value="0" @selected(request('estado')==='0')>Inactivos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('personas.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de personas -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Personas</h3>
            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $personas->total() }} {{ $personas->total() == 1 ? 'persona' : 'personas' }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($personas->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">#</th>
                            <th><i class="fas fa-user"></i> Nombre</th>
                            <th><i class="fas fa-briefcase"></i> Cargo</th>
                            <th><i class="fas fa-phone"></i> Celular</th>
                            <th class="text-center" width="100"><i class="fas fa-toggle-on"></i> Estado</th>
                            <th><i class="fas fa-user-circle"></i> Usuario</th>
                            <th class="text-center"><i class="fas fa-user-shield"></i> Roles</th>
                            <th class="text-center" width="200"><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personas as $p)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + ($personas->currentPage() - 1) * $personas->perPage() }}</td>
                            <td>
                                <strong>{{ $p->nombre }}</strong>
                            </td>
                            <td>
                                @if($p->cargo)
                                    <span class="text-primary">
                                        <i class="fas fa-briefcase"></i> {{ $p->cargo }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($p->celular)
                                    <a href="tel:{{ $p->celular }}" class="text-success">
                                        <i class="fas fa-phone"></i> {{ $p->celular }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $p->estado ? 'success' : 'secondary' }}">
                                    <i class="fas fa-{{ $p->estado ? 'check' : 'times' }}"></i>
                                    {{ $p->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                @if($p->user)
                                    <div>
                                        <i class="fas fa-user-circle text-info"></i>
                                        <strong>{{ $p->user->name }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope"></i> {{ $p->user->email }}
                                    </small>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-user-times"></i> Sin usuario
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->user && $p->user->roles?->count())
                                    @foreach($p->user->roles as $r)
                                        <span class="badge badge-primary"
                                              data-toggle="tooltip"
                                              title="Rol asignado">
                                            <i class="fas fa-shield-alt"></i> {{ $r->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button"
                                            class="btn btn-sm btn-info"
                                            data-toggle="modal"
                                            data-target="#detailModal{{ $p->id }}"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('personas.edit', $p) }}"
                                       class="btn btn-sm btn-warning"
                                       data-toggle="tooltip"
                                       title="Editar persona">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('personas.destroy', $p) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar a {{ $p->nombre }}?\n\nEsta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                data-toggle="tooltip"
                                                title="Eliminar persona">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal de detalles -->
                        <div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title">
                                            <i class="fas fa-user-circle"></i> Detalles de {{ $p->nombre }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5><i class="fas fa-user"></i> Información Personal</h5>
                                                <dl class="row">
                                                    <dt class="col-sm-4">Nombre:</dt>
                                                    <dd class="col-sm-8">{{ $p->nombre }}</dd>

                                                    <dt class="col-sm-4">Cargo:</dt>
                                                    <dd class="col-sm-8">{{ $p->cargo ?? 'No especificado' }}</dd>

                                                    <dt class="col-sm-4">Celular:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($p->celular)
                                                            <a href="tel:{{ $p->celular }}">{{ $p->celular }}</a>
                                                        @else
                                                            No especificado
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-4">Estado:</dt>
                                                    <dd class="col-sm-8">
                                                        <span class="badge badge-{{ $p->estado ? 'success' : 'secondary' }}">
                                                            {{ $p->estado ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <h5><i class="fas fa-user-shield"></i> Información de Usuario</h5>
                                                @if($p->user)
                                                    <dl class="row">
                                                        <dt class="col-sm-4">Usuario:</dt>
                                                        <dd class="col-sm-8">{{ $p->user->name }}</dd>

                                                        <dt class="col-sm-4">Email:</dt>
                                                        <dd class="col-sm-8">{{ $p->user->email }}</dd>

                                                        <dt class="col-sm-4">Roles:</dt>
                                                        <dd class="col-sm-8">
                                                            @forelse($p->user->roles as $r)
                                                                <span class="badge badge-primary">{{ $r->name }}</span>
                                                            @empty
                                                                <span class="text-muted">Sin roles</span>
                                                            @endforelse
                                                        </dd>

                                                        <dt class="col-sm-4">Verificado:</dt>
                                                        <dd class="col-sm-8">
                                                            @if($p->user->email_verified_at)
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check"></i> Sí
                                                                </span>
                                                            @else
                                                                <span class="badge badge-warning">
                                                                    <i class="fas fa-clock"></i> Pendiente
                                                                </span>
                                                            @endif
                                                        </dd>
                                                    </dl>
                                                @else
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Esta persona no tiene usuario del sistema asociado.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6 class="text-muted"><i class="fas fa-clock"></i> Registro</h6>
                                                <small>
                                                    <strong>Creado:</strong> {{ $p->created_at?->format('d/m/Y H:i') ?? 'N/A' }}<br>
                                                    <strong>Actualizado:</strong> {{ $p->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('personas.edit', $p) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
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
            @else
            <div class="text-center py-5">
                <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay personas registradas</p>
                <a href="{{ route('personas.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear primera persona
                </a>
            </div>
            @endif
        </div>
        @if($personas->hasPages())
        <div class="card-footer clearfix">
            {{ $personas->links() }}
        </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gestión de Personas</span>
                    <span class="info-box-number">Administra la información de personas y vincula usuarios del sistema para asignar roles y permisos</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .small-box > .inner {
        padding: 15px;
    }
    .small-box .icon {
        font-size: 70px;
        top: 10px;
        right: 15px;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@stop

@section('js')
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
