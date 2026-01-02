<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacion extends Model
{
   use SoftDeletes;
  protected $table = 'ubicacion';
    protected $fillable = ['descripcion', 'estado', 'area_id'];
     public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
