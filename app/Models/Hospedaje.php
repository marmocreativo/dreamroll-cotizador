<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospedaje extends Model
{
    protected $table = 'hospedajes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_unitario',
        'activo',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'activo'         => 'boolean',
    ];

    public function cotizacionHospedajes(): HasMany
    {
        return $this->hasMany(CotizacionHospedaje::class);
    }
}