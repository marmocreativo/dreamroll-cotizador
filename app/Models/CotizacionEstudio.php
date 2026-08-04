<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionEstudio extends Model
{
    protected $table = 'cotizacion_estudios';

    public $timestamps = false;

    protected $fillable = [
        'cotizacion_id',
        'estudio_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'notas',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'cantidad'        => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (CotizacionEstudio $item) {
            $item->subtotal = $item->cantidad * $item->precio_unitario;
        });

        static::saved(function (CotizacionEstudio $item) {
            $item->cotizacion->recalcular();
        });

        static::deleted(function (CotizacionEstudio $item) {
            $item->cotizacion->recalcular();
        });
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function estudio(): BelongsTo
    {
        return $this->belongsTo(Estudio::class);
    }
}