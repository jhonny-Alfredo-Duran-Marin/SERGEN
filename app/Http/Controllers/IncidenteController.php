<?php

namespace App\Http\Controllers;

use App\Models\{Incidente, IncidenteDevolucion, Item, Movimiento, Prestamo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidenteController extends Controller
{
    public function index()
    {
        // Cargamos la relación 'persona' para toda la colección de una vez
        $incidentes = Incidente::with('persona')->orderByDesc('created_at')->paginate(20);

        return view('incidentes.index', compact('incidentes'));
    }

    public function show(Incidente $incidente)
    {
        $incidente->load(['persona', 'items', 'devoluciones.item']);
        return view('incidentes.show', compact('incidente'));
    }

    public function registrarDevolucion(Request $request, Incidente $incidente)
    {
        $request->validate(['items' => 'required|array']);

        try {
            DB::transaction(function () use ($request, $incidente) {
                foreach ($request->items as $row) {
                    $cant = (int)($row['cantidad_devuelta'] ?? 0);
                    if ($cant <= 0) continue;

                    IncidenteDevolucion::create([
                        'incident_id'       => $incidente->id,
                        'item_id'           => $row['item_id'],
                        'cantidad_devuelta' => $cant,
                        'resultado'         => $row['resultado'],
                        'tipo'              => $row['tipo'],
                        'observacion'       => $row['observacion'] ?? null,
                    ]);

                    if ($row['resultado'] === 'DEVUELTO_OK') {
                        Item::where('id', $row['item_id'])->increment('cantidad', $cant);
                    }
                }

                $this->actualizarEstadoIncidente($incidente);

                // Sincronización con Préstamo
                if ($incidente->estado === 'COMPLETADO' && $incidente->prestamo) {
                    $incidente->prestamo->verificarYCompletarEstado();
                }
            });

            return redirect()->route('incidentes.show', $incidente)->with('status', 'Devoluciones procesadas.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function completar(Incidente $incidente)
    {
        DB::transaction(function () use ($incidente) {
            $incidente->update(['estado' => 'COMPLETADO']);

            if ($incidente->prestamo) {
                $incidente->prestamo->verificarYCompletarEstado();
            }
        });

        return back()->with('status', 'Incidente y Préstamo verificados.');
    }

    private function actualizarEstadoIncidente(Incidente $incidente)
    {
        $totalAfectado = DB::table('incidente_items')->where('incidente_id', $incidente->id)->sum('cantidad');
        $totalDevuelto = IncidenteDevolucion::where('incident_id', $incidente->id)->sum('cantidad_devuelta');

        if ($totalDevuelto >= $totalAfectado) {
            $incidente->update(['estado' => 'COMPLETADO']);
        } elseif ($totalDevuelto > 0) {
            $incidente->update(['estado' => 'EN_PROCESO']);
        }
    }

    /**
     * HISTORIAL DE INCIDENTE (Basado en formato de Préstamos)
     */
    public function recibo($id) // Cambiamos a $id para asegurar la búsqueda
    {
        // 1. Buscamos el incidente y cargamos TODAS las relaciones de golpe
        $incidente = Incidente::with(['persona', 'items', 'devoluciones.item', 'prestamo.proyecto'])
            ->findOrFail($id);

        // 2. Verificación de seguridad antes de generar el PDF
        if (!$incidente->persona) {
            return back()->withErrors(['error' => 'No se encontró la persona asociada a este incidente.']);
        }

        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('vendor/adminlte/dist/img/logoSer_Gen2.jpg')));

        $data = [
            'incidente'  => $incidente,
            'titulo'     => 'HISTORIAL DE DEVOLUCIONES - INCIDENTE',
            'logoBase64' => $logoBase64
        ];

        return Pdf::loadView('incidentes.historial_pdf', $data)
            ->setPaper('a4')
            ->stream("Historial_INC_{$incidente->codigo}.pdf");
    }
}
