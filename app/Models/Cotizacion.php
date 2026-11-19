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
        'folio_empresa',
        'cliente_id',
        'created_by',
        'firmante_id',
        'origen',
        'moneda',
        'tipo',
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

            if (empty($cotizacion->folio_empresa)) {
                $cotizacion->folio_empresa = static::generarFolioEmpresa($cotizacion->origen ?: 'dreamroll');
            }
        });

        // Si cambia la empresa de origen, pasa a la numeración de la nueva empresa.
        static::updating(function (Cotizacion $cotizacion) {
            if ($cotizacion->isDirty('origen')) {
                $cotizacion->folio_empresa = static::generarFolioEmpresa($cotizacion->origen ?: 'dreamroll');
            }
        });
    }

    /** Prefijo del folio de empresa según el origen. */
    public static function prefijoEmpresa(string $origen): string
    {
        return $origen === 'latimer' ? 'LAT' : 'DR';
    }

    /**
     * Folio con numeración independiente por empresa y por año.
     * Ej.: DR-2026-0001, LAT-2026-0001.
     */
    public static function generarFolioEmpresa(string $origen, ?string $año = null): string
    {
        $año ??= now()->format('Y');
        $prefijo = static::prefijoEmpresa($origen) . "-{$año}-";

        // Toma el mayor consecutivo existente (no count(), para no repetir
        // folios si se elimina alguna cotización).
        $ultimo = static::where('folio_empresa', 'like', $prefijo . '%')
            ->orderByRaw('LENGTH(folio_empresa) DESC')
            ->orderByDesc('folio_empresa')
            ->value('folio_empresa');

        $siguiente = $ultimo ? ((int) substr($ultimo, strlen($prefijo))) + 1 : 1;

        return $prefijo . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    /** Folio a mostrar en primer plano: el de empresa, o el global si aún no tiene. */
    public function getFolioPrincipalAttribute(): string
    {
        return $this->folio_empresa ?: $this->folio;
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

    public function hospedajes(): HasMany
    {
        return $this->hasMany(CotizacionHospedaje::class);
    }

    public function getClienteNombreCompletoAttribute(): string
    {
        return collect([$this->cliente_prefijo, $this->cliente_nombre, $this->cliente_apellidos])
            ->filter()
            ->implode(' ');
    }

    public function recalcular(): void
    {
        if ($this->tipo === 'basico') {
            $this->recalcularBasico();
            return;
        }

        $subtotalProductos = $this->productos->sum('subtotal');
        $totalHospedajes   = $this->hospedajes->sum('total'); // ya incluye ISH+IVA+resort fee+bell boys+camaristas

        $ivaProductos = $subtotalProductos * (self::IVA_PORCENTAJE / 100);

        $baseFee    = $subtotalProductos + $ivaProductos + $totalHospedajes;
        $feeAgencia = $baseFee * (($this->fee_porcentaje ?? 0) / 100);

        $baseConFee = $baseFee + $feeAgencia;
        $descuento  = $baseConFee * ($this->descuento / 100);
        $total      = $baseConFee - $descuento;

        $this->update([
            'subtotal'    => $subtotalProductos + $totalHospedajes,
            'fee_agencia' => $feeAgencia,
            'iva'         => $ivaProductos,
            'total'       => $total,
        ]);
    }

    /**
     * Cotización básica: Fee sobre subtotal, IVA sobre (subtotal + fee),
     * total = fee + subtotal + iva. No aplica hospedajes ni descuento.
     */
    private function recalcularBasico(): void
    {
        $subtotal   = $this->productos->sum('subtotal');
        $feeAgencia = $subtotal * (($this->fee_porcentaje ?? 0) / 100);

        $base      = $subtotal + $feeAgencia;
        $descuento = $base * (($this->descuento ?? 0) / 100);
        $baseConDescuento = $base - $descuento;

        $iva   = $baseConDescuento * (self::IVA_PORCENTAJE / 100);
        $total = $baseConDescuento + $iva;

        $this->update([
            'subtotal'    => $subtotal,
            'fee_agencia' => $feeAgencia,
            'iva'         => $iva,
            'total'       => $total,
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