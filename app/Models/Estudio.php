<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudio extends Model
{
    protected $table = 'estudios';

    protected $fillable = [
        'nombre',
        'especimen',
        'area_terapeutica',
        'tiempo_respuesta',
        'precio_unitario',
        'activo',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'activo'          => 'boolean',
    ];

    public function cotizacionEstudios(): HasMany
    {
        return $this->hasMany(CotizacionEstudio::class);
    }
}