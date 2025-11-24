<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
        'item_id',
        'accion',
        'tipo',
        'cantidad',
        'fecha',
        'user_id',
        'prestamo_id',
        'devolucion_id',
        'nota'
    ];

    protected $casts = ['fecha' => 'datetime'];

    // Relaciones
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        // si tu User está en App\Models\User
        return $this->belongsTo(User::class);
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }
}
