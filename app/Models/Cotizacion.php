<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'folio',
        'cliente_id',
        'created_by',
        'firmante_id',
        'origen',
        'cliente_prefijo',
        'cliente_nombre',
        'cliente_apellidos',
        'cliente_puesto',
        'cliente_empresa',
        'cliente_telefono',
        'cliente_email',
        'cliente_direccion',
        'estado',
        'subtotal',
        'fee_porcentaje',
        'fee_agencia',
        'descuento',
        'iva',
        'total',
        'tiempo_entrega',
        'condiciones',
        'notas',
        'valida_hasta',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'fee_porcentaje' => 'decimal:2',
        'fee_agencia'    => 'decimal:2',
        'descuento'      => 'decimal:2',
        'iva'            => 'decimal:2',
        'total'          => 'decimal:2',
        'valida_hasta'   => 'date',
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
        $subtotal         = $this->productos->sum('subtotal');
        $feeAgencia       = $subtotal * (($this->fee_porcentaje ?? 0) / 100);
        $baseConFee       = $subtotal + $feeAgencia;
        $descuento        = $baseConFee * ($this->descuento / 100);
        $baseConDescuento = $baseConFee - $descuento;
        $iva              = $baseConDescuento * (self::IVA_PORCENTAJE / 100);

        $this->update([
            'subtotal'    => $subtotal,
            'fee_agencia' => $feeAgencia,
            'iva'         => $iva,
            'total'       => $baseConDescuento + $iva,
        ]);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function firmante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'firmante_id');
    }
}