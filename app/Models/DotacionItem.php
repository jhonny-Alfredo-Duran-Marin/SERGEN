<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotacionItem extends Model
{
    protected $fillable = [
        'dotacion_id',
        'item_id',
        'cantidad',
        'estado_final',        // BUENO • BAJA • OBSERVADO • NO_DEVUELTO
        'fecha_devolucion',
        'nota_estado',
    ];

    public function dotacion()
    {
        return $this->belongsTo(Dotacion::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
