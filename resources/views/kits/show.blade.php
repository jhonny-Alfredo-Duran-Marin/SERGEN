@extends('adminlte::page')
@section('title', 'Detalle Kit ' . $kit->codigo)

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1><i class="fas fa-first-aid text-primary"></i> Kit: {{ $kit->codigo }}</h1>
        <div>
            <a href="{{ route('kits.edit', $kit) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('kits.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Información General</h3></div>
            <div class="card-body">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item"><b>Nombre:</b> <span class="float-right">{{ $kit->nombre }}</span></li>
                    <li class="list-group-item">
                        <b>Estado Kit:</b>
                        <span class="float-right badge {{ $kit->estado == 'Activo' ? 'badge-success' : ($kit->estado == 'Observado' ? 'badge-danger' : 'badge-secondary') }}">
                            {{ $kit->estado }}
                        </span>
                    </li>
                    <li class="list-group-item"><b>Registrado:</b> <span class="float-right text-muted">{{ $kit->created_at->format('d/m/Y H:i') }}</span></li>
                </ul>
                <p class="mt-3 text-muted"><b>Descripción:</b><br>{{ $kit->descripcion ?? 'Sin descripción' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Componentes del Kit</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th class="text-center">Estado Ítem</th>
                            <th class="text-end">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kit->items as $it)
                        <tr>
                            <td><span class="badge border bg-light">{{ $it->codigo }}</span></td>
                            <td>{{ $it->descripcion }}</td>
                            <td class="text-center">
                                <span class="badge {{ $it->pivot->estado == 'Activo' ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $it->pivot->estado }}
                                </span>
                            </td>
                            <td class="text-end"><b>{{ (int)$it->pivot->cantidad }}</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
