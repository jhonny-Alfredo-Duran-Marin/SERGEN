<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
     protected $table = 'detalle_devoluciones';
    protected $fillable = ['devolucion_id','estado', 'item_id', 'cantidad'];
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
