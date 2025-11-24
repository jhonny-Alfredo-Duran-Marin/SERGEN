<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Item;
use App\Models\PrestamoIncidente;
use App\Models\Consumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function create(Prestamo $prestamo)
    {
        $prestamo->load('persona', 'kit.items', 'detalles.item');
        return view('prestamos.devoluciones.create', compact('prestamo'));
    }

    public function store(Request $request, Prestamo $prestamo)
    {
        $items      = $request->items;
        $prestados  = $request->prestado;
        $devolver   = $request->devolver;
        $estados    = $request->estado;
        $consumidos = $request->consumido ?? []; // checkbox

        // para saber si el kit debe inhabilitarse
        $kit_con_problemas = false;

        DB::transaction(function () use (
            $items, $prestados, $devolver, $estados, $consumidos, $prestamo, &$kit_con_problemas
        ) {

            $items_con_problemas = [];

            foreach ($items as $index => $itemId) {

                $cantidadPrestada = (int)$prestados[$index];
                $cantidadDevuelta = (int)$devolver[$index];
                $tipoEstado       = $estados[$index];

                $item  = Item::lockForUpdate()->find($itemId);

                // ==============================
                // 1) CONSUMIDO
                // ==============================
                if (in_array($itemId, $consumidos)) {

                    // si un ítem es consumido, se descuenta del stock
                    $item->decrement('cantidad', $cantidadDevuelta);

                    if ($item->cantidad <= 0) {
                        $item->estado = 'Pasivo';
                        $item->save();
                    }

                    Consumo::create([
                        'item_id'            => $itemId,
                        'prestamo_id'        => $prestamo->id,
                        'persona_id'         => $prestamo->persona_id,
                        'proyecto_id'        => $prestamo->proyecto_id,
                        'cantidad_consumida' => $cantidadDevuelta,
                        'nota'               => 'Consumo registrado en devolución'
                    ]);

                    // seguimos sin generar incidentes en consumidos
                    continue;
                }

                // ==============================
                // 2) DEVOLUCIÓN NORMAL
                // ==============================
                if ($tipoEstado === 'ok') {

                    // sumamos cantidad devuelta al inventario
                    $item->increment('cantidad', $cantidadDevuelta);
                    continue;
                }

                // ==============================
                // 3) SI ESTA DAÑADO O INCOMPLETO → marcar problema
                // ==============================
                if ($tipoEstado === 'dañado' || $tipoEstado === 'faltante') {

                    $item->estado = 'Observado';
                    $item->save();

                    $items_con_problemas[] = $itemId;
                    $kit_con_problemas = true;
                }

            } // foreach


            // =============================================================
            // 4) SI HUBO PROBLEMAS → crear UN SOLO incidente por préstamo
            // =============================================================
            if (!empty($items_con_problemas)) {

                PrestamoIncidente::create([
                    'prestamo_id' => $prestamo->id,
                    'persona_id'  => $prestamo->persona_id,
                    'user_id'     => auth()->id(),
                    'tipo'        => 'Observaciones',
                    'nota'        => 'Ítems con problemas: '.implode(',',$items_con_problemas)
                ]);
            }

            // ======================================
            // 5) INHABILITAR KIT SI HUBO PROBLEMAS
            // ======================================
            if ($prestamo->kit && $kit_con_problemas) {
                $prestamo->kit->update(['estado' => 'Inhabilitado']);
            }

            // ======================================
            // 6) ACTUALIZAR ESTADO DEL PRÉSTAMO
            // ======================================
            if (empty($items_con_problemas)) {
                $prestamo->estado = 'Completo';
            } else {
                $prestamo->estado = 'Observado';
            }

            $prestamo->save();

        }); // transaction

        return redirect()
            ->route('prestamos.show', $prestamo)
            ->with('status', 'Devolución procesada correctamente.');
    }
}
