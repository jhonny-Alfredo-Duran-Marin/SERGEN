<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitPrestamo extends Model
{
    protected $fillable = [
        'prestamo_id',
        'kit_id',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function kit()
    {
        return $this->belongsTo(KitEmergencia::class);
    }
}
