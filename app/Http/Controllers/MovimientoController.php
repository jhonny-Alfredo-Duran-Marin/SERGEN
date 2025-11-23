<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Item;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        // ESTADÍSTICAS DEL DÍA
        $hoy = now()->format('Y-m-d');
        $stats = [
            'ingresos_hoy' => Movimiento::where('tipo', 'Ingreso')
                ->whereDate('fecha', $hoy)->sum('cantidad'),
            'egresos_hoy'  => Movimiento::where('tipo', 'Egreso')
                ->whereDate('fecha', $hoy)->sum('cantidad'),
            'total_hoy'    => Movimiento::whereDate('fecha', $hoy)->count(),
            'usuarios_hoy' => Movimiento::whereDate('fecha', $hoy)
                ->distinct('user_id')->count('user_id'),
        ];

        // FILTROS
        $query = Movimiento::with(['item', 'user', 'prestamo', 'devolucion'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->q;
                $q->whereHas('item', fn($qq) => $qq->where('codigo', 'like', "%$term%")
                    ->orWhere('descripcion', 'like', "%$term%"))
                  ->orWhereHas('user', fn($qq) => $qq->where('name', 'like', "%$term%"));
            })
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->filled('item_id'), fn($q) => $q->where('item_id', $request->item_id))
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha', '<=', $request->fecha_hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        $movimientos = $query->paginate(25)->withQueryString();
        $items = Item::orderBy('codigo')->get(['id', 'codigo', 'descripcion']);

        return view('movimientos.index', compact('movimientos', 'stats', 'items'));
    }
}
