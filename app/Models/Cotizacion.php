<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'folio',
        'cliente_prefijo',
        'cliente_nombre',
        'cliente_apellidos',
        'cliente_empresa',
        'cliente_telefono',
        'cliente_email',
        'cliente_direccion',
        'estado',
        'subtotal',
        'descuento',
        'iva',
        'total',
        'tiempo_entrega',
        'condiciones',
        'notas',
        'valida_hasta',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'descuento'    => 'decimal:2',
        'iva'          => 'decimal:2',
        'total'        => 'decimal:2',
        'valida_hasta' => 'date',
    ];

    const IVA_PORCENTAJE = 16;

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

    public function productos(): HasMany
    {
        return $this->hasMany(CotizacionProducto::class);
    }

    public function getClienteNombreCompletoAttribute(): string
    {
        return collect([$this->cliente_prefijo, $this->cliente_nombre, $this->cliente_apellidos])
            ->filter()
            ->implode(' ');
    }

    public function recalcular(): void
    {
        $subtotal   = $this->productos->sum('subtotal');
        $descuento  = $subtotal * ($this->descuento / 100);
        $baseConDescuento = $subtotal - $descuento;
        $iva        = $baseConDescuento * (self::IVA_PORCENTAJE / 100);

        $this->update([
            'subtotal' => $subtotal,
            'iva'      => $iva,
            'total'    => $baseConDescuento + $iva,
        ]);
    }
}