<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    protected $fillable = [
        'codigo',
        'tipo',
        'estado',
        'persona_id',
        'fecha_incidente',
        'descripcion',
        'prestamo_id',
        'dotacion_id',
    ];

    /* =========================
       RELACIONES
    ========================= */

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function dotacion()
    {
        return $this->belongsTo(Dotacion::class);
    }




    /* =========================
       HELPERS
    ========================= */

    public static function generarCodigo(): string
    {
        $next = (self::max('id') ?? 0) + 1;
        return 'INC-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
    public function items()
    {
        return $this->belongsToMany(Item::class, 'incidente_items')
            ->withPivot('id', 'cantidad', 'estado_item') // Incluimos el 'id' del pivote
            ->withTimestamps();
    }

    public function devoluciones()
    {
        // Relación con la tabla 'incidente_devolucions'
        return $this->hasMany(IncidenteDevolucion::class, 'incident_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
