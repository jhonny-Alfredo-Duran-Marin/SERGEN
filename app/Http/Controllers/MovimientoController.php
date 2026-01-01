<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Item;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function __construct()
    {
       $this->middleware(['permission:movimientos.view']);
    }
    public function index(Request $request)
    {
        // ESTADÍSTICAS DEL DÍA
        $hoy = now()->format('Y-m-d');
        $stats = [
            'ingresos_hoy' => Movimiento::where('accion', 'AUMENTO_STOCK')
                ->orWhere('accion', 'CREAR_ITEM')
                ->orWhere('accion', 'DEVOLUCION')
                ->whereDate('fecha', $hoy)
                ->sum('cantidad'),

            'egresos_hoy' => Movimiento::where('accion', 'DESCUENTO_STOCK')
                ->orWhere('accion', 'PRESTAMO')
                ->orWhere('accion', 'ELIMINAR_ITEM')
                ->whereDate('fecha', $hoy)
                ->sum('cantidad'),

            'total_hoy'    => Movimiento::whereDate('fecha', $hoy)->count(),
            'usuarios_hoy' => Movimiento::whereDate('fecha', $hoy)
                ->distinct('user_id')->count('user_id'),
        ];

        // CONSULTA BASE
        $query = Movimiento::with(['item', 'user', 'prestamo', 'devolucion'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->q;
                $q->whereHas('item', fn($qq) => $qq->where('codigo', 'like', "%$term%")
                    ->orWhere('descripcion', 'like', "%$term%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('name', 'like', "%$term%"));
            })
            ->when($request->filled('item_id'), fn($q) => $q->where('item_id', $request->item_id))
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha', '<=', $request->fecha_hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        $movimientos = $query->paginate(25)->withQueryString();

        // ASIGNAR TIPO DE MOVIMIENTO (Ingreso, Egreso, Info)
        foreach ($movimientos as $m) {
            $m->display_tipo = match ($m->accion) {

                // INGRESOS
                'CREAR_ITEM'        => 'Ingreso',
                'AUMENTO_STOCK'     => 'Ingreso',
                'DEVOLUCION'        => 'Ingreso',
                'CREAR_COMPRA'      => 'Ingreso',

                // EGRESOS
                'DESCUENTO_STOCK'   => 'Egreso',
                'PRESTAMO'          => 'Egreso',
                'ELIMINAR_ITEM'     => 'Egreso',
                'ELIMINAR_COMPRA'   => 'Egreso',

                // INFORMACIÓN
                'EDITAR_ITEM'       => 'Info',
                'EDITAR_COMPRA'     => 'Info',
                'COMPRA_ACTUALIZADA' => 'Info',

                default             => 'Info',
            };
        }


        $items = Item::orderBy('codigo')->get(['id', 'codigo', 'descripcion']);

        return view('movimientos.index', compact('movimientos', 'stats', 'items'));
    }
}
