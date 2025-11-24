<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'categoria_id',
        'medida_id',
        'area_id',
        'codigo',
        'descripcion',
        'fabricante',
        'cantidad',
        'piezas',
        'costo_unitario',
        'estado',
        'tipo',
        'descuento',
        'ubicacion',
        'fecha_registro',
        'imagen_path',
        'imagen_thumb',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function medida()
    {
        return $this->belongsTo(Medida::class);
    }

    public function dotaciones()
    {
        return $this->hasMany(Dotacion::class);
    }
    public function incidentes()
    {
        return $this->hasMany(PrestamoIncidente::class);
    }
    // Helpers útiles
    public function getValorInventarioAttribute()
    {
        return ($this->cantidad + $this->piezas) * (float)$this->costo_unitario;
    }
    public function kits()
    {
        return $this->belongsToMany(KitEmergencia::class, 'item_kit_emergencia')
            ->withPivot(['cantidad'])
            ->withTimestamps();
    }

    // Scopes de filtro
    public function scopeActivos($q)
    {
        return $q->where('estado', 'Activo');
    }
    public function scopeTipo($q, $tipo)
    {
        return $q->where('tipo', $tipo);
    }

    public function scopeSearch($q, $text)
    {
        return $q->where(function ($qq) use ($text) {
            $qq->where('codigo', 'like', "%$text%")
                ->orWhere('descripcion', 'like', "%$text%")
                ->orWhere('fabricante', 'like', "%$text%");
        });
    }

    public function getThumbUrlAttribute()
    {
        return $this->imagen_thumb
            ? Storage::disk('public')->url($this->imagen_thumb)
            : '/img/thumbplaceholder.png';
    }

    public function getImagenUrlAttribute()
    {
        return $this->imagen_path
            ? Storage::disk('public')->url($this->imagen_path)
            : ($this->imagen_thumb
                ? Storage::disk('public')->url($this->imagen_thumb)
                : '/img/placeholder.png');
    }

    public function registrarMovimiento($accion, $cantidad , $nota )
    {
        Movimiento::create([
            'item_id'   => $this->id,
            'accion'    => $accion,
            'cantidad'  => $cantidad,
            'fecha'     => now(),
            'user_id'   => auth()->id(),
            'nota'      => $nota
        ]);
    }
}
