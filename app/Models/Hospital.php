<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    protected $table = 'hospitales';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'procedencia',
        'direccion',
        'ciudad',
        'estado',
        'telefono',
        'email',
        'notas',
    ];

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }
}