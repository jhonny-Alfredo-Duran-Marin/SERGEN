<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medida extends Model
{
   use SoftDeletes;

    protected $fillable = ['descripcion','simbolo'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
