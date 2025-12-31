<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucionKit extends Model
{
     protected $table = 'detalle_devoluciones_kit';
    protected $fillable = ['devolucion_id','estado', 'item_id', 'kit_id','cantidad'];
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
     public function kit()
    {
        return $this->belongsTo(KitEmergencia::class);
    }
}
