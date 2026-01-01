<?php

namespace App\Http\Controllers;

use App\Models\{
    Prestamo,
    DetallePrestamo,
    PrestamoIncidente,
    Item,
    Persona,
    Proyecto,
    Movimiento,
    KitEmergencia,
    KitPrestamo
};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrestamoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:prestamos.view'])->only(['index', 'show', 'ImpresionHistorial']);
        $this->middleware(['permission:prestamos.create'])->only(['create', 'store']);
        $this->middleware(['permission:prestamos.update'])->only(['edit', 'update']);
        $this->middleware(['permission:prestamos.delete'])->only(['destroy']);
        $this->middleware(['permission:prestamos.imprimir'])->only(['ImpresionPrestamo']);
    }
    /* ============================================================
       INDEX
       ============================================================ */
    public function index(Request $request)
    {
        $stats = [
            'total'      => Prestamo::count(),
            'completos'  => Prestamo::where('estado', 'Completo')->count(),
            'activos'    => Prestamo::where('estado', 'Activo')->count(),
            'observados' => Prestamo::where('estado', 'Observado')->count(),
        ];

        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $query = Prestamo::with([
            'persona:id,nombre',
            'proyecto:id,codigo,descripcion',
            'detalles.item:id,descripcion',
            'kits:id,codigo,nombre'
        ]);


        if ($q !== '') $query->where('codigo', 'like', "%{$q}%");
        if (in_array($estado, ['Activo', 'Observado', 'Completo'])) {
            $query->where('estado', $estado);
        }

        $prestamos = $query->orderByDesc('id')->paginate(14)->withQueryString();

        return view('prestamos.index', compact('prestamos', 'stats'));
    }

    /* ============================================================
       CREATE
       ============================================================ */
    public function create()
    {
        $personas = Persona::where('estado', '1')->orderBy('nombre')->get(['id', 'nombre']);
        $proyectos = Proyecto::where('estado', 'Abierto')->orderBy('codigo')->get(['id', 'codigo', 'descripcion']);

        $itemsRaw = Item::where('estado', 'Disponible')
            ->orderBy('descripcion')
            ->get();

        $items = $itemsRaw->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'nombre' => $i->descripcion,
            'stock' => $i->cantidad,
            'costo' => (float) $i->costo_unitario
        ]);

        // Solo cargamos Kits que estén Disponibles
        $kitsData = KitEmergencia::with('items')
            ->where('estado', 'Activo')
            ->get()
            ->map(function ($kit) {
                return [
                    'id' => $kit->id,
                    'codigo' => $kit->codigo,
                    'nombre' => $kit->nombre,
                    'costo_total' => (float) $kit->items->sum(fn($item) => $item->pivot->cantidad * $item->costo_unitario),
                    'detalles' => $kit->items->map(fn($i) => [
                        'nombre' => $i->descripcion,
                        'cantidad' => $i->pivot->cantidad
                    ])
                ];
            });

        return view('prestamos.create', compact('personas', 'proyectos', 'items'))
            ->with('itemsJson', $items->toJson())
            ->with('kitsJson', $kitsData->toJson());
    }

    /* ============================================================
       STORE
       ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'codigo'               => 'required|string|max:50|unique:prestamos,codigo',
            'fecha'                => 'required|date',
            'persona_id'           => 'required|exists:personas,id',
            'proyecto_id'          => 'nullable|exists:proyectos,id',
            'nota'                 => 'nullable|string',
            'kits_seleccionados'   => 'nullable|array',
            'kits_seleccionados.*' => 'exists:kit_emergencias,id',
            'cant_item'            => 'array',
            'cant_item.*'          => 'integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Crear Cabecera del Préstamo
                $prestamo = Prestamo::create([
                    'codigo'      => $request->codigo,
                    'fecha'       => $request->fecha,
                    'persona_id'  => $request->persona_id,
                    'proyecto_id' => $request->proyecto_id,
                    'user_id'     => auth()->id(),
                    'nota'        => $request->nota,
                    'estado'      => 'Activo',
                ]);

                // 2. PROCESAR KITS (Tabla Pivote y Estado Pasivo)
                if ($request->has('kits_seleccionados')) {

                    foreach ($request->kits_seleccionados as $kitId) {
                        KitPrestamo::create([
                            'prestamo_id' => $prestamo->id,
                            'kit_id'      => $kitId,
                        ]);

                        // Pasar Kit a Pasivo
                        $kit = KitEmergencia::findOrFail($kitId);
                        $kit->update(['estado' => 'Pasivo']);
                    }
                }

                // 3. PROCESAR ÍTEMS SUELTOS (Detalles y Lógica de Stock)
                if ($request->has('cant_item')) {

                    foreach ($request->cant_item as $itemId => $cantidad) {
                        $cantidad = (int)$cantidad;
                        if ($cantidad <= 0) continue;

                        $item = Item::lockForUpdate()->findOrFail($itemId);

                        if ($item->cantidad < $cantidad) {
                            throw new \Exception("Stock insuficiente para: {$item->descripcion}");
                        }

                        // Descontar stock
                        $item->decrement('cantidad', $cantidad);

                        // Si el stock llega a 0, marcar como Prestado
                        if ($item->cantidad <= 0) {
                            $item->update(['estado' => 'Prestado']);
                        }

                        // Guardar en Detalles
                        DetallePrestamo::create([
                            'prestamo_id'       => $prestamo->id,
                            'item_id'           => $itemId,
                            'cantidad_prestada' => $cantidad,
                        ]);

                        // Registrar Movimiento
                        Movimiento::create([
                            'item_id'     => $itemId,
                            'tipo'        => 'Egreso',
                            'accion'      => 'Prestamo',
                            'cantidad'    => $cantidad,
                            'fecha'       => now(),
                            'user_id'     => auth()->id(),
                            'prestamo_id' => $prestamo->id,
                            'nota'        => 'Préstamo suelto ' . $prestamo->codigo,
                        ]);
                    }
                }
            });

            return redirect()->route('prestamos.index')->with('status', 'Préstamo registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Prestamo $prestamo)
    {
        if ($prestamo->devoluciones()->exists()) {
            return redirect()
                ->route('prestamos.index')
                ->withErrors(['error' => 'No se puede editar un préstamo que ya tiene devoluciones registradas.']);
        }

        $prestamo->load(['detalles.item', 'kits']);
        $personas = Persona::where('estado', '1')->orderBy('nombre')->get(['id', 'nombre']);
        $proyectos = Proyecto::where('estado', 'Abierto')->orderBy('codigo')->get(['id', 'codigo', 'descripcion']);

        // Incluimos los que están en este préstamo y los disponibles
        $items = Item::orderBy('descripcion')->get()->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'nombre' => $i->descripcion,
            'stock' => $i->cantidad,
            'costo' => (float) $i->costo_unitario
        ]);

        $kitsData = KitEmergencia::with('items')
            ->where('estado', 'Activo')
            ->orWhereIn('id', $prestamo->kits->pluck('id'))
            ->get()
            ->map(function ($kit) {
                return [
                    'id' => $kit->id,
                    'codigo' => $kit->codigo,
                    'nombre' => $kit->nombre,
                    'costo_total' => (float) $kit->items->sum(fn($item) => $item->pivot->cantidad * $item->costo_unitario),
                    'detalles' => $kit->items->map(fn($i) => [
                        'nombre' => $i->descripcion,
                        'cantidad' => $i->pivot->cantidad
                    ])
                ];
            });

        return view('prestamos.edit', compact('prestamo', 'personas',  'proyectos', 'items'))
            ->with('itemsJson', $items->toJson())
            ->with('kitsJson', $kitsData->toJson());
    }

    /* ============================================================
       UPDATE
       ============================================================ */
    public function update(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'persona_id' => 'required|exists:personas,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'nota'       => 'nullable|string',
            'kits_seleccionados' => 'nullable|array',
            'cant_item'  => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $prestamo) {

            /* =========================================
           1 SINCRONIZAR KITS (CLAVE)
           ========================================= */

            // IDs nuevos (lo que quedó en el formulario)
            $kitsNuevos = $request->input('kits_seleccionados', []);

            // IDs actuales en BD
            $kitsActuales = $prestamo->kits()->pluck('kit_emergencias.id')->toArray();

            // Kits quitados
            $kitsQuitados = array_diff($kitsActuales, $kitsNuevos);

            // Reactivar kits quitados
            if (!empty($kitsQuitados)) {
                KitEmergencia::whereIn('id', $kitsQuitados)
                    ->update(['estado' => 'Activo']);
            }

            // Pasar a pasivo los kits nuevos
            if (!empty($kitsNuevos)) {
                KitEmergencia::whereIn('id', $kitsNuevos)
                    ->update(['estado' => 'Pasivo']);
            }

            //  SINCRONIZACIÓN REAL
            $prestamo->kits()->sync($kitsNuevos);

            /* =========================================
           2️ REVERSAR ÍTEMS ANTERIORES
           ========================================= */
            foreach ($prestamo->detalles as $detalle) {
                $item = Item::find($detalle->item_id);
                if ($item) {
                    $item->increment('cantidad', $detalle->cantidad_prestada);
                    if ($item->cantidad > 0) {
                        $item->update(['estado' => 'Disponible']);
                    }
                }
                $detalle->delete();
            }

            /* =========================================
           3️ ACTUALIZAR CABECERA
           ========================================= */
            $prestamo->update(
                $request->only(['fecha', 'persona_id', 'proyecto_id', 'nota'])
            );

            /* =========================================
           4️ REGISTRAR ÍTEMS NUEVOS
           ========================================= */
            if ($request->filled('cant_item')) {
                foreach ($request->cant_item as $itemId => $cantidad) {
                    if ($cantidad <= 0) continue;

                    $item = Item::lockForUpdate()->findOrFail($itemId);

                    if ($item->cantidad < $cantidad) {
                        throw new \Exception("Stock insuficiente para {$item->descripcion}");
                    }

                    $item->decrement('cantidad', $cantidad);

                    if ($item->cantidad <= 0) {
                        $item->update(['estado' => 'Prestado']);
                    }

                    DetallePrestamo::create([
                        'prestamo_id'       => $prestamo->id,
                        'item_id'           => $itemId,
                        'cantidad_prestada' => $cantidad,
                        'costo_unitario'    => $item->costo_unitario,
                        'subtotal'          => $cantidad * $item->costo_unitario,
                    ]);
                }
            }
        });

        return redirect()
            ->route('prestamos.index')
            ->with('status', 'Préstamo actualizado correctamente');
    }


    public function show(Prestamo $prestamo)
    {
        $prestamo->load([
            'persona',
            'proyecto',
            'detalles.item',
            'devoluciones.detalles',
            'devoluciones.detalles.item',
            'kits.items'
        ]);

        return view('prestamos.show', compact('prestamo'));
    }

    public function ImpresionPrestamo(Prestamo $prestamo)
    {
        // 1. Cargar relaciones
        $prestamo->load([
            'persona',
            'proyecto',
            'detalles.item',
            'kits.items'
        ]);

        $totalGeneral = 0;

        // 2. Procesar Ítems Sueltos
        foreach ($prestamo->detalles as $detalle) {
            $costo = (float) ($detalle->item->costo_unitario ?? 0);
            $cantidad = (int) $detalle->cantidad_prestada;

            $detalle->costo_unitario_recibo = $costo;
            $detalle->subtotal_recibo = $cantidad * $costo;

            $totalGeneral += $detalle->subtotal_recibo;
        }

        // 3. Procesar Kits
        foreach ($prestamo->kits as $kit) {
            $totalKit = 0;
            foreach ($kit->items as $item) {
                $costoItem = (float) ($item->costo_unitario ?? 0);
                $cantItem = (int) $item->pivot->cantidad;

                $item->subtotal_recibo = $cantItem * $costoItem;
                $totalKit += $item->subtotal_recibo;
            }
            $kit->total_kit_recibo = $totalKit;
            $totalGeneral += $totalKit;
        }

        // 4. Armar objeto unificado
        $registro = (object) [
            'codigo'        => $prestamo->codigo,
            'fecha'         => $prestamo->fecha ?? $prestamo->created_at,
            'persona'       => $prestamo->persona,
            'proyecto'      => $prestamo->proyecto,
            'detalles'      => $prestamo->detalles,
            'kits'          => $prestamo->kits,
            'monto_total'   => $totalGeneral // Guardado dentro de registro
        ];

        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg')));

        return Pdf::loadView('recibo', [
            'registro'   => $registro,
            'titulo'     => 'RECIBO DE PRÉSTAMO',
            'tipo'       => 'prestamo',
            'logoBase64' => $logoBase64,
        ])->setPaper('a4')->stream("recibo_prestamo_{$prestamo->codigo}.pdf");
    }




    public function ImpresionHistorial(Prestamo $prestamo)
    {
        $prestamo->load([
            'persona',
            'proyecto',
            'detalles.item',
            'kits.items',
            'devoluciones.detalles.item'
        ]);

        $registro = (object) [
            'codigo'   => $prestamo->codigo,
            'fecha'    => $prestamo->fecha ?? $prestamo->created_at,
            'persona'  => $prestamo->persona,
            'proyecto' => $prestamo->proyecto,
            'detalles' => $prestamo->detalles,
            'kits'     => $prestamo->kits,
            'historial' => $prestamo->devoluciones,
        ];

        $logoPath = public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        return Pdf::loadView('recibo', [
            'registro' => $registro,
            'titulo'   => 'HISTORIAL DE PRÉSTAMO',
            'tipo'     => 'historial',
            'logoBase64' => $logoBase64,
        ])->setPaper('a4')
            ->stream("historial_prestamo_{$prestamo->codigo}.pdf");
    }
}
