<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestamoIncidente extends Model
{
    protected $fillable = [
        'prestamo_id','item_id','persona_id','user_id','tipo','nota'
    ];

    public function prestamo() { return $this->belongsTo(Prestamo::class); }
    public function item()     { return $this->belongsTo(Item::class); }
    public function persona()  { return $this->belongsTo(Persona::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
