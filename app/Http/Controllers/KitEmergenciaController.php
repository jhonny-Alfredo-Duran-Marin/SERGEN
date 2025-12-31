<?php

namespace App\Http\Controllers;

use App\Models\KitEmergencia;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitEmergenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:kits.view'])->only(['index', 'show']);
        $this->middleware(['permission:kits.create'])->only(['create', 'store']);
        $this->middleware(['permission:kits.update'])->only(['edit', 'update']);
        $this->middleware(['permission:kits.delete'])->only(['destroy']);
    }
    public function index()
    {
        $kits = KitEmergencia::withCount('items')->orderByDesc('id')->paginate(15);
        return view('kits.index', compact('kits'));
    }

    public function create()
    {
        $itemsAll = Item::where('cantidad', '>', 0)->orderBy('descripcion')->get();

        // --- RESTAURACIÓN DE LA VARIABLE $nextCode ---
        $last = KitEmergencia::orderByDesc('id')->first();
        $nextCode = 'KIT-' . str_pad((string)(($last?->id ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        $itemsForJs = $itemsAll->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'desc' => $i->descripcion,
            'stock' => (int)$i->cantidad
        ])->values();

        return view('kits.create', compact('itemsAll', 'itemsForJs', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'item_ids' => 'required|array',
            'cantidades' => 'required|array',
            'estados_items' => 'required|array',
        ]);

        DB::transaction(function () use ($data) {
            $kit = KitEmergencia::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'estado' => 'Activo'
            ]);

            foreach ($data['item_ids'] as $i => $itemId) {
                $cant = (int)$data['cantidades'][$i];
                $item = Item::lockForUpdate()->findOrFail($itemId);
                $item->decrement('cantidad', $cant);
                $item->update(['estado' => ($item->cantidad <= 0) ? 'En_Kit' : 'Disponible']);

                $kit->items()->attach($itemId, [
                    'cantidad' => $cant,
                    'estado' => $data['estados_items'][$i]
                ]);
            }
        });

        return redirect()->route('kits.index')->with('status', 'Kit creado correctamente.');
    }

    public function show(KitEmergencia $kit)
    {
        $kit->load(['items' => fn($q) => $q->withPivot('cantidad', 'estado')]);
        return view('kits.show', compact('kit'));
    }

    public function edit(KitEmergencia $kit)
    {
        $itemsAll = Item::orderBy('descripcion')->get();

        // Cargamos los items del kit actual para saber cuánto tienen asignado
        $kitItems = $kit->items->pluck('pivot.cantidad', 'id');

        $itemsForJs = $itemsAll->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'desc' => $i->descripcion,
            // IMPORTANTE: Stock disponible = lo que hay en bodega + lo que ya usa este kit
            'stock' => (int)$i->cantidad + (int)($kitItems[$i->id] ?? 0)
        ])->values();

        $preselect = $kit->items->map(fn($it) => [
            'id' => $it->id,
            'cant' => (int)$it->pivot->cantidad,
            'estado' => $it->pivot->estado
        ])->values();

        // Asegúrate de pasar itemsAll aquí
        return view('kits.edit', compact('kit', 'itemsAll', 'itemsForJs', 'preselect'));
    }

    public function update(Request $request, KitEmergencia $kit)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Pasivo,Observado',
            'item_ids' => 'required|array',
            'cantidades' => 'required|array',
            'estados_items' => 'required|array',
        ]);

        DB::transaction(function () use ($kit, $data) {
            $current = $kit->items()->pluck('kit_emergencia_item.cantidad', 'items.id');
            $newSync = [];

            foreach ($data['item_ids'] as $idx => $itemId) {
                $newQty = (int)$data['cantidades'][$idx];
                $oldQty = (int)($current[$itemId] ?? 0);
                $delta = $newQty - $oldQty;

                $item = Item::lockForUpdate()->findOrFail($itemId);
                if ($delta > 0) $item->decrement('cantidad', $delta);
                elseif ($delta < 0) $item->increment('cantidad', abs($delta));

                $item->update(['estado' => ($item->cantidad > 0) ? 'Disponible' : 'En_Kit']);

                $newSync[$itemId] = [
                    'cantidad' => $newQty,
                    'estado' => $data['estados_items'][$idx]
                ];
            }

            $kit->update([
                'nombre' => $data['nombre'],
                'estado' => $data['estado'],
                'descripcion' => $data['descripcion']
            ]);
            $kit->items()->sync($newSync);
        });

        return redirect()->route('kits.index')->with('status', 'Kit actualizado.');
    }

    public function destroy(KitEmergencia $kit)
    {
        DB::transaction(function () use ($kit) {
            foreach ($kit->items as $item) {
                $cant = (int)$item->pivot->cantidad;
                $itemModel = Item::lockForUpdate()->findOrFail($item->id);
                $itemModel->increment('cantidad', $cant);
                $itemModel->update(['estado' => 'Disponible']);
            }
            $kit->items()->detach();
            $kit->delete();
        });

        return redirect()->route('kits.index')->with('status', 'Kit eliminado.');
    }
}
