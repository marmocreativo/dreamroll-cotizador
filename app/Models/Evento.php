<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha',
        'lugar',
        'cliente',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function galeria(): HasMany
    {
        return $this->hasMany(GaleriaEvento::class)->orderBy('orden');
    }

    public function getPortadaUrlAttribute(): ?string
    {
        $portada = $this->galeria->first();
        return $portada ? asset('storage/' . $portada->imagen) : null;
    }
}