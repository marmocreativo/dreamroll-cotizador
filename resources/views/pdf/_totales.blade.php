{{-- Desglose de totales compartido por los PDFs (Dream Roll y Latimer). --}}
<table class="totales">
    @foreach ($cotizacion->desgloseTotales() as $renglon)
        <tr @if (!empty($renglon['destacado'])) style="font-weight: bold; border-top: 1px solid #e5e7eb;" @endif>
            <td>{{ mb_strtoupper($renglon['etiqueta']) }}</td>
            <td class="text-right">{{ !empty($renglon['negativo']) ? '-' : '' }}${{ number_format($renglon['monto'], 2) }}</td>
        </tr>
    @endforeach
    <tr class="total-row">
        <td>TOTAL ({{ $cotizacion->moneda ?? 'MXN' }})</td>
        <td class="text-right">${{ number_format($cotizacion->total, 2) }}</td>
    </tr>
</table>
