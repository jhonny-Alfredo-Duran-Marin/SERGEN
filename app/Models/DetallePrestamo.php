<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    protected $fillable = ['prestamo_id', 'item_id', 'cantidad_prestada', 'cantidad_devuelta', 'costo_unitario', 'subtotal'];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
