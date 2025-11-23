@extends('adminlte::page')

@section('title', 'Editar Item')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Item</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Callout informativo -->
            <div class="callout callout-warning">
                <h5><i class="fas fa-info-circle"></i> Editando Item:</h5>
                <p class="mb-0">
                    <strong class="text-lg">{{ $item->codigo }}</strong> - {{ $item->descripcion }}
                    @if($item->fabricante)
                        <br><small class="text-muted"><i class="fas fa-industry"></i> {{ $item->fabricante }}</small>
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('items.partials.form', [
                    'item' => $item,
                    'categorias' => $categorias,
                    'medidas' => $medidas,
                    'mode' => 'edit',
                ])
            </form>

            <!-- Información adicional -->
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-database"></i> Información del Sistema</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID del Item:</dt>
                        <dd class="col-sm-9">{{ $item->id }}</dd>

                        <dt class="col-sm-3">Valor Inventario:</dt>
                        <dd class="col-sm-9">
                            <strong class="text-success">
                                ${{ number_format($item->cantidad * $item->costo_unitario, 2, '.', ',') }}
                            </strong>
                        </dd>

                        <dt class="col-sm-3">Fecha de Registro:</dt>
                        <dd class="col-sm-9">{{ $item->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Última Actualización:</dt>
                        <dd class="col-sm-9">{{ $item->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Actualizado hace:</dt>
                        <dd class="col-sm-9">{{ $item->updated_at?->diffForHumans() ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .callout {
        border-left-width: 5px;
    }
</style>
@stop
