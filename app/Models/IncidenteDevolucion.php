<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidenteDevolucion extends Model
{
    protected $fillable = [
        'incident_id',
        'item_id',
        'cantidad_devuelta',
        'resultado',
        'aceptado',
        'observacion',
        'impreso'
    ];

    public function incidente()
    {
        return $this->belongsTo(Incidente::class, 'incident_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
