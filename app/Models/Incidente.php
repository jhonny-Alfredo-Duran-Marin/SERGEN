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
        'descripcion'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'incidente_items')
            ->withPivot([
                'prestamo_id',
                'dotacion_id',
                'estado_item',
                'cantidad',
                'observacion'
            ])
            ->withTimestamps();
    }

    public function devoluciones()
    {
        return $this->hasMany(IncidenteDevolucion::class, 'incident_id');
    }

    public static function generarCodigo()
    {
        $next = self::max('id') + 1;
        return 'INC-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
