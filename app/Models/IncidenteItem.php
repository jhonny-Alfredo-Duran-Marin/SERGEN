<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidenteItem extends Model
{
    protected $fillable = [
        'incidente_id',
        'item_id',
        'prestamo_id',
        'dotacion_id',
        'estado_item',
        'cantidad',
        'observacion'
    ];


    public function incidente()
    {
        return $this->belongsTo(Incidente::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
