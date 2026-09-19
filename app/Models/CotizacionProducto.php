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
        'grupo',
        'dia',
        'horario',
        'lugar',
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

    /**
     * NOTA: este modelo ya NO recalcula automáticamente la cotización al
     * guardarse/eliminarse (se quitó por rendimiento en imports masivos).
     * Quien cree/edite/borre estos registros debe llamar
     * $cotizacion->recalcular() manualmente al terminar.
     */
    protected static function booted(): void
    {
        static::saving(function (CotizacionProducto $item) {
            $item->subtotal = $item->cantidad * $item->precio_unitario;
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