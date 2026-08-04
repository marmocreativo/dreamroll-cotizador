<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $table = 'medicos';

    protected $fillable = [
        'hospital_id',
        'prefijo',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'especialidad',
        'cedula_profesional',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return collect([$this->prefijo, $this->nombre, $this->apellido])
            ->filter()
            ->implode(' ');
    }
}