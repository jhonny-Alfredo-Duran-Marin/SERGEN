<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use App\Models\IncidenteDevolucion;
use App\Models\Item;
use App\Models\Prestamo;
use App\Models\Dotacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidenteController extends Controller
{
    public function index()
    {
        $query = Incidente::with('persona');

        // Calcular estadísticas ANTES de paginar
        $total = $query->count();
        $activos = (clone $query)->where('estado', 'ACTIVO')->count();
        $enProceso = (clone $query)->where('estado', 'EN_PROCESO')->count();
        $completados = (clone $query)->where('estado', 'COMPLETADO')->count();

        // Paginar
        $incidentes = $query->orderByDesc('fecha_incidente')->paginate(20);

        return view('incidentes.index', compact(
            'incidentes',
            'total',
            'activos',
            'enProceso',
            'completados'
        ));
    }

    public function create()
    {
        $personas = Persona::withoutTrashed()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $items = Item::orderBy('descripcion')->get();

        $prestamos = Prestamo::with('persona')->get();
        $dotaciones = Dotacion::with('persona')->get();

        return view('incidentes.create', compact('personas', 'items', 'prestamos', 'dotaciones'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'            => 'required|in:PRESTAMO,DOTACION',
            'persona_id'      => 'required|exists:personas,id',
            'relacion_id'     => 'nullable|integer',
            'fecha_incidente' => 'required|date',
            'descripcion'     => 'nullable|string',

            'items'                 => 'required|array|min:1',
            'items.*.item_id'       => 'required|exists:items,id',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.estado_item'   => 'required',
            'items.*.observacion'   => 'nullable|string'
        ]);

        DB::transaction(function () use ($data) {

            $incidente = Incidente::create([
                'codigo'          => Incidente::generarCodigo(),
                'tipo'            => $data['tipo'],
                'estado'          => 'ACTIVO',
                'persona_id'      => $data['persona_id'],
                'fecha_incidente' => $data['fecha_incidente'],
                'descripcion'     => $data['descripcion'],
            ]);

            foreach ($data['items'] as $it) {
                $incidente->items()->attach($it['item_id'], [
                    'cantidad'      => $it['cantidad'],
                    'estado_item'   => $it['estado_item'],
                    'observacion'   => $it['observacion'] ?? null,
                    'prestamo_id'   => $data['tipo'] === 'PRESTAMO' ? $data['relacion_id'] : null,
                    'dotacion_id'   => $data['tipo'] === 'DOTACION' ? $data['relacion_id'] : null,
                ]);
            }
        });

        return redirect()->route('incidentes.index')->with('status', 'Incidente registrado.');
    }

    public function edit(Incidente $incidente)
    {
        $incidente->load('items');

        return view('incidentes.edit', [
            'incidente'  => $incidente,
            'personas'   => Persona::orderBy('nombre')->get(),
            'prestamos'  => Prestamo::orderBy('id', 'desc')->get(),
            'dotaciones' => Dotacion::orderBy('id', 'desc')->get(),
            'items'      => Item::orderBy('descripcion')->get()
        ]);
    }

    public function update(Request $request, Incidente $incidente)
    {
        $data = $request->validate([
            'tipo'            => 'required|in:PRESTAMO,DOTACION',
            'persona_id'      => 'required|exists:personas,id',
            'relacion_id'     => 'nullable|integer',
            'fecha_incidente' => 'required|date',
            'descripcion'     => 'nullable|string',

            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'required|exists:items,id',
            'items.*.cantidad'    => 'required|integer|min:1',
            'items.*.estado_item' => 'required',
            'items.*.observacion' => 'nullable|string'
        ]);

        DB::transaction(function () use ($data, $incidente) {

            $incidente->update([
                'tipo'            => $data['tipo'],
                'persona_id'      => $data['persona_id'],
                'fecha_incidente' => $data['fecha_incidente'],
                'descripcion'     => $data['descripcion'],
            ]);

            $incidente->items()->detach();

            foreach ($data['items'] as $it) {
                $incidente->items()->attach($it['item_id'], [
                    'cantidad'      => $it['cantidad'],
                    'estado_item'   => $it['estado_item'],
                    'observacion'   => $it['observacion'] ?? null,
                    'prestamo_id'   => $data['tipo'] === 'PRESTAMO' ? $data['relacion_id'] : null,
                    'dotacion_id'   => $data['tipo'] === 'DOTACION' ? $data['relacion_id'] : null,
                ]);
            }
        });

        return redirect()->route('incidentes.index')->with('status', 'Incidente actualizado.');
    }

    public function devolverForm(Incidente $incidente)
    {
        $incidente->load('items');
        return view('incidentes.devolver', compact('incidente'));
    }

    public function registrarDevolucion(Request $request, Incidente $incidente)
    {
        $data = $request->validate([
            'items'                      => 'required|array',
            'items.*.item_id'            => 'required|exists:items,id',
            'items.*.devolver'           => 'nullable|boolean',
            'items.*.cantidad_devuelta'  => 'nullable|integer|min:1',
            'items.*.resultado'          => 'nullable|in:DEVUELTO_OK,DEVUELTO_DANADO,NO_RECUPERADO,REPARABLE',
            'items.*.observacion'        => 'nullable|string'
        ]);

        DB::transaction(function () use ($data, $incidente) {

            foreach ($data['items'] as $row) {

                // si no marcó devolver → ignorar
                if (empty($row['devolver'])) continue;

                // validar cantidad máxima permitida
                $pivot = $incidente->items()->where('item_id', $row['item_id'])->first();

                if ($row['cantidad_devuelta'] > $pivot->pivot->cantidad) {
                    throw new \Exception("No puede devolver más de lo afectado.");
                }

                // registrar devolución
                IncidenteDevolucion::create([
                    'incident_id'       => $incidente->id,
                    'item_id'           => $row['item_id'],
                    'cantidad_devuelta' => $row['cantidad_devuelta'],
                    'resultado'         => $row['resultado'],
                    'aceptado'          => true,
                    'observacion'       => $row['observacion'],
                ]);

                // si devuelve OK, se suma al stock
                if ($row['resultado'] === 'DEVUELTO_OK') {
                    Item::find($row['item_id'])->increment('cantidad', $row['cantidad_devuelta']);
                }
            }

            // actualizar estado del incidente
            $total = $incidente->items()->sum('incidente_items.cantidad');
            $devueltos = IncidenteDevolucion::where('incident_id', $incidente->id)
                ->sum('cantidad_devuelta');

            if ($devueltos >= $total) {
                $incidente->update(['estado' => 'COMPLETADO']);
            }
        });

        return redirect()->route('incidentes.show', $incidente)
            ->with('status', 'Devolución registrada correctamente.');
    }


    public function show(Incidente $incidente)
    {
        $incidente->load(['persona', 'items', 'devoluciones.item']);
        return view('incidentes.show', compact('incidente'));
    }

    public function recibo(IncidenteDevolucion $devolucion)
    {
        $devolucion->load(['item', 'incidente.persona']);

        $pdf = Pdf::loadView('incidentes.recibo', [
            'devolucion' => $devolucion
        ])->setPaper('A5', 'portrait');

        $devolucion->update(['impreso' => true]);

        return $pdf->stream('recibo-' . $devolucion->id . '.pdf');
    }

    public function destroy(Incidente $incidente)
    {
        $incidente->delete();
        return back()->with('status', 'Incidente eliminado.');
    }
}
