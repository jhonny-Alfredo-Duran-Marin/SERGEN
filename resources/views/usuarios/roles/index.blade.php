@extends('adminlte::page')

@section('title', 'Gestión de Roles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-shield"></i> Gestión de Roles</h1>
        @can('roles.create')
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Rol
        </a>
        @endcan
    </div>
@stop

@section('content')
    @if(session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Roles</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $roles->total() }} roles</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($roles->count() > 0)
            <table class="table table-striped table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50">#</th>
                        <th><i class="fas fa-user-tag"></i> Nombre del Rol</th>
                        <th width="150" class="text-center"><i class="fas fa-key"></i> Permisos</th>
                        <th width="200" class="text-center"><i class="fas fa-cog"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td class="text-muted">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                        <td>
                            <strong class="text-primary">{{ $role->name }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info badge-pill">
                                {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                @can('roles.update')
                                <a class="btn btn-sm btn-warning"
                                   href="{{ route('roles.edit', $role) }}"
                                   data-toggle="tooltip"
                                   title="Editar rol">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('roles.delete')
                                <form action="{{ route('roles.destroy', $role) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar el rol {{ $role->name }}?\n\nEsta acción no se puede deshacer y puede afectar a los usuarios con este rol.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            data-toggle="tooltip"
                                            title="Eliminar rol">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay roles registrados</p>
                @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear primer rol
                </a>
                @endcan
            </div>
            @endif
        </div>
        @if($roles->hasPages())
        <div class="card-footer clearfix">
            {{ $roles->links() }}
        </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sobre los roles</span>
                    <span class="info-box-number">Los roles agrupan permisos y se asignan a usuarios para controlar el acceso al sistema</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
