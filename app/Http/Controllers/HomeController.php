<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Item;
use App\Models\Movimiento;
use App\Models\Prestamo;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->can('movimientos.view') || $user->can('permissions.view') || $user->id === 1) {
            return $this->superAdminDashboard();
        }

        // SI ES OPERATIVO / USUARIO NORMAL → DASHBOARD SIMPLE
        return $this->operativoDashboard();
    }

    // HomeController.php
    private function superAdminDashboard()
    {
        $stats = [
            'items_total'       => Item::count(),
            'items_bajo_stock'  => Item::where('cantidad', '<', 5)->count(),
            'prestamos_activos' => Prestamo::where('estado', '!=', 'Completo')->count(),
            'movimientos_hoy'   => Movimiento::whereDate('fecha', today())->count(),
        ];

        $ultimosMovimientos = Movimiento::with('item', 'user')
            ->latest()->take(6)->get();

        $ultimasCompras = Compra::latest()->take(5)->get();

        $stockCritico = Item::where('cantidad', '<', 3)
            ->orderBy('cantidad')->take(8)->get();

        return view('dashboards.super-admin', compact(
            'stats',
            'ultimosMovimientos',
            'ultimasCompras',
            'stockCritico'
        ));
    }
    private function operativoDashboard()
    {
        $itemsDisponibles = Item::where('cantidad', '>', 0)
            ->with('categoria', 'medida')
            ->orderBy('descripcion')
            ->paginate(15);

        return view('dashboards.operativo', compact('itemsDisponibles'));
    }
}
