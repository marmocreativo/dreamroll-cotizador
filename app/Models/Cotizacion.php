<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'folio',
        'medico_id',
        'hospital_id',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'notas',
        'valida_hasta',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'descuento'    => 'decimal:2',
        'total'        => 'decimal:2',
        'valida_hasta' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cotizacion $cotizacion) {
            if (empty($cotizacion->folio)) {
                $cotizacion->folio = static::generarFolio();
            }
        });
    }

    protected static function generarFolio(): string
    {
        $año = now()->format('Y');
        $ultimo = static::whereYear('created_at', $año)->lockForUpdate()->count();
        $consecutivo = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);

        return "COT-{$año}-{$consecutivo}";
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function estudios(): HasMany
    {
        return $this->hasMany(CotizacionEstudio::class);
    }

    public function recalcular(): void
    {
        $subtotal = $this->estudios->sum('subtotal');
        $descuento = $subtotal * ($this->descuento / 100);

        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $descuento,
        ]);
    }
}