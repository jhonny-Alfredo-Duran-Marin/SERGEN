<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dotacion extends Model
{
     protected $table = 'dotacions';
    protected $fillable = ['item_id', 'persona_id', 'cantidad', 'fecha'];
    protected $casts = ['fecha' => 'date'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
