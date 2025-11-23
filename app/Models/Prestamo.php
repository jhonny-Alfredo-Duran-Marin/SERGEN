<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = ['codigo', 'fecha', 'estado', 'tipo_destino', 'persona_id', 'proyecto_id','kit_emergencia_id', 'user_id', 'nota'];
    protected $casts = ['fecha' => 'date'];

    public function detalles()
    {
        return $this->hasMany(DetallePrestamo::class);
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function kit()
    {
        return $this->belongsTo(KitEmergencia::class, 'kit_emergencia_id');
    }
    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class);
    }
    public function incidentes()
    {
        return $this->hasMany(PrestamoIncidente::class);
    }

    public function getEsCompletoAttribute(): bool
    {
        return $this->detalles->every(
            fn($d) => $d->cantidad_devuelta >= $d->cantidad_prestada
        );
    }
    public static function generateCode(): string
    {
        $year = now()->format('Y');
        $last = self::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();

        $number = $last ? ((int)substr($last->codigo, -4)) + 1 : 1;

        return 'PRES-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
