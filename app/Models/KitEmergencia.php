<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitEmergencia extends Model
{
    use SoftDeletes;

    // Incluye 'codigo' en fillable (no hace daño aunque se autogenere)
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    public function items()
    {
        // Usa SIEMPRE el mismo nombre de pivot que en el controlador: 'kit_emergencia_item'
        return $this->belongsToMany(Item::class, 'kit_emergencia_item')
            ->withPivot(['cantidad'])
            ->withTimestamps();
    }
    public function items_kit()
    {
        // Esta es la relación que realmente contiene los productos
        return $this->belongsToMany(Item::class, 'kit_emergencia_item', 'kit_emergencia_id', 'item_id')
            ->withPivot('cantidad');
    }

    // Autogenera 'codigo' antes de insertar (KIT-0001, KIT-0002, ...)


    // (Opcional) total de cantidades del kit
    public function getTotalCantidadAttribute(): int
    {
        return (int) $this->items->sum('pivot.cantidad');
    }

    // (Opcional) scope de búsqueda
    protected static function booted()
    {
        // generar después de insertar (ya tenemos $kit->id)
        static::created(function (self $kit) {
            if (empty($kit->codigo)) {
                $kit->codigo = 'KIT-' . str_pad((string)$kit->id, 4, '0', STR_PAD_LEFT);
                // saveQuietly para no volver a disparar eventos
                $kit->saveQuietly();
            }
        });
    }
    public function scopeSearch($q, $text)
    {
        if ($text === '' || $text === null) return $q;
        return $q->where(function ($qq) use ($text) {
            $qq->where('codigo', 'like', "%{$text}%")
                ->orWhere('nombre', 'like', "%{$text}%")
                ->orWhere('descripcion', 'like', "%{$text}%");
        });
    }
}
