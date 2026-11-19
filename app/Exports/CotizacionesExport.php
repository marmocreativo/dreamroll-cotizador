<?php

namespace App\Exports;

use App\Models\Cotizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CotizacionesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $filtros = []) {}

    public function collection()
    {
        return Cotizacion::when($this->filtros['busqueda'] ?? null, fn($q, $v) =>
                $q->where(fn($w) => $w
                    ->where('folio_empresa', 'like', "%{$v}%")
                    ->orWhere('folio', 'like', "%{$v}%")
                    ->orWhere('cliente_nombre', 'like', "%{$v}%")
                    ->orWhere('cliente_apellidos', 'like', "%{$v}%")
                    ->orWhere('cliente_empresa', 'like', "%{$v}%")
                )
            )
            ->when($this->filtros['estado'] ?? null, fn($q, $v) =>
                $q->where('estado', $v)
            )
            ->when(in_array($this->filtros['origen'] ?? null, ['dreamroll', 'latimer'], true), fn($q) =>
                $q->where('origen', $this->filtros['origen'])
            )
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Folio Global',
            'Empresa emisora',
            'Cliente',
            'Empresa',
            'Teléfono',
            'Email',
            'Total',
            'Estado',
            'Fecha de creación',
        ];
    }

    public function map($cotizacion): array
    {
        return [
            $cotizacion->folio_empresa,
            $cotizacion->folio,
            $cotizacion->origen === 'latimer' ? 'Latimer' : 'Dream Roll',
            $cotizacion->cliente_nombre_completo,
            $cotizacion->cliente_empresa,
            $cotizacion->cliente_telefono,
            $cotizacion->cliente_email,
            $cotizacion->total,
            ucfirst($cotizacion->estado),
            $cotizacion->created_at->format('d/m/Y H:i'),
        ];
    }
}