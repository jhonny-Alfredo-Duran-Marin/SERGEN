<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Item;
use App\Models\Movimiento;
use App\Models\Prestamo;
use App\Models\Incidente; // Asegúrate de tener este modelo
use App\Models\Consumo;
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
        return $this->operativoDashboard();
    }

    private function superAdminDashboard()
    {
        $stats = [
            'items_total'       => Item::count(),
            'compras_mes'       => Compra::whereMonth('created_at', now()->month)->count(),
            'prestamos_activos' => Prestamo::where('estado', '!=', 'Completo')->count(),
            'incidentes_total'  => Incidente::count(), // Cantidad de incidentes
        ];

        // Historial de acciones y registros recientes
        $ultimosPrestamos = Prestamo::with(['persona', 'proyecto'])->latest()->take(5)->get();
        $ultimasCompras   = Compra::latest()->take(5)->get();
        $ultimosConsumos  = Consumo::with(['item', 'proyecto'])->latest()->take(5)->get();
        $ultimosMovimientos = Movimiento::with(['item', 'user'])->latest()->take(8)->get();

        return view('dashboards.super-admin', compact(
            'stats',
            'ultimosPrestamos',
            'ultimasCompras',
            'ultimosConsumos',
            'ultimosMovimientos'
        ));
    }

    private function operativoDashboard()
    {
        $itemsDisponibles = Item::where('cantidad', '>', 0)->with(['categoria', 'medida'])->paginate(15);
        return view('dashboards.operativo', compact('itemsDisponibles'));
    }
}
