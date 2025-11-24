<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dotacion extends Model
{
    protected $fillable = [
        'persona_id',
        'fecha',
        'nota',
        'impreso',
        'estado_final'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // UNA dotación → muchos items entregados
    public function items()
    {
        return $this->hasMany(DotacionItem::class);
    }
}
