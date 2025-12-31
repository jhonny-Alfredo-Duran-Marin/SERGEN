<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotacionItem extends Model
{
    protected $fillable = [
        'dotacion_id',
        'item_id',
        'cantidad',
        'estado_item',
        'fecha_devolucion',
        'fecha_entrega',
        'fecha_siguiente',
        'observacion',

    ];
    // app/Models/Dotacion.php

    protected $casts = [
        'fecha_entrega' => 'date',
        'fecha_siguiente' => 'date',
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
