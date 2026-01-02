{{-- resources/views/compras/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Gestión de Compras')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Gestión de Compras</h1>
        @can('compras.create')
            <a href="{{ route('compras.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Compra
            </a>
        @endcan
    </div>
@stop

@section('content')
    {{-- 1) Estadísticas --}}
    <div class="row">
        <div class="col-md-3">
            <x-adminlte-info-box title="Total Compras" text="{{ $total }}" icon="fas fa-shopping-cart" theme="info" />
        </div>
        <div class="col-md-3">
            <x-adminlte-info-box title="Pendientes" text="{{ $pendientes }}" icon="fas fa-exclamation-triangle"
                theme="{{ $pendientes > 0 ? 'warning' : 'secondary' }}" />
        </div>
        <div class="col-md-3">
            <x-adminlte-info-box title="Resueltas" text="{{ $resueltos }}" icon="fas fa-check-circle" theme="success" />
        </div>
        <div class="col-md-3">
            <x-adminlte-info-box title="Total Gastado" text="$ {{ number_format($totalGastado, 2) }}"
                icon="fas fa-dollar-sign" theme="danger" />
        </div>
    </div>

    {{-- 2) Filtros --}}
    <x-adminlte-card title="Filtros Avanzados" theme="primary" icon="fas fa-filter" collapsible>
        <form action="{{ route('compras.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-input name="q" label="Descripción" placeholder="Buscar por descripción..."
                        value="{{ request('q') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text text-primary"><i class="fas fa-search"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-3">
                    <x-adminlte-select name="tipo" label="Tipo de Compra">
                        <option value="">Todos</option>
                        @foreach (['Herramienta', 'Material', 'Insumos', 'Otros'] as $opt)
                            <option value="{{ $opt }}" @selected(request('tipo') === $opt)>{{ $opt }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-3">
                    <x-adminlte-select name="estado" label="Estado (Alerta)">
                        <option value="">Todos</option>
                        @foreach (['Pendiente', 'Resuelto'] as $opt)
                            <option value="{{ $opt }}" @selected(request('estado') === $opt)>
                                {{ $opt }}{{ $opt === 'Pendiente' ? ' (Alerta)' : '' }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="fecha" type="date" label="Fecha Específica"
                        value="{{ request('fecha') }}" />
                </div>
            </div>

            <div class="text-right">
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </x-adminlte-card>

    {{-- 3) Lista --}}
    <x-adminlte-card title="Lista de Compras" theme="lightblue" icon="fas fa-list">
        {{-- Flash de éxito --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-check"></i> {{ session('status') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Imagen</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Cant.</th>
                        <th>Costo Total</th>
                        <th>Estado (Alerta)</th>
                        <th>Registró</th>
                        <th style="width:110px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($compras as $compra)
                        <tr>
                            <td>{{ $compra->id }}</td>
                            <td>
                                @if ($compra->imagen)
                                    <img src="{{ asset('storage/' . $compra->imagen) }}"
                                        style="height:45px; border-radius:5px; object-fit:cover;">
                                @else
                                    —
                                @endif
                            </td>

                            <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                            <td>{{ $compra->descripcion }}</td>
                            <td>
                                @php
                                    $theme = match ($compra->tipo_compra) {
                                        'Herramienta' => 'warning',
                                        'Material' => 'dark',
                                        'Insumos' => 'info',
                                        'Otros' => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $theme }}">{{ $compra->tipo_compra }}</span>
                            </td>
                            <td>{{ $compra->cantidad }}</td>
                            <td><strong>${{ number_format($compra->costo_total, 2) }}</strong></td>
                            <td>
                                @if ($compra->estado_procesamiento === 'Pendiente')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Pendiente
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Resuelto
                                    </span>
                                @endif
                            </td>
                            <td>{{ $compra->user->name ?? '—' }}</td>
                            <td class="text-nowrap">
                                {{-- 1. Botón Ver (Fuera del modal para acceso directo) --}}
                                @can('compras.show')
                                    <a href="{{ route('compras.show', $compra) }}" class="btn btn-xs btn-info"
                                        title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                {{-- 2. Botón Editar --}}
                                @can('compras.update')
                                    <a href="{{ route('compras.edit', $compra) }}" class="btn btn-xs btn-warning"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                {{-- 3. Botón y Modal de Eliminar --}}
                                @can('compras.delete')
                                    @php $delId = 'delCompra'.$compra->id; @endphp
                                    <button class="btn btn-xs btn-danger" data-toggle="modal"
                                        data-target="#{{ $delId }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <x-adminlte-modal id="{{ $delId }}" title="Confirmar Eliminación" theme="danger"
                                        icon="fas fa-trash">
                                        <p>¿Estás seguro de eliminar esta compra?</p>
                                        <p><strong>Descripción:</strong> {{ $compra->descripcion }}</p>

                                        <form id="form-del-{{ $compra->id }}"
                                            action="{{ route('compras.destroy', $compra) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <x-slot name="footerSlot">
                                            <button type="submit" class="btn btn-danger"
                                                form="form-del-{{ $compra->id }}">
                                                Eliminar
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Cancelar
                                            </button>
                                        </x-slot>
                                    </x-adminlte-modal>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron compras con los filtros
                                seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $compras->links() }}
        </div>
    </x-adminlte-card>
@stop
