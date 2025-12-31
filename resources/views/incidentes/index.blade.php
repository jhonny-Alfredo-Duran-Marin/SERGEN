@extends('adminlte::page')

@section('title', 'Gestión de Incidentes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-exclamation-triangle text-danger"></i> Gestión de Incidentes</h1>
    </div>
@stop

@section('content')
    {{-- Estadísticas rápidas --}}
    <div class="row">
        @php
            $stats = [
                ['label' => 'Total', 'val' => $incidentes->total(), 'bg' => 'info', 'icon' => 'list'],
                ['label' => 'Activos', 'val' => $incidentes->where('estado', 'ACTIVO')->count(), 'bg' => 'warning', 'icon' => 'exclamation-circle'],
                ['label' => 'En Proceso', 'val' => $incidentes->where('estado', 'EN_PROCESO')->count(), 'bg' => 'primary', 'icon' => 'sync'],
                ['label' => 'Completados', 'val' => $incidentes->where('estado', 'COMPLETADO')->count(), 'bg' => 'success', 'icon' => 'check-circle'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-{{ $stat['bg'] }} elevation-2">
                <div class="inner">
                    <h3>{{ $stat['val'] }}</h3>
                    <p>{{ $stat['label'] }}</p>
                </div>
                <div class="icon"><i class="fas fa-{{ $stat['icon'] }}"></i></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card card-outline card-danger shadow">
        <div class="card-header border-0">
            <h3 class="card-title text-bold">Lista de Incidentes</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-valign-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Código</th>
                            <th>Persona Responsable</th>
                            <th>Origen</th>
                            <th class="text-center">Estado</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentes as $i)
                        <tr>
                            <td class="text-bold text-danger">{{ $i->codigo }}</td>
                            <td>{{ $i->persona->nombre }}</td>
                            <td>
                                <span class="badge elevation-1 {{ $i->tipo === 'PRESTAMO' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $i->tipo }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $config = [
                                        'ACTIVO' => ['c' => 'warning', 'i' => 'clock'],
                                        'EN_PROCESO' => ['c' => 'info', 'i' => 'sync'],
                                        'COMPLETADO' => ['c' => 'success', 'i' => 'check']
                                    ][$i->estado];
                                @endphp
                                <span class="badge badge-{{ $config['c'] }} px-2 py-1 shadow-sm">
                                    <i class="fas fa-{{ $config['i'] }} mr-1"></i> {{ $i->estado }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($i->fecha_incidente)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    {{-- 1. Ver Incidente --}}
                                    <a href="{{ route('incidentes.show', $i) }}" class="btn btn-sm btn-default" title="Ver Detalle">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>

                                    {{-- 2. Devolución (Solo si NO está completado) --}}
                                    @if($i->estado !== 'COMPLETADO')
                                        <a href="{{ route('incidentes.devolver', $i) }}" class="btn btn-sm btn-default" title="Registrar Devolución">
                                            <i class="fas fa-undo text-success"></i>
                                        </a>

                                        {{-- 3. Completar Automáticamente --}}
                                        <form action="{{ route('incidentes.completar', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Marcar como COMPLETADO?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-default" title="Cierre Automático">
                                                <i class="fas fa-check-double text-primary"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- 4. Imprimir Historial --}}
                                    <a href="{{ route('incidentes.recibo', $i) }}" target="_blank" class="btn btn-sm btn-default" title="Imprimir Historial">
                                        <i class="fas fa-print text-dark"></i>
                                    </a>

                                    {{-- Botón Eliminar (Opcional) --}}
                                    <form action="{{ route('incidentes.destroy', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar incidente?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-default" title="Eliminar">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $incidentes->links() }}
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
