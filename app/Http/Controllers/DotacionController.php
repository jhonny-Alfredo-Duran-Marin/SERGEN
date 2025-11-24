<?php

namespace App\Http\Controllers;

use App\Models\Dotacion;
use App\Models\DotacionItem;
use App\Models\Incidente;
use App\Models\IncidenteItem;
use App\Models\Item;
use App\Models\Persona;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DotacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:dotaciones.view'])->only(['index', 'show']);
        $this->middleware(['permission:dotaciones.create'])->only(['create', 'store']);
        $this->middleware(['permission:dotaciones.update'])->only(['edit', 'update']);
        $this->middleware(['permission:dotaciones.delete'])->only(['destroy']);
    }

    /* ======================================================
     * INDEX
     * ====================================================== */
    public function index(Request $request)
    {
        $q       = $request->string('q')->toString();
        $persId  = $request->integer('persona_id');
        $f1      = $request->date('desde');
        $f2      = $request->date('hasta');

        $query = Dotacion::query()
            ->with(['persona'])
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('persona', function ($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%");
            });
        }

        if ($persId)  $query->where('persona_id', $persId);
        if ($f1)      $query->whereDate('fecha', '>=', $f1);
        if ($f2)      $query->whereDate('fecha', '<=', $f2);

        // Calcular estadísticas ANTES de paginar
        $total = (clone $query)->count();
        $abiertas = (clone $query)->where('estado_final', 'ABIERTA')->count();
        $devueltas = (clone $query)->where('estado_final', 'DEVUELTA')->count();
        $completadas = (clone $query)->where('estado_final', 'COMPLETADA')->count();

        $dotaciones = $query->paginate(20)->withQueryString();
        $personas   = Persona::orderBy('nombre')->get(['id', 'nombre']);

        return view('dotaciones.index', compact(
            'dotaciones',
            'personas',
            'total',
            'abiertas',
            'devueltas',
            'completadas'
        ));
    }
    /* ======================================================
     * CREATE
     * ====================================================== */
    public function create()
    {
        $items    = Item::orderBy('descripcion')->get(['id', 'codigo', 'descripcion', 'cantidad']);
        $personas = Persona::orderBy('nombre')->get(['id', 'nombre']);

        return view('dotaciones.create', compact('items', 'personas'));
    }

    /* ======================================================
     * STORE MULTI ITEMS
     * ====================================================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'persona_id' => ['required', 'exists:personas,id'],
            'fecha'      => ['required', 'date'],
            'items'      => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {

            $dotacion = Dotacion::create([
                'persona_id'   => $data['persona_id'],
                'fecha'        => $data['fecha'],
                'estado_final' => 'ABIERTA'
            ]);

            foreach ($data['items'] as $row) {

                $item = Item::lockForUpdate()->find($row['item_id']);

                if ($row['cantidad'] > $item->cantidad) {
                    abort(422, "No hay stock suficiente para {$item->descripcion}");
                }

                DotacionItem::create([
                    'dotacion_id' => $dotacion->id,
                    'item_id'     => $row['item_id'],
                    'cantidad'    => $row['cantidad'],
                    'estado_item' => 'EN_USO'
                ]);

                $item->decrement('cantidad', $row['cantidad']);
                $item->update(['estado' => 'Dotado']);
            }
        });

        return redirect()->route('dotaciones.index')
            ->with('status', 'Dotación registrada correctamente.');
    }

    /* ======================================================
     * SHOW
     * ====================================================== */
    public function show(Dotacion $dotacion)
    {
        $dotacion->load(['persona', 'items.item']);
        return view('dotaciones.show', compact('dotacion'));
    }

    /* ======================================================
     * EDIT
     * ====================================================== */
    public function edit(Dotacion $dotacion)
    {
        $dotacion->load('items.item');

        $items    = Item::orderBy('descripcion')->get(['id', 'codigo', 'descripcion', 'cantidad']);
        $personas = Persona::orderBy('nombre')->get(['id', 'nombre']);

        return view('dotaciones.edit', compact('dotacion', 'items', 'personas'));
    }

    /* ======================================================
     * UPDATE MULTI ITEMS
     * ====================================================== */
    public function update(Request $request, Dotacion $dotacion)
    {
        $data = $request->validate([
            'persona_id' => ['required', 'exists:personas,id'],
            'fecha'      => ['required', 'date'],
            'items'      => ['required', 'array', 'min:1'],

            'items.*.dotacion_item_id' => ['nullable', 'exists:dotacion_items,id'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data, $dotacion) {

            $dotacion->update([
                'persona_id' => $data['persona_id'],
                'fecha'      => $data['fecha'],
            ]);

            $ids_recibidos = [];

            foreach ($data['items'] as $row) {

                if (!empty($row['dotacion_item_id'])) {

                    $dotItem = DotacionItem::lockForUpdate()->find($row['dotacion_item_id']);
                    $item = Item::lockForUpdate()->find($row['item_id']);

                    $item->increment('cantidad', $dotItem->cantidad);

                    if ($row['cantidad'] > $item->cantidad) {
                        abort(422, "No hay stock suficiente para {$item->descripcion}");
                    }

                    $dotItem->update([
                        'item_id'  => $row['item_id'],
                        'cantidad' => $row['cantidad'],
                    ]);

                    $item->decrement('cantidad', $row['cantidad']);
                    $item->update(['estado' => 'Dotado']);

                    $ids_recibidos[] = $dotItem->id;
                } else {

                    $item = Item::lockForUpdate()->find($row['item_id']);

                    if ($row['cantidad'] > $item->cantidad) {
                        abort(422, "Stock insuficiente para {$item->descripcion}");
                    }

                    $dotItem = DotacionItem::create([
                        'dotacion_id' => $dotacion->id,
                        'item_id'     => $row['item_id'],
                        'cantidad'    => $row['cantidad'],
                        'estado_item' => 'EN_USO'
                    ]);

                    $item->decrement('cantidad', $row['cantidad']);
                    $item->update(['estado' => 'Dotado']);

                    $ids_recibidos[] = $dotItem->id;
                }
            }

            DotacionItem::where('dotacion_id', $dotacion->id)
                ->whereNotIn('id', $ids_recibidos)
                ->each(function ($di) {
                    $di->item->increment('cantidad', $di->cantidad);
                    $di->item->update(['estado' => 'Disponible']);
                    $di->delete();
                });
        });

        return redirect()->route('dotaciones.show', $dotacion)
            ->with('status', 'Dotación actualizada.');
    }

    /* ======================================================
     * FORM DEVOLVER
     * ====================================================== */
    public function formDevolver(Dotacion $dotacion)
    {
        $dotacion->load(['items.item']);
        return view('dotaciones.devolver', compact('dotacion'));
    }

    /* ======================================================
     * PROCESAR DEVOLUCIÓN DEFINITIVO
     * ====================================================== */
    public function procesarDevolucion(Request $request, Dotacion $dotacion)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.dotacion_item_id' => ['required', 'exists:dotacion_items,id'],
            'items.*.estado' => ['required', Rule::in(['OK', 'DANADO', 'PERDIDO', 'BAJA'])],
            'items.*.observacion' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $dotacion) {

            $items_incidente = [];
            $huboProblemas   = false;

            foreach ($data['items'] as $it) {

                $dotItem = DotacionItem::lockForUpdate()->find($it['dotacion_item_id']);
                $item    = $dotItem->item;
                $estado  = $it['estado'];

                $dotItem->update([
                    'estado_item'     => $estado,
                    'fecha_devolucion' => now(),
                    'observacion'     => $it['observacion'] ?? null,
                ]);

                switch ($estado) {

                    case 'OK':
                        $item->increment('cantidad', $dotItem->cantidad);
                        $item->update(['estado' => 'Disponible']);
                        break;

                    case 'DANADO':
                        $huboProblemas = true;
                        $item->update(['estado' => 'Observacion']);
                        $items_incidente[] = [
                            'item_id'     => $item->id,
                            'dotacion_id' => $dotacion->id,
                            'estado_item' => 'DANADO',
                            'cantidad'    => $dotItem->cantidad,
                            'observacion' => $it['observacion'] ?? null,
                        ];
                        break;

                    case 'PERDIDO':
                        $huboProblemas = true;
                        $item->update(['estado' => 'Observacion']);
                        $items_incidente[] = [
                            'item_id'     => $item->id,
                            'dotacion_id' => $dotacion->id,
                            'estado_item' => 'PERDIDO',
                            'cantidad'    => $dotItem->cantidad,
                            'observacion' => $it['observacion'] ?? null,
                        ];
                        break;

                    case 'BAJA':
                        $huboProblemas = true;
                        $item->update(['estado' => 'Baja']);
                        $items_incidente[] = [
                            'item_id'     => $item->id,
                            'dotacion_id' => $dotacion->id,
                            'estado_item' => 'BAJA',
                            'cantidad'    => $dotItem->cantidad,
                            'observacion' => $it['observacion'] ?? null,
                        ];
                        break;
                }
            }

            if ($huboProblemas) {

                $incidente = Incidente::create([
                    'codigo'          => Incidente::generarCodigo(),
                    'tipo'            => 'DOTACION',
                    'estado'          => 'ACTIVO',
                    'persona_id'      => $dotacion->persona_id,
                    'fecha_incidente' => now(),
                    'descripcion'     => 'Incidentes detectados en la devolución',
                ]);

                foreach ($items_incidente as $inc) {
                    IncidenteItem::create(array_merge($inc, [
                        'incident_id' => $incidente->id
                    ]));
                }

                $dotacion->update(['estado_final' => 'COMPLETADA']);
            } else {
                $dotacion->update(['estado_final' => 'DEVUELTA']);
            }
        });

        return redirect()->route('dotaciones.show', $dotacion)
            ->with('status', 'Devolución procesada correctamente.');
    }

    /* ======================================================
     * DELETE
     * ====================================================== */
    public function destroy(Dotacion $dotacion)
    {
        DB::transaction(function () use ($dotacion) {

            foreach ($dotacion->items as $di) {
                $di->item->increment('cantidad', $di->cantidad);
                $di->item->update(['estado' => 'Disponible']);
            }

            $dotacion->delete();
        });

        return redirect()->route('dotaciones.index')
            ->with('status', 'Dotación eliminada.');
    }


    public function pdf(Dotacion $dotacion)
    {
        $dotacion->load(['persona', 'items.item']);

        $pdf = Pdf::loadView('dotaciones.pdf', [
            'dotacion' => $dotacion
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('dotacion-' . $dotacion->id . '.pdf');
    }
}
