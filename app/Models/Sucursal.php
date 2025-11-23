<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
     use SoftDeletes;
      protected $table = 'sucursal';

    protected $fillable = ['descripcion','estado'];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
