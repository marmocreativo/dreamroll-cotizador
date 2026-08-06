<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriaEvento extends Model
{
    protected $table = 'galeria_eventos';

    protected $fillable = [
        'evento_id',
        'imagen',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }
}