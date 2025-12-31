<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumo extends Model
{
    protected $fillable = [
        'item_id',
        'prestamo_id',
        'persona_id',
        'proyecto_id',
        'cantidad_consumida',
        'precio_unitario'

    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
