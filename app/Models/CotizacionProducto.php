<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionProducto extends Model
{
    protected $table = 'cotizacion_productos';

    public $timestamps = false;

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'costo',
        'aumento_porcentaje',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'notas',
    ];

    protected $casts = [
        'costo'              => 'decimal:2',
        'aumento_porcentaje' => 'decimal:2',
        'precio_unitario'    => 'decimal:2',
        'subtotal'           => 'decimal:2',
        'cantidad'           => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (CotizacionProducto $item) {
            $item->subtotal = $item->cantidad * $item->precio_unitario;
        });

        static::saved(function (CotizacionProducto $item) {
            $item->cotizacion->recalcular();
        });

        static::deleted(function (CotizacionProducto $item) {
            $item->cotizacion->recalcular();
        });
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}