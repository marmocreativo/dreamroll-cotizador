<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionHospedaje extends Model
{
    protected $table = 'cotizacion_hospedajes';

    protected $fillable = [
        'cotizacion_id',
        'hospedaje_id',
        'nombre',
        'checkin',
        'checkout',
        'noches',
        'habitaciones',
        'costo_unitario',
        'subtotal',
        'ish_porcentaje',
        'iva_porcentaje',
        'resort_fee',
        'bell_boys',
        'camaristas',
        'cargos_adicionales',
        'total',
        'notas',
    ];

    protected $casts = [
        'noches'              => 'integer',
        'habitaciones'        => 'integer',
        'costo_unitario'      => 'decimal:2',
        'subtotal'            => 'decimal:2',
        'ish_porcentaje'      => 'decimal:2',
        'iva_porcentaje'      => 'decimal:2',
        'resort_fee'          => 'decimal:2',
        'bell_boys'           => 'decimal:2',
        'camaristas'          => 'decimal:2',
        'cargos_adicionales'  => 'array',
        'total'               => 'decimal:2',
    ];

    /**
     * NOTA: este modelo ya NO recalcula automáticamente la cotización al
     * guardarse/eliminarse (se quitó por rendimiento en imports masivos).
     * Quien cree/edite/borre estos registros debe llamar
     * $cotizacion->recalcular() manualmente al terminar.
     */
    protected static function booted(): void
    {
        static::saving(function (CotizacionHospedaje $item) {
            $item->subtotal = $item->costo_unitario * $item->noches * $item->habitaciones;

            $ish       = $item->subtotal * (($item->ish_porcentaje ?? 0) / 100);
            $iva       = $item->subtotal * (($item->iva_porcentaje ?? 0) / 100);
            $resortFee = ($item->resort_fee ?? 0) * $item->habitaciones * $item->noches;
            $bellBoys  = ($item->bell_boys ?? 0) * $item->habitaciones;
            $camaristas = ($item->camaristas ?? 0) * $item->habitaciones * $item->noches;

            $item->total = $item->subtotal + $ish + $iva + $resortFee + $bellBoys + $camaristas;
        });

    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function hospedaje(): BelongsTo
    {
        return $this->belongsTo(Hospedaje::class);
    }
}