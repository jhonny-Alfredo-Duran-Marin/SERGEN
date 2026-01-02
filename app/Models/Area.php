<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $fillable = ['descripcion', 'estado', 'sucursal_id'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    // Relación con Ubicación
    public function ubicaciones()
    {
        return $this->hasMany(Ubicacion::class, 'area_id');
    }

    // El "Truco" para que borre las ubicaciones automáticamente
    protected static function booted()
    {
        static::deleted(function ($area) {
            // Al borrar el área, borra (soft delete) sus ubicaciones
            $area->ubicaciones()->delete();
        });

        static::restored(function ($area) {
            // (Opcional) Si restauras el área, restaura sus ubicaciones
            $area->ubicaciones()->restore();
        });
    }
}
