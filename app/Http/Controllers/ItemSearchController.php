<?php
// app/Http/Controllers/ItemSearchController.php
namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:items.view']);
    }
    public function search(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $query = Item::query()->select('id', 'codigo', 'descripcion', 'cantidad', 'medida_id')
            ->with(['medida:id,simbolo']);

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('codigo', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhere('fabricante', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('descripcion')->limit(25)->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'codigo' => $i->codigo,
                'descripcion' => $i->descripcion,
                'stock' => (int)$i->cantidad,
                'medida' => $i->medida?->simbolo,
            ]);

        return response()->json($items);
    }
}
