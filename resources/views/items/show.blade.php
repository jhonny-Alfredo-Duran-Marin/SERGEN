@extends('adminlte::page')

@section('title', 'Detalle del Item')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-box-open"></i> Item:
            <span class="text-primary">{{ $item->codigo }}</span>
        </h1>
        {{-- Botones de Acción principal, visibles siempre --}}
        <div class="btn-group">
            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- COLUMNA PRINCIPAL (8/12) --}}
        <div class="col-md-8">

            {{-- 1. Información General (Unificada) --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                    <div class="card-tools">
                        <button id="btn-copy-codigo" class="btn btn-tool" data-toggle="tooltip" title="Copiar código">
                            <i class="fas fa-copy"></i>
                        </button>
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" class="btn btn-tool" data-toggle="tooltip" title="Abrir URL del producto">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        {{-- Código y Fabricante --}}
                        <dt class="col-sm-4"><i class="fas fa-barcode text-primary"></i> Código:</dt>
                        <dd class="col-sm-8">
                            <strong class="text-lg" id="codigo-item">{{ $item->codigo }}</strong>
                            @if($item->fabricante)
                                <div class="small text-muted"><i class="fas fa-industry"></i> {{ $item->fabricante }}</div>
                            @endif
                        </dd>

                        {{-- Descripción --}}
                        <dt class="col-sm-4"><i class="fas fa-tag text-info"></i> Descripción:</dt>
                        <dd class="col-sm-8">{{ $item->descripcion }}</dd>

                        {{-- Categoría y Área (Uso de '??' para simplificar condicionales) --}}
                        <dt class="col-sm-4"><i class="fas fa-sitemap text-secondary"></i> Categoría:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-light border">{{ $item->categoria->descripcion ?? 'No especificada' }}</span>
                        </dd>

                        <dt class="col-sm-4"><i class="fas fa-layer-group text-success"></i> Área:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-light border">{{ $item->area->descripcion ?? 'No especificada' }}</span>
                        </dd>

                        {{-- Unidad de Medida --}}
                        <dt class="col-sm-4"><i class="fas fa-ruler text-info"></i> Unidad de Medida:</dt>
                        <dd class="col-sm-8">
                            {{ $item->medida->descripcion ?? 'No especificada' }}
                            @if($item->medida)
                                <span class="badge badge-info">{{ $item->medida->simbolo }}</span>
                            @endif
                        </dd>

                        {{-- Ubicación --}}
                        <dt class="col-sm-4"><i class="fas fa-map-marker-alt text-danger"></i> Ubicación:</dt>
                        <dd class="col-sm-8">{{ $item->ubicacion ?? 'No especificada' }}</dd>

                        {{-- Fecha de Registro --}}
                        @if($item->fecha_registro)
                            <dt class="col-sm-4"><i class="fas fa-calendar-alt text-muted"></i> Fecha Registro:</dt>
                            <dd class="col-sm-8">
                                {{ optional($item->fecha_registro)->format('d/m/Y') }}
                                <small class="text-muted">({{ optional($item->fecha_registro)->diffForHumans() }})</small>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- 2. Inventario y Costos (Tarjeta Dedicada) --}}
            @php
                $cantidad = $item->cantidad ?? 0;
                $costo_unitario = $item->costo_unitario ?? 0;
                $valor_total_inventario = $cantidad * $costo_unitario;
                $stock_bajo = $cantidad <= 3;
            @endphp
            <div class="card card-success card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-boxes"></i> Inventario y Costos</h3></div>
                <div class="card-body">
                    <div class="row">
                        {{-- Cantidad en Stock --}}
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-{{ $stock_bajo ? 'danger' : 'success' }}"><i class="fas fa-boxes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Cantidad en Stock</span>
                                    <span class="info-box-number">{{ $cantidad }}</span>
                                    @if($stock_bajo)
                                        <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Stock bajo</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- Piezas por Unidad --}}
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="fas fa-cubes"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Piezas por Unidad</span>
                                    <span class="info-box-number">{{ $item->piezas ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- Costo Unitario --}}
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Costo Unitario</span>
                                    <span class="info-box-number">${{ number_format($costo_unitario, 2, '.', ',') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Valor Total en Inventario (Bloque grande) --}}
                    <div class="alert alert-success text-center mt-3">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator"></i>
                            <strong>Valor Total en Inventario:</strong>
                            <span class="text-lg">
                                ${{ number_format($valor_total_inventario, 2, '.', ',') }}
                            </span>
                        </h5>
                        <small class="text-muted">
                            ({{ $cantidad }} × ${{ number_format($costo_unitario, 2, '.', ',') }})
                        </small>
                    </div>
                </div>
            </div>

        </div>
        {{-- FIN COLUMNA PRINCIPAL --}}

        {{-- COLUMNA LATERAL (4/12) --}}
        <div class="col-md-4">
            {{-- Imagen del Item (Simplificada y sin if innecesario) --}}
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-image"></i> Imagen del Item</h3>
                    <div class="card-tools">
                        @if(!empty($item->imagen_url) && !str_contains($item->imagen_url, 'placeholder'))
                            <a href="{{ $item->imagen_url }}" download class="btn btn-tool" data-toggle="tooltip" title="Descargar imagen">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center p-2">
                    <img src="{{ $item->imagen_url ?: $item->thumb_url }}"
                         alt="Imagen del item"
                         class="img-fluid rounded"
                         style="max-height: 300px; object-fit: cover; width: 100%; cursor: pointer;"
                         data-toggle="modal"
                         data-target="#imageModal"
                         loading="lazy"
                         decoding="async">
                </div>
            </div>

            {{-- Clasificación y Estado --}}
            <div class="card card-{{ $item->estado === 'Activo' ? 'success' : 'secondary' }} card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-tags"></i> Clasificación</h3></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="d-block text-muted">Tipo de Item:</label>
                        <span class="badge badge-{{ $item->tipo === 'Herramienta' ? 'warning' : 'dark' }} badge-lg">
                            <i class="fas fa-{{ $item->tipo === 'Herramienta' ? 'tools' : 'box' }}"></i>
                            {{ $item->tipo }}
                        </span>
                    </div>
                    <div>
                        <label class="d-block text-muted">Estado:</label>
                        <span class="badge badge-{{ $item->estado === 'Activo' ? 'success' : 'secondary' }} badge-lg">
                            <i class="fas fa-{{ $item->estado === 'Activo' ? 'check-circle' : 'times-circle' }}"></i>
                            {{ $item->estado }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Acciones (Simplificadas) --}}
            <div class="card card-warning">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-cog"></i> Acciones</h3></div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> Editar Item
                    </a>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Eliminar Item
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Volver a Lista
                    </a>
                </div>
            </div>
        </div>
        {{-- FIN COLUMNA LATERAL --}}
    </div>

    {{-- 3. Información del Sistema (Colapsable, como en el primero) --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-database"></i> Información del Sistema</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-2">ID del Item:</dt>
                        <dd class="col-sm-4">{{ $item->id }}</dd>

                        <dt class="col-sm-2">Fecha de Creación:</dt>
                        <dd class="col-sm-4">{{ optional($item->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Última Actualización:</dt>
                        <dd class="col-sm-4">{{ optional($item->updated_at)->format('d/m/Y H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-2">Actualizado hace:</dt>
                        <dd class="col-sm-4">{{ optional($item->updated_at)->diffForHumans() ?? 'N/A' }}</dd>

                        @if($item->deleted_at)
                            <dt class="col-sm-2">Eliminado:</dt>
                            <dd class="col-sm-4"><span class="text-danger">{{ $item->deleted_at->format('d/m/Y H:i') }}</span></dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Modales (No se modifican para mantener funcionalidad) --}}
    @include('items.partials.image_modal', ['item' => $item]) {{-- Asume que crearemos estos parciales --}}
    @include('items.partials.delete_modal', ['item' => $item])

@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5em 0.75em;
    }
    /* Simplificamos el uso de d-grid/gap-2 en Acciones */
    .d-grid {
        display: grid;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    .info-box {
        min-height: 90px;
    }
</style>
@stop

@section('js')
<script>
    (function() {
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        const btn = document.getElementById('btn-copy-codigo');
        const el = document.getElementById('codigo-item');

        btn?.addEventListener('click', async () => {
            try {
                // Usamos el mismo código eficiente de la segunda versión para el tooltip
                const originalTitle = btn.title;
                await navigator.clipboard.writeText(el?.textContent?.trim() || '');
                btn.title = '¡Copiado!';
                $(btn).tooltip('show');

                setTimeout(() => {
                    btn.title = originalTitle;
                    $(btn).tooltip('hide');
                }, 1000);
            } catch (e) {
                alert('No se pudo copiar el código');
            }
        });
    })();
</script>
@stop
