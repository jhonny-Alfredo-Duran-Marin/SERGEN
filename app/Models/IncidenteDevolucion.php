<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidenteDevolucion extends Model
{
    // Especificamos el nombre exacto de la tabla de tu migración
    protected $table = 'incidente_devolucions';

    protected $fillable = [
        'incident_id',
        'item_id',
        'cantidad_devuelta',
        'resultado',
        'tipo', // Agregado para diferenciar Perdido/Dañado
        'observacion',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function incidente()
    {
        return $this->belongsTo(Incidente::class, 'incident_id');
    }
}
