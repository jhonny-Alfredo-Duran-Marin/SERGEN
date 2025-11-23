<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $fillable = ['descripcion','estado'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
