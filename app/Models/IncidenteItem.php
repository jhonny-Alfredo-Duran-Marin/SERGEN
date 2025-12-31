<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidenteItem extends Model
{
    protected $table = 'incidente_items';

    protected $fillable = [
        'incidente_id',
        'item_id',
        'estado_item',
        'cantidad',
    ];

    /* =========================
       RELACIONES
    ========================= */

    public function incidente()
    {
        return $this->belongsTo(Incidente::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
