@extends('adminlte::page')

@section('title', 'Ubicaciones')

@section('content_header')
    <h1>Lista de Ubicaciones</h1>
@stop

@section('content')
    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon fas fa-ban"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            @can('ubicaciones.create')
                <a href="{{ route('ubicaciones.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Ubicación
                </a>
            @endcan
        </div>
        <div class="card-body">
            @if($ubicaciones->isEmpty())
                <div class="alert alert-info">
                    <i class="icon fas fa-info"></i> No hay ubicaciones registradas.
                </div>
            @else
                <table id="ubicacionesTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Descripción</th>
                            <th>Área</th>
                            <th>Sucursal</th>
                            <th style="width: 100px;">Estado</th>
                            @if (auth()->user()->can('ubicaciones.update') || auth()->user()->can('ubicaciones.delete'))
                                <th style="width: 150px;">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ubicaciones as $ubicacion)
                            <tr>
                                <td>{{ $ubicacion->id }}</td>
                                <td>{{ $ubicacion->descripcion }}</td>
                                <td>{{ $ubicacion->area->descripcion }}</td>
                                <td>{{ $ubicacion->area->sucursal->descripcion }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $ubicacion->estado == 'Activo' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $ubicacion->estado }}
                                    </span>
                                </td>
                                @if (auth()->user()->can('ubicaciones.update') || auth()->user()->can('ubicaciones.delete'))
                                    <td class="text-center">
                                        @can('ubicaciones.update')
                                            <a href="{{ route('ubicaciones.edit', $ubicacion) }}"
                                               class="btn btn-sm btn-info"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('ubicaciones.delete')
                                            <form action="{{ route('ubicaciones.destroy', $ubicacion) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar la ubicación: {{ $ubicacion->descripcion }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#ubicacionesTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[1, 'asc']]
            });
        });
    </script>
@stop
