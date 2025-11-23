<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyecto extends Model
{
    use SoftDeletes;

      protected $fillable = [
        'codigo','descripcion','empresa','orden_compra','sitio',
        'monto','es_facturado','estado','fecha_inicio','fecha_fin',
        'persona_id'
    ];

    protected $casts = [
        'es_facturado' => 'boolean',
        'monto'        => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

     public function persona()  { return $this->belongsTo(Persona::class); }
}

