@extends('adminlte::page')

@section('title', "Préstamo $prestamo->codigo")

@section('content_header')

    {{-- Auto-abrir PDF de devolución recién creada --}}
    @if (session('print_devolucion_id'))
        <script>
            (function() {
                const url = @json(route('devoluciones.imprimir.recibo', session('print_devolucion_id')));
                const w = window.open(url, '_blank', 'width=900,height=650');
                if (!w) {
                    alert('Tu navegador bloqueó la ventana de impresión. Habilita ventanas emergentes para este sitio.');
                }
            })();
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-contract text-primary"></i>
            Préstamo {{ $prestamo->codigo }}
            <span class="badge badge-{{
                $prestamo->estado === 'Activo' ? 'primary' :
                ($prestamo->estado === 'Observado' ? 'warning' : 'success')
            }} ml-2">
                <i class="fas fa-{{
                    $prestamo->estado === 'Activo' ? 'clock' :
                    ($prestamo->estado === 'Observado' ? 'exclamation-triangle' : 'check-circle')
                }}"></i>
                {{ $prestamo->estado }}
            </span>
        </h1>

        <div>
            {{-- Registrar Devolución --}}
            @if($prestamo->estado !== 'Completo')
                <a href="{{ route('devoluciones.create', $prestamo) }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-undo"></i> Registrar Devolución
                </a>
            @endif

            {{-- Editar solo si está Activo --}}
            @if($prestamo->estado === 'Activo')
                <a href="{{ route('prestamos.edit', $prestamo) }}" class="btn btn-warning shadow-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @else
                <button class="btn btn-warning shadow-sm" disabled title="Solo se puede editar préstamos Activos">
                    <i class="fas fa-lock"></i> Editar
                </button>
            @endif

            {{-- Imprimir Historial --}}
            <a href="{{ route('devoluciones.imprimir.historial', $prestamo) }}"
               target="_blank"
               class="btn btn-primary shadow-sm">
                <i class="fas fa-print"></i> Imprimir
            </a>

            <a href="{{ route('prestamos.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')

{{-- Información Principal --}}
<div class="row">
    <div class="col-md-8">
        {{-- Info General --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Información del Préstamo
                </h3>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Código:</dt>
                    <dd class="col-sm-9">
                        <strong class="text-lg">{{ $prestamo->codigo }}</strong>
                    </dd>

                    <dt class="col-sm-3">Fecha:</dt>
                    <dd class="col-sm-9">{{ $prestamo->fecha->format('d/m/Y h:i A') }}</dd>

                    <dt class="col-sm-3">Persona:</dt>
                    <dd class="col-sm-9">
                        <strong>{{ $prestamo->persona->nombre }}</strong>
                    </dd>

                    <dt class="col-sm-3">Proyecto:</dt>
                    <dd class="col-sm-9">
                        {{ $prestamo->proyecto->codigo ?? 'Uso General' }}
                        @if($prestamo->proyecto)
                            <br><small class="text-muted">{{ $prestamo->proyecto->descripcion }}</small>
                        @endif
                    </dd>

                    @if($prestamo->nota)
                    <dt class="col-sm-3">Nota:</dt>
                    <dd class="col-sm-9">
                        <div class="alert alert-info mb-0 py-2">
                            <i class="fas fa-sticky-note"></i> {{ $prestamo->nota }}
                        </div>
                    </dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Items Sueltos Prestados --}}
        @if($prestamo->detalles->count() > 0)
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-box"></i> Items Sueltos Prestados
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $prestamo->detalles->count() }} items</span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Prestado</th>
                            <th class="text-center">Devuelto</th>
                            <th class="text-center">Pendiente</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestamo->detalles as $detalle)
                            @php
                                $devuelto = DB::table('detalle_devoluciones')
                                    ->join('devoluciones', 'detalle_devoluciones.devolucion_id', '=', 'devoluciones.id')
                                    ->where('devoluciones.prestamo_id', $prestamo->id)
                                    ->where('devoluciones.estado', '!=', 'Anulada') // Filtro crítico
                                    ->where('detalle_devoluciones.item_id', $detalle->item_id)
                                    ->sum('detalle_devoluciones.cantidad');

                                $pendiente = $detalle->cantidad_prestada - $devuelto;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $detalle->item->codigo }}</strong><br>
                                    <small class="text-muted">{{ $detalle->item->descripcion }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $detalle->cantidad_prestada }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ $devuelto }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $pendiente > 0 ? 'warning' : 'secondary' }}">{{ $pendiente }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pendiente > 0)
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Completo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Kits Prestados --}}
        @if($prestamo->kits->count() > 0)
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i> Kits Prestados
                </h3>
            </div>
            <div class="card-body p-0">
                @foreach($prestamo->kits as $kit)
                    <div class="border-bottom">
                        <div class="p-3 bg-light">
                            <strong>Kit: {{ $kit->codigo }} — {{ $kit->nombre }}</strong>
                        </div>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Item del Kit</th>
                                    <th class="text-center">Prestado</th>
                                    <th class="text-center">Devuelto</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kit->items as $item)
                                    @php
                                        $devueltoK = DB::table('detalle_devoluciones_kit')
                                            ->join('devoluciones', 'detalle_devoluciones_kit.devolucion_id', '=', 'devoluciones.id')
                                            ->where('devoluciones.prestamo_id', $prestamo->id)
                                            ->where('devoluciones.estado', '!=', 'Anulada') // Filtro crítico
                                            ->where('detalle_devoluciones_kit.kit_id', $kit->id)
                                            ->where('detalle_devoluciones_kit.item_id', $item->id)
                                            ->sum('detalle_devoluciones_kit.cantidad');
                                        $pendK = $item->pivot->cantidad - $devueltoK;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->descripcion }}</td>
                                        <td class="text-center">{{ $item->pivot->cantidad }}</td>
                                        <td class="text-center text-success"><strong>{{ $devueltoK }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $pendK > 0 ? 'warning' : 'success' }}">
                                                {{ $pendK > 0 ? 'En uso' : 'Finalizado' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Historial de Devoluciones Mejorado con Anulación --}}
        @if($prestamo->devoluciones->count() > 0)
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i> Últimas Devoluciones
                </h3>
                <div class="card-tools">
                    <a href="{{ route('devoluciones.index', $prestamo) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-eye"></i> Ver Todas
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($prestamo->devoluciones->sortByDesc('created_at')->take(5) as $dev)
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>Devolución #{{ $dev->id }}</strong>
                                <span class="badge badge-{{
                                    $dev->estado === 'Anulada' ? 'danger' :
                                    ($dev->estado === 'Completa' ? 'success' : 'warning')
                                }}">
                                    {{ $dev->estado }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt"></i> {{ $dev->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            <div class="btn-group">
                                {{-- Botón Imprimir --}}
                                <a href="{{ route('devoluciones.imprimir.recibo', $dev->id) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary" title="Imprimir Recibo">
                                    <i class="fas fa-print"></i>
                                </a>

                                {{-- Botón ANULAR (Con validación de tiempo) --}}
                                @if($dev->estado !== 'Anulada' && $dev->created_at->diffInHours(now()) < 2)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmarAnulacion({{ $dev->id }})"
                                            title="Anular Devolución">
                                        <i class="fas fa-undo"></i> Anular
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="row text-sm">
                            <div class="col-md-6">
                                <span class="text-muted">Items sueltos:</span> <strong>{{ $dev->detalles->count() }}</strong>
                                <span class="mx-2">|</span>
                                <span class="text-muted">Items kits:</span> <strong>{{ $dev->detallesKit->count() }}</strong>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="text-muted">Registrado por:</span> {{ $dev->user->name }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-md-4">
        {{-- Resumen --}}
        <div class="card card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Resumen</h3>
            </div>
            <div class="card-body">
                <div class="info-box mb-3 bg-info">
                    <span class="info-box-icon"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Items Sueltos</span>
                        <span class="info-box-number">{{ $prestamo->detalles->count() }}</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Kits</span>
                        <span class="info-box-number">{{ $prestamo->kits->count() }}</span>
                    </div>
                </div>

                <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="fas fa-history"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Devoluciones</span>
                        <span class="info-box-number">{{ $prestamo->devoluciones->count() }}</span>
                    </div>
                </div>

                @php
                    $incidentesCount = \App\Models\Incidente::where('prestamo_id', $prestamo->id)->count();
                @endphp

                @if($incidentesCount > 0)
                <div class="info-box mb-0 bg-danger">
                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Incidentes</span>
                        <span class="info-box-number">{{ $incidentesCount }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Acciones Rápidas --}}
        @if($prestamo->estado !== 'Completo')
        <div class="card card-success shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('devoluciones.create', $prestamo) }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-undo"></i> Registrar Devolución
                </a>
                <a href="{{ route('devoluciones.index', $prestamo) }}" class="btn btn-info btn-block">
                    <i class="fas fa-history"></i> Ver Historial
                </a>
            </div>
        </div>
        @endif

        {{-- Info Adicional --}}
        <div class="card card-secondary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info"></i> Información</h3>
            </div>
            <div class="card-body text-sm">
                <p class="mb-2"><strong>Registrado por:</strong> {{ $prestamo->user->name ?? 'Sistema' }}</p>
                <p class="mb-0"><strong>Fecha registro:</strong> {{ $prestamo->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE SEGURIDAD PARA ANULACIÓN --}}
<div class="modal fade" id="modalAnular" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Anulación Crítica</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formAnular" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-bold">¿Está seguro de anular la Devolución #<span id="devIdText"></span>?</p>
                    <p class="text-sm text-muted">
                        Se restaurará el stock, se re-armarán los kits y se eliminarán consumos/incidentes vinculados.
                        <strong>Esta acción requiere su contraseña para autorizar.</strong>
                    </p>
                    <div class="form-group mt-3">
                        <label>Ingrese su contraseña de usuario:</label>
                        <input type="password" name="password" class="form-control" required placeholder="Contraseña de seguridad">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    function confirmarAnulacion(id) {
        // Genera la URL dinámicamente. Asegúrate que coincida con tu ruta web.php
        document.getElementById('formAnular').action = "{{ url('devoluciones') }}/" + id + "/anular";
        document.getElementById('devIdText').innerText = id;
        $('#modalAnular').modal('show');
    }
</script>
@stop
