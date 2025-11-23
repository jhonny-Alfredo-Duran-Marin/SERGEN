<?php

// app/Http/Controllers/KitsController.php
namespace App\Http\Controllers;

use App\Models\KitEmergencia;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KitEmergenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:kits.view'])->only(['index', 'show']);
        $this->middleware(['permission:kits.create'])->only(['create', 'store']);
        $this->middleware(['permission:kits.update'])->only(['edit', 'update']);
        $this->middleware(['permission:kits.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $kits = KitEmergencia::withCount('items')->orderByDesc('id')->paginate(15);
        return view('kits.index', compact('kits'));
    }

    public function create()
    {
        $itemsAll = Item::with('medida:id,simbolo')
            ->select('id', 'codigo', 'descripcion', 'cantidad', 'medida_id')
            ->orderBy('descripcion')->get();

        $last = KitEmergencia::orderByDesc('id')->first();
        $nextCode = 'KIT-' . str_pad((string)(($last?->id ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        $itemsForJs = $itemsAll->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'desc' => $i->descripcion,
            'stock' => (int)$i->cantidad,
            'med' => optional($i->medida)->simbolo
        ])->values();

        return view('kits.create', compact('itemsAll', 'nextCode', 'itemsForJs'));
    }

    // app/Http/Controllers/KitsController.php

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:5000'],
            'item_ids'     => ['required', 'array', 'min:1'],
            'item_ids.*'   => ['integer', 'exists:items,id'],
            'cantidades'   => ['required', 'array', 'min:1'],
            'cantidades.*' => ['integer', 'min:1'],
        ]);


        try {
            DB::transaction(function () use ($data) {
                $kit = KitEmergencia::create([
                    'nombre'      => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                ]);

                $attach = [];

                foreach ($data['item_ids'] as $i => $itemId) {
                    $cant = (int)($data['cantidades'][$i] ?? 0);
                    if ($cant <= 0) continue;

                    /** @var \App\Models\Item $item */
                    $item = Item::lockForUpdate()->findOrFail($itemId);

                    // Validar stock disponible
                    if ($item->cantidad < $cant) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "cantidades.$i" => "Stock insuficiente para «{$item->descripcion}». Disponible: {$item->cantidad}",
                        ]);
                    }

                    // Descontar stock
                    $item->decrement('cantidad', $cant);

                    // Agregar al pivot
                    $attach[$itemId] = ['cantidad' => $cant];
                }

                if (empty($attach)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'cantidades' => 'Debes indicar cantidades mayores a 0.',
                    ]);
                }

                $kit->items()->sync($attach);
            });
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'No se pudo guardar el kit: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('kits.index')->with('status', 'Kit creado y stock descontado.');
    }


    public function edit(KitEmergencia $kit)
    {
        $itemsAll = Item::with('medida:id,simbolo')
            ->select('id', 'codigo', 'descripcion', 'cantidad', 'medida_id')
            ->orderBy('descripcion')->get();

        $itemsForJs = $itemsAll->map(fn($i) => [
            'id' => $i->id,
            'codigo' => $i->codigo,
            'desc' => $i->descripcion,
            'stock' => (int)$i->cantidad,
            'med' => optional($i->medida)->simbolo
        ])->values();

        $preselect = $kit->items->map(fn($it) => [
            'id' => $it->id,
            'cant' => (int)$it->pivot->cantidad
        ])->values();

        return view('kits.edit', compact('kit', 'itemsAll', 'itemsForJs', 'preselect'));
    }

    public function update(Request $request, KitEmergencia $kit)
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:5000'],
            'item_ids'     => ['required', 'array', 'min:1'],
            'item_ids.*'   => ['integer', 'exists:items,id'],
            'cantidades'   => ['required', 'array', 'min:1'],
            'cantidades.*' => ['integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($kit, $data) {
                // Mapa actual (item_id => cantidad_en_pivot)
                $current = $kit->items()->pluck('kit_emergencia_item.cantidad', 'items.id'); // Collection

                // Mapa nuevo desde la request
                $new = collect($data['item_ids'])->mapWithKeys(function ($itemId, $idx) use ($data) {
                    $cant = (int)($data['cantidades'][$idx] ?? 0);
                    return $cant > 0 ? [(int)$itemId => $cant] : [];
                });

                // Ajuste de stock por diferencia
                $allItemIds = $current->keys()->merge($new->keys())->unique();

                foreach ($allItemIds as $itemId) {
                    $oldQty = (int)($current[$itemId] ?? 0);
                    $newQty = (int)($new[$itemId] ?? 0);
                    $delta  = $newQty - $oldQty; // >0: descontar más; <0: devolver stock

                    if ($delta === 0) continue;

                    /** @var \App\Models\Item $item */
                    $item = \App\Models\Item::lockForUpdate()->findOrFail($itemId);

                    if ($delta > 0) {
                        // Necesitamos más stock
                        if ($item->cantidad < $delta) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'cantidades' => "Stock insuficiente para «{$item->descripcion}». Necesita $delta y hay {$item->cantidad}.",
                            ]);
                        }
                        $item->decrement('cantidad', $delta);
                    } else {
                        // Devolvemos stock
                        $item->increment('cantidad', abs($delta));
                    }
                }

                // Actualizar datos de kit y pivot
                $kit->update([
                    'nombre'      => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                ]);

                // Preparar sync con cantidades
                $attach = $new->map(fn($c) => ['cantidad' => $c])->toArray();
                if (empty($attach)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'cantidades' => 'Debes indicar cantidades mayores a 0.',
                    ]);
                }
                $kit->items()->sync($attach);
            });
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'No se pudo actualizar el kit: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('kits.index')->with('status', 'Kit actualizado y stock ajustado.');
    }
    public function show(KitEmergencia $kit)
    {
        // Carga ítems + medida y el conteo
        $kit->load(['items.medida'])->loadCount('items');

        // (opcional) totales
        $totalItems = (int) $kit->items->sum('pivot.cantidad');

        return view('kits.show', compact('kit', 'totalItems'));
    }

    public function destroy(KitEmergencia $kit)
    {
        try {
            DB::transaction(function () use ($kit) {
                // Devolver stock antes de eliminar
                $kit->load('items');

                foreach ($kit->items as $it) {
                    $qty = (int)$it->pivot->cantidad;
                    if ($qty > 0) {
                        // lock + devolver
                        $item = Item::lockForUpdate()->find($it->id);
                        if ($item) {
                            $item->increment('cantidad', $qty);
                        }
                    }
                }

                $kit->items()->detach();
                $kit->delete();
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['general' => 'No se pudo eliminar el kit: ' . $e->getMessage()]);
        }

        return redirect()->route('kits.index')->with('status', 'Kit eliminado y stock repuesto.');
    }
}
