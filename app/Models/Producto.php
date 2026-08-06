<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_unitario',
        'imagen',
        'activo',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'activo'          => 'boolean',
    ];

    public function cotizacionProductos(): HasMany
    {
        return $this->hasMany(CotizacionProducto::class);
    }
}