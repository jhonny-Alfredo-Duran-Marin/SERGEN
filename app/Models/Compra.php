<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha_compra',
        'descripcion',
        'costo_total',
        'cantidad',
        'tipo_compra',
        'estado_procesamiento',
        'user_id',
        'imagen',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_compra' => 'date',
        'costo_total' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    /**
     * Obtiene el usuario (empleado) que registró la compra.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
