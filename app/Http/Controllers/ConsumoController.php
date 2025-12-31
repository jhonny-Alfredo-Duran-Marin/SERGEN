<?php

namespace App\Http\Controllers;

use App\Models\Consumo;
use App\Models\Persona;
use App\Models\Proyecto;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsumoController extends Controller
{
    public function index(Request $request)
    {
        $query = Consumo::with(['item', 'persona', 'proyecto', 'prestamo']);

        // Filtros Inteligentes
        if ($request->filled('persona_id')) {
            $query->where('persona_id', $request->persona_id);
        }
        if ($request->filled('proyecto_id')) {
            $query->where('proyecto_id', $request->proyecto_id);
        }
        if ($request->filled('prestamo_id')) {
            $query->where('prestamo_id', $request->prestamo_id);
        }

        // Estadísticas Dinámicas
        $consumos = $query->latest()->paginate(20)->withQueryString();
        $totalDinero = $query->sum(\DB::raw('cantidad_consumida * precio_unitario'));
        $totalItems = $query->sum('cantidad_consumida');

        $personas = Persona::orderBy('nombre')->get();
        $proyectos = Proyecto::orderBy('descripcion')->get();
        $prestamos = Prestamo::latest()->take(50)->get();

        return view('consumos.index', compact('consumos', 'totalDinero', 'totalItems', 'personas', 'proyectos', 'prestamos'));
    }

    public function show(Consumo $consumo)
    {
        return view('consumos.show', compact('consumo'));
    }

    // 1. REPORTE GENERAL (Botón de arriba con filtros)
    public function reportepdf(Request $request)
    {
        $query = Consumo::with(['item', 'persona', 'proyecto']);

        // Filtros aplicados
        if ($request->filled('persona_id')) $query->where('persona_id', $request->persona_id);
        if ($request->filled('proyecto_id')) $query->where('proyecto_id', $request->proyecto_id);

        $consumos = $query->latest()->get();

        // Cálculo de total sumando cada subtotal
        $total = $consumos->sum(fn($c) => $c->cantidad_consumida * $c->precio_unitario);

        // Lógica del logo Ser.Gen
        $logoPath = public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('consumos.reporte_general_pdf', [
            'consumos'   => $consumos,
            'total'      => $total,
            'logoBase64' => $logoBase64,
            'titulo'     => 'REPORTE CONSOLIDADO DE CONSUMO DE MATERIALES'
        ]);

        // Usamos 'landscape' para que entren más columnas cómodamente
        return $pdf->setPaper('a4', 'landscape')->stream('reporte_general_consumos.pdf');
    }

    // 2. RECIBO INDIVIDUAL (Botón en cada fila de la tabla)
    public function imprimirRecibo(Consumo $consumo)
    {
        $consumo->load(['item', 'persona', 'proyecto']);

        $logoPath = public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // Calculamos el subtotal aquí para enviarlo a la vista
        $subtotal = $consumo->cantidad_consumida * $consumo->precio_unitario;

        return Pdf::loadView('consumos.recibo_individual_pdf', [
            'registro'   => $consumo,
            'logoBase64' => $logoBase64,
            'titulo'     => 'RECIBO DE CONSUMO DIRECTO',
            'subtotal'   => $subtotal, // Esta es la variable que faltaba reconocer
        ])->setPaper('a4', 'portrait')->stream("recibo_consumo_{$consumo->id}.pdf");
    }
}
