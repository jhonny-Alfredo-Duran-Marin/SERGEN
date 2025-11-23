<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $fillable = ['descripcion','estado','sucursal_id'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
     public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

}
