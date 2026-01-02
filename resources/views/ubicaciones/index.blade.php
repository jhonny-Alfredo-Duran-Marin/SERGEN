@extends('adminlte::page')

@section('title', 'Ubicaciones')

@section('content_header')
    <h1>Lista de Ubicaciones</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            {{-- Solo muestra el botón si tiene permiso de crear --}}
            @can('areas.create')
                <a href="{{ route('ubicaciones.create') }}" class="btn btn-primary">Nueva Ubicación</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Estado</th>
                        @if (auth()->user()->can('areas.update') || auth()->user()->can('areas.delete'))
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ubicaciones as $ubicacion)
                        <tr>
                            <td>{{ $ubicacion->id }}</td>
                            <td>{{ $ubicacion->descripcion }}</td>
                            <td>{{ $ubicacion->area->descripcion }} - {{ $ubicacion->area->sucursal->descripcion }}</td>
                            <td>
                                <span class="badge {{ $ubicacion->estado == 'Activo' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $ubicacion->estado }}
                                </span>
                            </td>
                            @if (auth()->user()->can('areas.update') || auth()->user()->can('areas.delete'))
                                <td>
                                    @can('areas.update')
                                        <a href="{{ route('ubicaciones.edit', $ubicacion) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    @endcan

                                    @can('areas.delete')
                                        <form action="{{ route('ubicaciones.destroy', $ubicacion) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Está seguro de eliminar esta ubicación?')">
                                                <i class="fas fa-trash"></i> Borrar
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
