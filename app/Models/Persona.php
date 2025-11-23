<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $fillable = ['nombre', 'cargo', 'celular', 'estado'];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'persona_id');
    }
    public function dotaciones()
    {
        return $this->hasMany(Dotacion::class);
    }
    public function incidentes()
    {
        return $this->hasMany(PrestamoIncidente::class);
    }
    public function proyectospro()
    {
        return $this->hasMany(Proyecto::class);
    }
}
