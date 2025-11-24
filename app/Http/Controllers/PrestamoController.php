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
    KitEmergencia
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrestamoController extends Controller
{
    /* ============================================================
       INDEX
       ============================================================ */
    public function index(Request $request)
    {
        $stats = [
            'total'       => Prestamo::count(),
            'completos'   => Prestamo::where('estado', 'Completo')->count(),
            'activos'     => Prestamo::where('estado', 'Activo')->count(),
            'observados'  => Prestamo::where('estado', 'Observado')->count(),
        ];

        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $query = Prestamo::with([
            'persona:id,nombre',
            'proyecto:id,codigo,descripcion',
            'detalles.item:id,descripcion'
        ]);

        if ($q !== '') $query->where('codigo', 'like', "%{$q}%");
        if (in_array($estado, ['Activo','Observado','Completo'])) {
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
        $personas = Persona::orderBy('nombre')->get(['id','nombre']);
        $proyectos = Proyecto::orderBy('codigo')->get(['id','codigo','descripcion']);
        $items = Item::orderBy('descripcion')->get(['id','descripcion','codigo','cantidad','costo_unitario']);

        return view('prestamos.create', compact('personas','proyectos','items'));
    }

    /* ============================================================
       STORE
       ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'codigo'            => 'required|string|max:50|unique:prestamos,codigo',
            'fecha'             => 'required|date',
            'persona_id'        => 'required|exists:personas,id',
            'proyecto_id'       => 'nullable|exists:proyectos,id',
            'nota'              => 'nullable|string',
            'kit_emergencia_id' => 'nullable|exists:kit_emergencias,id',
            'cant_item'         => 'array',
            'cant_item.*'       => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            /* =====================================
               CREAR PRESTAMO
               ===================================== */
            $prestamo = Prestamo::create([
                'codigo'            => $request->codigo,
                'fecha'             => $request->fecha,
                'persona_id'        => $request->persona_id,
                'proyecto_id'       => $request->proyecto_id,
                'user_id'           => auth()->id(),
                'kit_emergencia_id' => $request->kit_emergencia_id, // SOLO ID DEL KIT
                'nota'              => $request->nota,
                'estado'            => 'Activo',
            ]);

            /* =====================================
               1) DESCONTAR STOCK DEL KIT (si aplica)
               ===================================== */
            if ($request->filled('kit_emergencia_id')) {

                $kit = KitEmergencia::with('items')->findOrFail($request->kit_emergencia_id);

                foreach ($kit->items as $kitItem) {

                    $cant = $kitItem->pivot->cantidad;
                    $item = Item::lockForUpdate()->find($kitItem->id);

                    if ($item->cantidad < $cant) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'kit_emergencia_id' => "Sin stock: {$item->descripcion}"
                        ]);
                    }

                    // descontar stock
                    $item->decrement('cantidad', $cant);
                    $item->update(['estado' => 'Prestado']);

                    // movimiento
                    Movimiento::create([
                        'item_id'     => $item->id,
                        'tipo'        => 'Egreso',
                        'accion'      => 'Prestamo (KIT)',
                        'cantidad'    => $cant,
                        'fecha'       => now(),
                        'user_id'     => auth()->id(),
                        'prestamo_id' => $prestamo->id,
                        'nota'        => "Préstamo KIT {$kit->codigo}",
                    ]);
                }
            }

            /* =====================================
               2) GUARDAR SOLO ITEMS SUELTOS
               ===================================== */
            if ($request->has('cant_item')) {

                foreach ($request->cant_item as $itemId => $cantidad) {

                    $cantidad = (int)$cantidad;
                    if ($cantidad <= 0) continue;

                    $item = Item::lockForUpdate()->findOrFail($itemId);

                    if ($item->cantidad < $cantidad) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "cant_item.$itemId" => "Sin stock: {$item->descripcion}"
                        ]);
                    }

                    // descontar stock
                    $item->decrement('cantidad', $cantidad);
                    $item->update(['estado' => 'Prestado']);

                    // guardar detalle (SOLO SUELTO)
                    DetallePrestamo::create([
                        'prestamo_id'       => $prestamo->id,
                        'item_id'           => $itemId,
                        'cantidad_prestada' => $cantidad,
                        'costo_unitario'    => $item->costo_unitario,
                        'subtotal'          => $item->costo_unitario * $cantidad,
                    ]);

                    // movimiento
                    Movimiento::create([
                        'item_id'     => $itemId,
                        'tipo'        => 'Egreso',
                        'accion'      => 'Prestamo',
                        'cantidad'    => $cantidad,
                        'fecha'       => now(),
                        'user_id'     => auth()->id(),
                        'prestamo_id' => $prestamo->id,
                        'nota'        => 'Préstamo ' . $prestamo->codigo,
                    ]);
                }
            }
        });

        return redirect()
            ->route('prestamos.index')
            ->with('status', 'Préstamo creado correctamente.');
    }

    /* ============================================================
       SHOW
       ============================================================ */
    public function show(Prestamo $prestamo)
    {
        $prestamo->load([
            'persona',
            'proyecto',
            'detalles.item',
            'devoluciones.detalles',
            'devoluciones.detalles.item',
            'kit.items',
        ]);

        return view('prestamos.show', compact('prestamo'));
    }

    /* ============================================================
       EDIT
       ============================================================ */
    public function edit(Prestamo $prestamo)
    {
        $prestamo->load('detalles.item');

        $personas = Persona::orderBy('nombre')->get(['id','nombre']);
        $proyectos = Proyecto::orderBy('codigo')->get(['id','codigo','descripcion']);
        $items = Item::orderBy('descripcion')->get(['id','descripcion','codigo','cantidad','costo_unitario']);

        return view('prestamos.edit', compact('prestamo','personas','proyectos','items'));
    }

    /* ============================================================
       UPDATE
       ============================================================ */
    public function update(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'fecha'             => 'required|date',
            'persona_id'        => 'required|exists:personas,id',
            'proyecto_id'       => 'nullable|exists:proyectos,id',
            'nota'              => 'nullable|string',
            'kit_emergencia_id' => 'nullable|exists:kit_emergencias,id',
            'cant_item'         => 'array',
            'cant_item.*'       => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request, $prestamo) {

            /* =====================================
               ACTUALIZAR CABECERA
               ===================================== */
            $prestamo->update([
                'fecha'             => $request->fecha,
                'persona_id'        => $request->persona_id,
                'proyecto_id'       => $request->proyecto_id,
                'nota'              => $request->nota,
                'kit_emergencia_id' => $request->kit_emergencia_id ?? null,
            ]);

            /* =====================================
               DEVOLVER STOCK DE ITEMS SUELTOS ANTERIORES
               ===================================== */
            foreach ($prestamo->detalles as $detalle) {

                $item = $detalle->item;

                $pendiente = $detalle->cantidad_prestada - $detalle->cantidad_devuelta;
                $item->increment('cantidad', $pendiente);

                if ($item->cantidad > 0 && $item->estado === 'Prestado') {
                    $item->update(['estado' => 'Activo']);
                }

                $detalle->delete();
            }

            /* =====================================
               DESCONTAR STOCK DEL NUEVO KIT (si hay)
               ===================================== */
            if ($request->filled('kit_emergencia_id')) {

                $kit = KitEmergencia::with('items')->findOrFail($request->kit_emergencia_id);

                foreach ($kit->items as $kitItem) {

                    $cant = $kitItem->pivot->cantidad;
                    $item = Item::lockForUpdate()->find($kitItem->id);

                    if ($item->cantidad < $cant) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'kit_emergencia_id' => "Sin stock: {$item->descripcion}"
                        ]);
                    }

                    // nuevo descuento
                    $item->decrement('cantidad', $cant);
                    $item->update(['estado' => 'Prestado']);

                    Movimiento::create([
                        'item_id'     => $item->id,
                        'tipo'        => 'Egreso',
                        'accion'      => 'Prestamo (KIT)',
                        'cantidad'    => $cant,
                        'fecha'       => now(),
                        'user_id'     => auth()->id(),
                        'prestamo_id' => $prestamo->id,
                        'nota'        => "Actualización KIT {$kit->codigo}",
                    ]);
                }
            }

            /* =====================================
               GUARDAR NUEVOS ITEMS SUELTOS
               ===================================== */
            if ($request->has('cant_item')) {

                foreach ($request->cant_item as $itemId => $cantidad) {

                    $cantidad = (int)$cantidad;
                    if ($cantidad <= 0) continue;

                    $item = Item::lockForUpdate()->find($itemId);

                    if ($item->cantidad < $cantidad) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "cant_item.$itemId" => "Sin stock: {$item->descripcion}"
                        ]);
                    }

                    $item->decrement('cantidad', $cantidad);
                    $item->update(['estado' => 'Prestado']);

                    DetallePrestamo::create([
                        'prestamo_id'       => $prestamo->id,
                        'item_id'           => $itemId,
                        'cantidad_prestada' => $cantidad,
                        'costo_unitario'    => $item->costo_unitario,
                        'subtotal'          => $item->costo_unitario * $cantidad,
                    ]);

                    Movimiento::create([
                        'item_id'     => $itemId,
                        'tipo'        => 'Egreso',
                        'accion'      => 'Prestamo',
                        'cantidad'    => $cantidad,
                        'fecha'       => now(),
                        'user_id'     => auth()->id(),
                        'prestamo_id' => $prestamo->id,
                        'nota'        => 'Actualización ' . $prestamo->codigo,
                    ]);
                }
            }
        });

        return redirect()
            ->route('prestamos.show',$prestamo)
            ->with('status','Préstamo actualizado');
    }

    /* ============================================================
       INCIDENTE
       ============================================================ */
    public function storeIncidente(Request $request, Prestamo $prestamo)
    {
        $data = $request->validate([
            'item_id' => ['required','exists:items,id'],
            'tipo'    => ['required', Rule::in(['Falta','Daño','Pérdida','Otro'])],
            'nota'    => ['nullable','string','max:2000'],
        ]);

        $existe = $prestamo->detalles()
            ->where('item_id',$data['item_id'])
            ->exists();

        if (!$existe) {
            return back()
                ->withErrors(['item_id'=>'El ítem no pertenece al préstamo'])
                ->withInput();
        }

        PrestamoIncidente::create([
            'prestamo_id' => $prestamo->id,
            'item_id'     => $data['item_id'],
            'persona_id'  => $prestamo->persona_id,
            'user_id'     => auth()->id(),
            'tipo'        => $data['tipo'],
            'nota'        => $data['nota'] ?? null,
        ]);

        if ($prestamo->estado !== 'Completo') {
            $prestamo->update(['estado'=>'Observado']);
        }

        return back()->with('status','Incidente registrado.');
    }
}

