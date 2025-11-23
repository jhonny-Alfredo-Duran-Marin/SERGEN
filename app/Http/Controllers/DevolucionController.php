<?php

// app/Http/Controllers/DevolucionController.php
namespace App\Http\Controllers;

use App\Models\{Prestamo, DetallePrestamo, Devolucion, DetalleDevolucion, Item, Movimiento};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DevolucionController extends Controller
{
    public function create(Prestamo $prestamo)
    {
        $prestamo->load(['detalles.item.medida', 'kit']);

        $pendientes = $prestamo->detalles->map(function ($d) {
            $d->pendiente = $d->cantidad_prestada - $d->cantidad_devuelta;
            return $d;
        })->filter(fn($d) => $d->pendiente > 0);

        return view('devoluciones.create', compact('prestamo', 'pendientes'));
    }

    public function store(Request $request, Prestamo $prestamo)
    {
        // Cargamos detalles + items para validar pertenencia y pendientes
        $prestamo->load('detalles.item');

        $data = $request->validate([
            'nota'          => ['nullable', 'string'],
            'items'         => ['required', 'array', 'min:1'],
            'items.*'       => ['integer'],
            'cantidades'    => ['required', 'array', 'min:1'],
            'cantidades.*'  => ['integer', 'min:1'],
        ]);

        // Aseguramos correspondencia items[] ↔ cantidades[]
        if (count($data['items']) !== count($data['cantidades'])) {
            throw ValidationException::withMessages([
                'cantidades' => 'El número de cantidades no coincide con los ítems seleccionados.',
            ]);
        }

        DB::transaction(function () use ($prestamo, $data) {
            // Crear cabecera de devolución
            $dev = Devolucion::create([
                'prestamo_id' => $prestamo->id,
                'estado'      => 'Pendiente',
                'fecha'       => now(),
                'user_id'     => auth()->id(),
                'nota'        => $data['nota'] ?? null,
            ]);

            // Mapa rápido de detalles por item_id
            $detallesByItem = $prestamo->detalles->keyBy('item_id');

            foreach ($data['items'] as $i => $itemId) {
                $itemId = (int) $itemId;
                $cant   = (int) ($data['cantidades'][$i] ?? 0);

                /** @var DetallePrestamo|null $detalle */
                $detalle = $detallesByItem->get($itemId);
                if (!$detalle) {
                    throw ValidationException::withMessages([
                        "items.$i" => "El ítem seleccionado no pertenece a este préstamo.",
                    ]);
                }

                // Bloqueamos el ítem para evitar condiciones de carrera en stock
                /** @var Item $item */
                $item = Item::whereKey($itemId)->lockForUpdate()->firstOrFail();

                // Validar pendiente
                $pendiente = $detalle->cantidad_prestada - $detalle->cantidad_devuelta;
                if ($pendiente <= 0) {
                    throw ValidationException::withMessages([
                        "items.$i" => "No hay pendiente para devolver en «{$item->descripcion}».",
                    ]);
                }
                if ($cant > $pendiente) {
                    throw ValidationException::withMessages([
                        "cantidades.$i" => "Cantidad a devolver excede lo pendiente ({$pendiente}).",
                    ]);
                }

                // Crear línea de devolución
                DetalleDevolucion::create([
                    'devolucion_id' => $dev->id,
                    'item_id'       => $item->id,
                    'cantidad'      => $cant,
                ]);

                // Actualizar detalle del préstamo (devuelto)
                $detalle->increment('cantidad_devuelta', $cant);

                // Devolver al stock
                $item->increment('cantidad', $cant);

                // Registrar movimiento
                Movimiento::create([
                    'item_id'       => $item->id,
                    'tipo'          => 'Ingreso',
                    'cantidad'      => $cant,
                    'fecha'         => now(),
                    'user_id'       => auth()->id(),
                    'prestamo_id'   => $prestamo->id,
                    'devolucion_id' => $dev->id,
                    'nota'          => 'Devolución préstamo ' . $prestamo->codigo,
                ]);
            }

            // Recalcular estado del préstamo y de la devolución
            $prestamo->load('detalles');
            $completo = $prestamo->detalles->every(
                fn($d) => $d->cantidad_devuelta >= $d->cantidad_prestada
            );

            $prestamo->update(['estado' => $completo ? 'Completo' : 'Activo']);
            $dev->update(['estado' => $completo ? 'Completa' : 'Parcial']);
        });

        return redirect()
            ->route('prestamos.show', $prestamo)
            ->with('status', 'Devolución registrada.');
    }
    // Agregá este método en DevolucionController
    public function devolverKitCompleto(Prestamo $prestamo)
    {
        $prestamo->load('detalles.item');

        DB::transaction(function () use ($prestamo) {
            $devolucion = Devolucion::create([
                'prestamo_id' => $prestamo->id,
                'fecha'       => now(),
                'user_id'     => auth()->id(),
                'nota'        => 'KIT devuelto completo: ' . $prestamo->kit->nombre,
                'estado'      => 'Completa',
            ]);

            foreach ($prestamo->detalles as $detalle) {
                $pendiente = $detalle->cantidad_prestada - $detalle->cantidad_devuelta;
                if ($pendiente <= 0) continue;

                DetalleDevolucion::create([
                    'devolucion_id' => $devolucion->id,
                    'item_id'       => $detalle->item_id,
                    'cantidad'      => $pendiente,
                ]);

                $detalle->increment('cantidad_devuelta', $pendiente);
                $detalle->item->increment('cantidad', $pendiente);

                Movimiento::create([
                    'item_id'       => $detalle->item_id,
                    'tipo'          => 'Ingreso',
                    'cantidad'      => $pendiente,
                    'fecha'         => now(),
                    'user_id'       => auth()->id(),
                    'prestamo_id'   => $prestamo->id,
                    'devolucion_id' => $devolucion->id,
                    'nota'          => 'KIT completo - ' . $prestamo->codigo,
                ]);
            }

            $prestamo->update(['estado' => 'Completo']);
        });

        return redirect()
            ->route('prestamos.show', $prestamo)
            ->with('status', '¡KIT devuelto COMPLETO! Todo en orden.');
    }
}
