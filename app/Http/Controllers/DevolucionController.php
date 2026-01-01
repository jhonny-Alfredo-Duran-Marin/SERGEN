<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\{
    Prestamo,
    Devolucion,
    DetalleDevolucion,
    DetalleDevolucionKit,
    Consumo,
    Incidente,
    IncidenteItem,
    KitEmergencia,
    Item,
    Movimiento
};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:devoluciones.view'])->only(['index', 'imprimirRecibo', 'imprimirHistorial']);
        $this->middleware(['permission:devoluciones.create'])->only(['create', 'store']);
        $this->middleware(['permission:devoluciones.anular'])->only(['anular']);
    }

    public function index(Prestamo $prestamo)
    {
        $prestamo->load([
            'persona',
            'proyecto',
            'devoluciones' => fn($q) => $q->orderBy('created_at', 'desc'),
            'devoluciones.detalles.item',
            'devoluciones.detallesKit.item',
            'devoluciones.detallesKit.kit',
            'devoluciones.user',
            'kits.items'
        ]);

        return view('devoluciones.index', compact('prestamo'));
    }

    public function create(Prestamo $prestamo)
    {
        $prestamo->load(['persona', 'proyecto', 'detalles.item', 'kits.items']);

        $totalesSueltos = DB::table('detalle_devoluciones')
            ->join('devoluciones', 'detalle_devoluciones.devolucion_id', '=', 'devoluciones.id')
            ->where('devoluciones.prestamo_id', $prestamo->id)
            ->where('devoluciones.estado', '!=', 'Anulada')
            ->whereIn('detalle_devoluciones.estado', ['OK', 'Dañado', 'Perdido', 'Consumido'])
            ->groupBy('detalle_devoluciones.item_id')
            ->select('detalle_devoluciones.item_id', DB::raw('SUM(cantidad) as total'))
            ->pluck('total', 'item_id');

        $totalesKits = DB::table('detalle_devoluciones_kit')
            ->join('devoluciones', 'detalle_devoluciones_kit.devolucion_id', '=', 'devoluciones.id')
            ->where('devoluciones.prestamo_id', $prestamo->id)
            ->where('devoluciones.estado', '!=', 'Anulada')
            ->whereIn('detalle_devoluciones_kit.estado', ['OK', 'Dañado', 'Perdido', 'Consumido'])
            ->select('detalle_devoluciones_kit.kit_id', 'detalle_devoluciones_kit.item_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('detalle_devoluciones_kit.kit_id', 'detalle_devoluciones_kit.item_id')
            ->get()
            ->keyBy(fn($row) => "{$row->kit_id}-{$row->item_id}");

        $itemsSueltos = $prestamo->detalles->map(function ($detalle) use ($totalesSueltos) {
            $devuelto = $totalesSueltos[$detalle->item_id] ?? 0;
            $pendiente = $detalle->cantidad_prestada - $devuelto;
            return $pendiente > 0 ? [
                'item_id' => $detalle->item_id,
                'item' => $detalle->item,
                'prestado' => $detalle->cantidad_prestada,
                'devuelto' => $devuelto,
                'pendiente' => $pendiente,
            ] : null;
        })->filter()->values();

        $kitsConPendientes = $prestamo->kits->map(function ($kit) use ($totalesKits) {
            $itemsDelKit = $kit->items->map(function ($item) use ($kit, $totalesKits) {
                $key = "{$kit->id}-{$item->id}";
                $devuelto = $totalesKits[$key]->total ?? 0;
                $pendiente = $item->pivot->cantidad - $devuelto;
                return $pendiente > 0 ? [
                    'item_id' => $item->id,
                    'item' => $item,
                    'prestado' => $item->pivot->cantidad,
                    'devuelto' => $devuelto,
                    'pendiente' => $pendiente,
                ] : null;
            })->filter()->values();

            return $itemsDelKit->isNotEmpty() ? [
                'kit_id' => $kit->id,
                'kit' => $kit,
                'items' => $itemsDelKit,
            ] : null;
        })->filter()->values();

        return view('devoluciones.create', compact('prestamo', 'itemsSueltos', 'kitsConPendientes'));
    }

    public function store(Request $request, Prestamo $prestamo)
    {
        $data = $request->validate([
            'items' => 'nullable|array',
            'kits' => 'nullable|array'
        ]);

        $devolucionId = null;

        DB::transaction(function () use ($prestamo, $data, &$devolucionId) {
            $prestamo->load(['detalles.item', 'kits.items']);

            // Crear la cabecera de la devolución
            $devolucion = Devolucion::create([
                'prestamo_id' => $prestamo->id,
                'user_id' => auth()->id(),
                'estado' => 'Pendiente'
            ]);

            $incidente = null;
            $num = fn($v) => max(0, (int)($v ?? 0));

            // Función para asegurar la creación del incidente padre
            $getIncidente = function () use (&$incidente, $prestamo) {
                if (!$incidente) {
                    $incidente = Incidente::create([
                        'codigo' => Incidente::generarCodigo(),
                        'tipo' => 'PRESTAMO',
                        'estado' => 'ACTIVO',
                        'persona_id' => $prestamo->persona_id,
                        'fecha_incidente' => now(),
                        'prestamo_id' => $prestamo->id,
                    ]);
                }
                return $incidente;
            };

            // --- PROCESAR ÍTEMS SUELTOS ---
            if (isset($data['items'])) {
                foreach ($data['items'] as $itemId => $valores) {
                    $ok = $num($valores['ok'] ?? 0);
                    $dan = $num($valores['dañado'] ?? 0);
                    $per = $num($valores['perdido'] ?? 0);
                    $con = $num($valores['consumido'] ?? 0);

                    // IMPORTANTE: Cada estado debe registrar su propio detalle
                    if ($ok > 0) {
                        $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'OK', $ok, null);
                        Item::where('id', $itemId)->increment('cantidad', $ok);
                        DB::table('items')
                            ->where('id', $itemId)
                            ->update(['estado' => 'Disponible']);

                        $this->registrarMovimientos($itemId, $ok, 'Devolución OK', 'Ingreso', $prestamo->id);
                    }

                    if ($dan > 0) {
                        // Se registra en la tabla de detalles de devolución Y en incidentes
                        $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Dañado', $dan, null);
                        $this->registrarIncidente($getIncidente()->id, $itemId, 'DAÑADO', $dan);
                        $this->registrarMovimientos($itemId, $dan, 'Devolución Dañado', 'Egreso', $prestamo->id);
                        $this->darBajaItem($itemId, $prestamo->id);
                    }

                    if ($per > 0) {
                        $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Perdido', $per, null);
                        $this->registrarIncidente($getIncidente()->id, $itemId, 'PERDIDO', $per);
                        $this->registrarMovimientos($itemId, $per, 'Devolución Perdido', 'Egreso', $prestamo->id);
                        $this->darBajaItem($itemId, $prestamo->id);
                    }

                    if ($con > 0) {
                        $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Consumido', $con, null);
                        $this->registrarConsumo($itemId, $prestamo->id, $con);
                        $this->registrarMovimientos($itemId, $con, 'Devolución Consumido', 'Egreso', $prestamo->id);
                        $this->darBajaItem($itemId, $prestamo->id);
                    }
                }
            }

            // --- PROCESAR KITS (Lógica similar) ---
            if (isset($data['kits'])) {
                foreach ($data['kits'] as $kitId => $itemsKit) {
                    foreach ($itemsKit as $itemId => $valores) {
                        $okK = $num($valores['ok'] ?? 0);
                        $danK = $num($valores['dañado'] ?? 0);
                        $perK = $num($valores['perdido'] ?? 0);
                        $conK = $num($valores['consumido'] ?? 0);

                        if ($okK > 0) $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'OK', $okK, $kitId);
                        if ($danK > 0) {
                            $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Dañado', $danK, $kitId);
                            $this->registrarIncidente($getIncidente()->id, $itemId, 'DAÑADO', $danK);
                        }
                        if ($perK > 0) {
                            $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Perdido', $perK, $kitId);
                            $this->registrarIncidente($getIncidente()->id, $itemId, 'PERDIDO', $perK);
                        }
                        if ($conK > 0) {
                            $this->registrarDetalleDevolucion($devolucion->id, $itemId, 'Consumido', $conK, $kitId);
                            $this->registrarConsumo($itemId, $prestamo->id, $conK);
                        }
                        $this->darBajaItemKit($itemId, $prestamo->id, $kitId);
                    }
                }
            }

            // Actualización de estados finales
            $tienePendientes = $this->tienePendientes($prestamo->id);
            $prestamo->update([
                'estado' => $incidente ? 'Observado' : ($tienePendientes ? 'Activo' : 'Completo')
            ]);

            $devolucion->update(['estado' => $tienePendientes ? 'Parcial' : 'Completa']);
            $devolucionId = $devolucion->id;
        });

        return redirect()->route('prestamos.show', $prestamo)->with('print_devolucion_id', $devolucionId);
    }

    public function anular(Request $request, Devolucion $devolucion)
    {
        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, auth()->user()->password)) return back()->withErrors(['error' => 'Contraseña incorrecta.']);
        if ($devolucion->created_at->diffInHours(now()) >= 2) return back()->withErrors(['error' => 'Plazo expirado.']);

        try {
            DB::transaction(function () use ($devolucion) {
                $prestamo = $devolucion->prestamo;
                $estadoAntes = $prestamo->estado;

                foreach ($devolucion->detalles as $det) {
                    $item = Item::findOrFail($det->item_id);
                    if ($det->estado === 'OK' && $item->cantidad >= $det->cantidad) $item->decrement('cantidad', $det->cantidad);
                    $this->limpiarTablasDependientes($det, $prestamo->id);
                    $det->update(['estado' => 'Anulada']);
                    $this->actualizarEstadoItemAnulacion($item, false);
                }

                foreach ($devolucion->detallesKit as $dk) {
                    $this->limpiarTablasDependientes($dk, $prestamo->id);

                    if ($estadoAntes === 'Completo') {
                        if ($dk->estado !== 'OK' && $dk->estado !== 'Anulada') {
                            DB::table('kit_emergencia_item')->where('kit_emergencia_id', $dk->kit_id)
                                ->where('item_id', $dk->item_id)->update(['estado' => 'Activo']);
                        }
                    }

                    $dk->update(['estado' => 'Anulada']);

                    $tienePasivos = DB::table('kit_emergencia_item')->where('kit_emergencia_id', $dk->kit_id)
                        ->where('estado', 'Pasivo')->exists();

                    DB::table('kit_emergencias')->where('id', $dk->kit_id)->update([
                        'estado' => $tienePasivos ? 'Observado' : 'Pasivo'
                    ]);

                    $this->actualizarEstadoItemAnulacion(Item::find($dk->item_id), true);
                }

                $hasIncidentes = DB::table('detalle_devoluciones as dd')->join('devoluciones as d', 'dd.devolucion_id', '=', 'd.id')
                    ->where('d.prestamo_id', $prestamo->id)->where('d.estado', '!=', 'Anulada')->whereIn('dd.estado', ['Dañado', 'Perdido'])->exists() ||
                    DB::table('detalle_devoluciones_kit as ddk')->join('devoluciones as d', 'ddk.devolucion_id', '=', 'd.id')
                    ->where('d.prestamo_id', $prestamo->id)->where('d.estado', '!=', 'Anulada')->whereIn('ddk.estado', ['Dañado', 'Perdido'])->exists();

                $prestamo->update(['estado' => $hasIncidentes ? 'Observado' : 'Activo']);
                $devolucion->update(['estado' => 'Anulada']);
            });

            return redirect()->route('prestamos.show', $devolucion->prestamo_id)->with('status', 'Anulada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function limpiarTablasDependientes($det, $pId)
    {
        if ($det->estado === 'Consumido') Consumo::where('prestamo_id', $pId)->where('item_id', $det->item_id)->where('cantidad_consumida', $det->cantidad)->delete();
        if (in_array($det->estado, ['Dañado', 'Perdido'])) {
            $rows = DB::table('incidente_items')->join('incidentes', 'incidente_items.incidente_id', '=', 'incidentes.id')
                ->where('incidentes.prestamo_id', $pId)->where('incidente_items.item_id', $det->item_id)->where('incidente_items.cantidad', $det->cantidad)
                ->select('incidente_items.id', 'incidente_items.incidente_id')->get();
            foreach ($rows as $r) {
                DB::table('incidente_items')->where('id', $r->id)->delete();
                if (DB::table('incidente_items')->where('incidente_id', $r->incidente_id)->count() === 0) Incidente::where('id', $r->incidente_id)->delete();
            }
        }
    }

    public function tienePendientes($pId)
    {
        $s = DB::table('detalle_prestamos')->where('prestamo_id', $pId)->get()->sum(fn($d) => max(0, $d->cantidad_prestada - DB::table('detalle_devoluciones as dd')->join('devoluciones as d', 'dd.devolucion_id', '=', 'd.id')->where('d.prestamo_id', $pId)->where('dd.item_id', $d->item_id)->where('dd.estado', '!=', 'Anulada')->sum('dd.cantidad')));
        $k = DB::table('kit_prestamos as kp')->join('kit_emergencia_item as kei', 'kp.kit_id', '=', 'kei.kit_emergencia_id')->where('kp.prestamo_id', $pId)->get()->sum(fn($i) => max(0, $i->cantidad - DB::table('detalle_devoluciones_kit as ddk')->join('devoluciones as d', 'ddk.devolucion_id', '=', 'd.id')->where('d.prestamo_id', $pId)->where('ddk.kit_id', $i->kit_emergencia_id)->where('ddk.item_id', $i->item_id)->where('ddk.estado', '!=', 'Anulada')->sum('ddk.cantidad')));
        return ($s + $k) > 0;
    }

    private function actualizarEstadoItemAnulacion($item, $esKit = false)
    {
        if ($item->cantidad > 0) $item->update(['estado' => 'Disponible']);
        else $item->update(['estado' => $this->existeItemEnCualquierPrestamo($item->id, 0) ? ($esKit ? 'En_Kit' : 'Prestado') : 'Pasivo']);
    }

    public function existeItemEnCualquierPrestamo($itemId, $pId)
    {
        $s = DB::table('detalle_prestamos')->join('prestamos', 'detalle_prestamos.prestamo_id', '=', 'prestamos.id')->where('detalle_prestamos.item_id', $itemId)->where('prestamos.id', '!=', $pId)->whereIn('prestamos.estado', ['Activo', 'Observado'])->exists();
        $k = DB::table('kit_prestamos')->join('prestamos', 'kit_prestamos.prestamo_id', '=', 'prestamos.id')->join('kit_emergencias', 'kit_prestamos.kit_id', '=', 'kit_emergencias.id')->join('kit_emergencia_item', 'kit_emergencias.id', '=', 'kit_emergencia_item.kit_emergencia_id')->where('kit_emergencia_item.item_id', $itemId)->where('prestamos.id', '!=', $pId)->whereIn('prestamos.estado', ['Activo', 'Observado'])->where('kit_emergencias.estado', 'Activo')->exists();
        return $s || $k;
    }

    public function darBajaItem($itemId, $pId)
    {
        $item = Item::find($itemId);
        if (!$item || $item->estado === 'Pasivo') return;
        $noRec = DB::table('detalle_devoluciones')->join('devoluciones', 'detalle_devoluciones.devolucion_id', '=', 'devoluciones.id')->where('devoluciones.prestamo_id', $pId)->where('detalle_devoluciones.item_id', $itemId)->whereIn('detalle_devoluciones.estado', ['Dañado', 'Perdido', 'Consumido'])->sum('detalle_devoluciones.cantidad');
        $pre = DB::table('detalle_prestamos')->where('prestamo_id', $pId)->where('item_id', $itemId)->sum('cantidad_prestada');
        if ($noRec == $pre && $item->cantidad == 0 && !$this->existeItemEnCualquierPrestamo($itemId, $pId)) $item->update(['estado' => 'Pasivo']);
    }

    public function darBajaItemKit($itemId, $prestamoId, $kitId)
    {
        $kit = KitEmergencia::findOrFail($kitId);

        $pivot = DB::table('kit_emergencia_item')
            ->where('kit_emergencia_id', $kitId)
            ->where('item_id', $itemId)
            ->first();

        if (!$pivot) return;

        $totalesKit = DB::table('detalle_devoluciones_kit')
            ->join('devoluciones', 'detalle_devoluciones_kit.devolucion_id', '=', 'devoluciones.id')
            ->where('devoluciones.prestamo_id', $prestamoId)
            ->where('devoluciones.estado', '!=', 'Anulada')
            ->where('detalle_devoluciones_kit.kit_id', $kitId)
            ->where('detalle_devoluciones_kit.item_id', $itemId)
            ->selectRaw("
            SUM(CASE WHEN estado IN ('Dañado', 'Perdido', 'Consumido') THEN cantidad ELSE 0 END) as bajas,
            SUM(CASE WHEN estado = 'OK' THEN cantidad ELSE 0 END) as oks
        ")
            ->first();

        $cantidadPrestada = DB::table('detalle_prestamos')
            ->where('prestamo_id', $prestamoId)
            ->where('item_id', $itemId)
            ->sum('cantidad_prestada');

        $devueltoSuelto = DB::table('detalle_devoluciones')
            ->join('devoluciones', 'detalle_devoluciones.devolucion_id', '=', 'devoluciones.id')
            ->where('devoluciones.prestamo_id', $prestamoId)
            ->where('devoluciones.estado', '!=', 'Anulada')
            ->where('detalle_devoluciones.item_id', $itemId)
            ->sum('cantidad');

        $devueltoEnKits = DB::table('detalle_devoluciones_kit')
            ->join('devoluciones', 'detalle_devoluciones_kit.devolucion_id', '=', 'devoluciones.id')
            ->where('devoluciones.prestamo_id', $prestamoId)
            ->where('devoluciones.estado', '!=', 'Anulada')
            ->where('detalle_devoluciones_kit.item_id', $itemId)
            ->sum('cantidad');

        if (($devueltoSuelto + $devueltoEnKits) >= $cantidadPrestada) {
            $nuevoEstado = ($totalesKit->oks > 0 && $totalesKit->bajas < $pivot->cantidad) ? 'Activo' : 'Pasivo';

            $kit->items()->updateExistingPivot($itemId, [
                'cantidad' => $totalesKit->oks,
                'estado' => $nuevoEstado
            ]);
        }

        $hayProblemas = DB::table('kit_emergencia_item')
            ->where('kit_emergencia_id', $kitId)
            ->where(function ($q) {
                $q->where('estado', 'Pasivo')->orWhere('cantidad', 0);
            })->exists();

        $kit->update(['estado' => $hayProblemas ? 'Observado' : 'Activo']);
    }

    public function registrarMovimientos($itemId, $cant, $acc, $tipo, $pId)
    {
        Movimiento::create(['item_id' => $itemId, 'accion' => $acc, 'tipo' => $tipo, 'cantidad' => $cant ?? 0, 'fecha' => now(), 'user_id' => auth()->id(), 'prestamo_id' => $pId]);
    }

    private function registrarDetalleDevolucion($dId, $iId, $t, $c, $kId)
    {
        if ($kId) DetalleDevolucionKit::create(['devolucion_id' => $dId, 'kit_id' => $kId, 'item_id' => $iId, 'estado' => $t, 'cantidad' => $c]);
        else DetalleDevolucion::create(['devolucion_id' => $dId, 'item_id' => $iId, 'estado' => $t, 'cantidad' => $c]);
    }

    private function registrarIncidente($incId, $iId, $t, $c)
    {
        IncidenteItem::create(['incidente_id' => $incId, 'item_id' => $iId, 'estado_item' => $t, 'cantidad' => $c]);
    }

    private function registrarConsumo($itemId, $prestamoId, $cantidad)
    {
        $item = Item::findOrFail($itemId);
        $prestamo = Prestamo::findOrFail($prestamoId);
        return Consumo::create(['item_id' => $itemId, 'prestamo_id' => $prestamoId, 'cantidad_consumida' => $cantidad, 'precio_unitario' => $item->costo_unitario, 'proyecto_id' => $prestamo->proyecto_id, 'persona_id' => $prestamo->proyecto_id ? null : $prestamo->persona_id]);
    }

    /* ============================================================
   IMPRIMIR RECIBO DE UNA DEVOLUCIÓN ESPECÍFICA
   ============================================================ */
    public function imprimirRecibo(Devolucion $devolucion)
    {
        // CARGA ANSIOSA: Cargamos el préstamo y su persona asociada
        $devolucion->load([
            'prestamo.persona',
            'prestamo.proyecto',
            'detalles.item',
            'detallesKit.item',
            'user'
        ]);


        $logoPath = public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('devoluciones.recibo_devolucion', [
            'registro'   => $devolucion, // Se pasa como 'registro' para la vista
            'titulo'     => 'Recibo de Devolución #' . $devolucion->id,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4')->stream("recibo_devolucion_{$devolucion->id}.pdf");
    }

    /* ============================================================
       IMPRIMIR HISTORIAL COMPLETO DE DEVOLUCIONES DE UN PRÉSTAMO
       ============================================================ */
    public function imprimirHistorial(Prestamo $prestamo)
    {
        $prestamo->load([
            'persona',
            'proyecto',
            'devoluciones' => fn($q) => $q->where('estado', '!=', 'Anulada')->orderBy('fecha', 'desc'),
            'devoluciones.detalles.item',
            'devoluciones.detallesKit.item',
            'devoluciones.detallesKit.kit',
            'devoluciones.user'
        ]);

        $logoPath = public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('devoluciones.historial_devoluciones', [
            'prestamo' => $prestamo,
            'titulo' => 'Historial de Devoluciones - Préstamo ' . $prestamo->codigo,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4');

        // En el controlador para historial
        return Pdf::loadView('devoluciones.historial_devoluciones', [
            'prestamo'   => $prestamo, // La vista debe usar $prestamo->codigo, etc.
            'titulo'     => 'Historial de Devoluciones - Préstamo ' . $prestamo->codigo,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4')->stream("historial_devoluciones_{$prestamo->codigo}.pdf");
    }
}
