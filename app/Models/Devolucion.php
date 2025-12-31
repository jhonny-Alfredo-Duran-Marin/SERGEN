<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'prestamo_id',
        'estado',
        'fecha',
        'user_id',
        'nota'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class);
    }


    public function detallesKit()
    {
        return $this->hasMany(DetalleDevolucionKit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEsAnuladaAttribute(): bool
    {
        return $this->estado === 'Anulada';
    }
}
