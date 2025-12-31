@extends('adminlte::page')

@section('title', 'Detalle del Incidente')

@section('content_header')
    <h1>
        <i class="fas fa-exclamation-triangle"></i> Incidente:
        <span class="text-danger">{{ $incidente->codigo }}</span>
    </h1>
@stop

@section('content')
    @if (session('status'))
        <x-adminlte-alert theme="success" dismissible>
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </x-adminlte-alert>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                    <div class="card-tools">
                        @php
                            $estadoBadge = [
                                'ACTIVO' => ['class' => 'warning', 'icon' => 'exclamation-circle'],
                                'EN_PROCESO' => ['class' => 'info', 'icon' => 'sync'],
                                'COMPLETADO' => ['class' => 'success', 'icon' => 'check-circle'],
                            ];
                            $badge = $estadoBadge[$incidente->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                        @endphp
                        <span class="badge badge-{{ $badge['class'] }} badge-lg">
                            <i class="fas fa-{{ $badge['icon'] }}"></i> {{ $incidente->estado }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fas fa-barcode text-danger"></i> Código:</dt>
                        <dd class="col-sm-8"><strong class="text-lg">{{ $incidente->codigo }}</strong></dd>

                        <dt class="col-sm-4"><i class="fas fa-user text-primary"></i> Persona:</dt>
                        <dd class="col-sm-8"><strong>{{ $incidente->persona->nombre ?? 'N/A' }}</strong></dd>

                        <dt class="col-sm-4"><i class="fas fa-tag text-info"></i> Tipo Origen:</dt>
                        <dd class="col-sm-8"><span class="badge badge-primary">{{ $incidente->tipo }}</span></dd>

                        <dt class="col-sm-4"><i class="fas fa-calendar text-warning"></i> Fecha Incidente:</dt>
                        <dd class="col-sm-8">{{ $incidente->fecha_incidente }}</dd>

                        @if ($incidente->descripcion)
                            <dt class="col-sm-4"><i class="fas fa-file-alt text-secondary"></i> Descripción:</dt>
                            <dd class="col-sm-8">{{ $incidente->descripcion }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-box"></i> Ítems en el Incidente</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Afectado</th>
                                <th class="text-center text-success">Ya Devuelto</th>
                                <th class="text-center">Pendiente</th>
                                <th class="text-center">Estado Inicial</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incidente->items as $it)
                                @php
                                    // Cálculo inteligente
                                    // Sumamos solo las devoluciones que correspondan a este item_id
                                    $devueltoItem = $incidente->devoluciones
                                        ->where('item_id', $it->id)
                                        ->sum('cantidad_devuelta');

                                    // Si hay varios registros del mismo item_id en el mismo incidente,
                                    // Laravel los tratará como filas separadas gracias a la carga de la tabla pivote.
                                    $afectado = $it->pivot->cantidad ?? 0;

                                    // El pendiente se calcula individualmente por registro de la tabla pivote
                                    // pero restando lo devuelto globalmente para ese item en el incidente.
                                    // Si el sistema permite devoluciones parciales, el pendiente se actualiza así:
                                    $pendiente = max(0, $afectado - $devueltoItem);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $it->codigo }}</strong><br>
                                        <small>{{ $it->descripcion }}</small>
                                    </td>
                                    <td class="text-center"><strong>{{ $afectado }}</strong></td>
                                    <td class="text-center text-success">{{ $devueltoItem }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $pendiente > 0 ? 'danger' : 'success' }}">
                                            {{ $pendiente }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $it->pivot->estado_item ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Historial de Devoluciones del Incidente</h3>
                </div>
                <div class="card-body p-0">
                    @if ($incidente->devoluciones && $incidente->devoluciones->count() > 0)
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Item</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Resultado</th>
                                    <th class="text-center">PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incidente->items as $it)
                                    @php
                                        // Normalizamos el estado para que coincida con el ENUM de devoluciones
                                        $tipoFila =
                                            $it->pivot->estado_item === 'PERDIDO' ||
                                            $it->pivot->estado_item === 'FALTANTE'
                                                ? 'Perdido'
                                                : 'Dañado';

                                        // FILTRO CRUCIAL: Sumar solo devoluciones que correspondan a este item Y a este tipo
                                        $devueltoEspecifico = $incidente->devoluciones
                                            ->where('item_id', $it->id)
                                            ->where('tipo', $tipoFila)
                                            ->sum('cantidad_devuelta');

                                        $pendiente = max(0, $it->pivot->cantidad - $devueltoEspecifico);
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $it->codigo }}</strong><br><small>{{ $it->descripcion }}</small>
                                        </td>
                                        <td class="text-center">{{ $it->pivot->cantidad }}</td>
                                        <td class="text-center text-success font-weight-bold">{{ $devueltoEspecifico }}
                                        </td>
                                        <td
                                            class="text-center font-weight-bold text-{{ $pendiente > 0 ? 'danger' : 'success' }}">
                                            {{ $pendiente }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary">{{ $it->pivot->estado_item }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-3 text-muted">No se han registrado devoluciones para este incidente.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog"></i> Acciones</h3>
                </div>
                <div class="card-body">
                    @if ($incidente->estado !== 'COMPLETADO')
                        <a href="{{ route('incidentes.devolver', $incidente) }}" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-undo"></i> Registrar Devolución
                        </a>

                        <form action="{{ route('incidentes.completar', $incidente) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block"
                                onclick="return confirm('¿Finalizar incidente sin más devoluciones?')">
                                <i class="fas fa-check-double"></i> Cierre Forzado
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('incidentes.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Volver a Lista
                    </a>
                </div>
            </div>

            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Balance General</h3>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Total Dañado/Perdido:</td>
                            <td class="text-right"><strong>{{ $incidente->items->sum('pivot.cantidad') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Total Recuperado:</td>
                            <td class="text-right text-success">
                                <strong>{{ $incidente->devoluciones->sum('cantidad_devuelta') }}</strong>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td>Saldo Pendiente:</td>
                            <td class="text-right text-danger">
                                <strong>{{ max(0, $incidente->items->sum('pivot.cantidad') - $incidente->devoluciones->sum('cantidad_devuelta')) }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
