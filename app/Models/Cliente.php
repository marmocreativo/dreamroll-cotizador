<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'empresa',
        'rfc',
        'direccion_fiscal',
        'regimen_fiscal',
        'uso_cfdi',
        'contacto_prefijo',
        'contacto_nombre',
        'contacto_apellidos',
        'contacto_puesto',
        'contacto_telefono',
        'contacto_email',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function getContactoNombreCompletoAttribute(): string
    {
        return collect([$this->contacto_prefijo, $this->contacto_nombre, $this->contacto_apellidos])
            ->filter()
            ->implode(' ');
    }
}