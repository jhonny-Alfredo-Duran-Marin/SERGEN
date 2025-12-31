<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = ['codigo', 'fecha', 'estado', 'tipo_destino', 'persona_id', 'proyecto_id', 'user_id', 'nota'];
    protected $casts = ['fecha' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    // Relaciones

    public function detalles()
    {
        return $this->hasMany(DetallePrestamo::class);
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function kits()
    {
        return $this->belongsToMany(
            KitEmergencia::class,
            'kit_prestamos',
            'prestamo_id',
            'kit_id'
        );
    }
    public function kitPrestamos()
    {
        return $this->hasMany(KitPrestamo::class);
    }
    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }

    public static function generateCode(): string
    {
        $year = now()->format('Y');
        $last = self::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();

        $number = $last ? ((int)substr($last->codigo, -4)) + 1 : 1;

        return 'PRES-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }


    public function tieneObservaciones(): bool
    {
        return $this->devoluciones()
            ->whereNotNull('nota')
            ->where('nota', '!=', '')
            ->exists();
    }
    public function tieneDanosOPerdidas(): bool
    {
        // Definimos los estados que consideramos como "problema"
        $estadosCriticos = ['Dañado', 'Perdido'];

        // 1. Verificar en detalle_devoluciones (Ítems sueltos)
        $existeEnDetalles = $this->devoluciones()
            ->whereHas('detalles', function ($query) use ($estadosCriticos) {
                $query->whereIn('estado', $estadosCriticos);
            })->exists();

        if ($existeEnDetalles) {
            return true;
        }
        // 2. Verificar en detalle_devoluciones_kit (Ítems dentro de kits)
        $existeEnKits = $this->devoluciones()
            ->whereHas('detallesKit', function ($query) use ($estadosCriticos) {
                $query->whereIn('estado', $estadosCriticos);
            })->exists();

        return $existeEnKits;
    }

    public function obtenerSaldosPendientes()
    {
        // 1. Cargar relaciones si no están cargadas
        $this->loadMissing(['detalles.item', 'kits.items', 'devoluciones.detalles', 'devoluciones.detallesKit']);

        // 2. Agrupar totales devueltos de ítems sueltos
        $totalesSueltos = $this->devoluciones
            ->where('estado', '!=', 'Anulada')
            ->flatMap->detalles
            ->whereIn('estado', ['OK', 'Dañado', 'Perdido', 'Consumido'])
            ->groupBy('item_id')
            ->map(fn($group) => $group->sum('cantidad'));

        // 3. Agrupar totales devueltos de ítems en kits
        $totalesKits = $this->devoluciones
            ->where('estado', '!=', 'Anulada')
            ->flatMap->detallesKit
            ->whereIn('estado', ['OK', 'Dañado', 'Perdido', 'Consumido'])
            ->groupBy(fn($row) => "{$row->kit_id}-{$row->item_id}")
            ->map(fn($group) => $group->sum('cantidad'));

        // 4. Calcular pendientes de ítems sueltos
        $pendientesSueltos = $this->detalles->map(function ($detalle) use ($totalesSueltos) {
            $devuelto = $totalesSueltos[$detalle->item_id] ?? 0;
            return (object)[
                'item_id'   => $detalle->item_id,
                'prestado'  => $detalle->cantidad_prestada,
                'devuelto'  => $devuelto,
                'pendiente' => max(0, $detalle->cantidad_prestada - $devuelto)
            ];
        })->filter(fn($item) => $item->pendiente > 0);

        // 5. Calcular pendientes de ítems en kits
        $pendientesKits = $this->kits->flatMap(function ($kit) use ($totalesKits) {
            return $kit->items->map(function ($item) use ($kit, $totalesKits) {
                $key = "{$kit->id}-{$item->id}";
                $devuelto = $totalesKits[$key] ?? 0;
                $prestado = $item->pivot->cantidad;
                return (object)[
                    'kit_id'    => $kit->id,
                    'item_id'   => $item->id,
                    'prestado'  => $prestado,
                    'devuelto'  => $devuelto,
                    'pendiente' => max(0, $prestado - $devuelto)
                ];
            });
        })->filter(fn($item) => $item->pendiente > 0);

        return [
            'sueltos' => $pendientesSueltos,
            'kits'    => $pendientesKits,
            'tiene_pendientes' => $pendientesSueltos->isNotEmpty() || $pendientesKits->isNotEmpty()
        ];
    }

    /**
     * Método simple para usar en el IncidenteController
     */
    public function verificarYCompletarEstado(): void
    {

        $saldos = $this->obtenerSaldosPendientes();

        if (!$saldos['tiene_pendientes']) {

            $this->update(['estado' => 'Completo']);
        }
    }
}
