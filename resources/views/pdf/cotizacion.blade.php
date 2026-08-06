<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 24px; }
        .header { margin-bottom: 24px;}
        .fecha { text-align: right; color: #666; font-size: 11px; }
        .header-logo { margin-bottom: 4px; }
        .destinatario { margin-bottom: 20px; }
        .destinatario strong { display: block; font-size: 13px; color: #1b2d4f; text-transform: uppercase; }
        .intro { margin-bottom: 20px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background-color: #1b2d4f; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 8px 10px; }
        .text-right { text-align: right; }
        .totales { width: 260px; margin-left: auto; margin-top: 8px; }
        .totales td { padding: 4px 8px; font-size: 12px; }
        .totales .total-row { font-weight: bold; font-size: 13px; border-top: 2px solid #f5a623; color: #1b2d4f; }
        .notas { margin-top: 20px; padding: 10px; background: #f9fafb; border-left: 3px solid #f5a623; font-size: 11px; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_principal.png'))) }}" 
                style="height: 60px; width: auto;" />
        </div>
    </div>

    <div class="destinatario">
        <div style="display: flex; justify-content: space-between; align-items: baseline;">
            <strong>{{ $cotizacion->cliente_nombre_completo }}</strong>
            <span style="font-size: 11px; color: #666;">Ciudad de México a {{ now()->isoFormat('D [de] MMMM [de] YYYY') }}</span>
        </div>
        {{ $cotizacion->cliente_empresa ?? '' }}<br>
        {{ $cotizacion->cliente_telefono ?? '' }}
        @if($cotizacion->cliente_telefono && $cotizacion->cliente_email) &middot; @endif
        {{ $cotizacion->cliente_email ?? '' }}
    </div>

    <p class="intro">
        De acuerdo con su amable solicitud hacemos llegar nuestra propuesta para los productos requeridos.
    </p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->productos as $renglon)
            <tr>
                <td>
                    <strong>{{ $renglon->producto?->nombre ?? '—' }}</strong>
                </td>
                <td class="text-right">{{ $renglon->cantidad }}</td>
                <td class="text-right">${{ number_format($renglon->precio_unitario, 2) }} MXN</td>
                <td class="text-right">${{ number_format($renglon->subtotal, 2) }} MXN</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">${{ number_format($cotizacion->subtotal, 2) }}</td>
        </tr>
        @if ($cotizacion->descuento > 0)
        <tr>
            <td>Descuento ({{ $cotizacion->descuento }}%)</td>
            <td class="text-right">-${{ number_format($cotizacion->subtotal * ($cotizacion->descuento / 100), 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>IVA (16%)</td>
            <td class="text-right">${{ number_format($cotizacion->iva, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td class="text-right">${{ number_format($cotizacion->total, 2) }} MXN</td>
        </tr>
    </table>

    @if ($cotizacion->tiempo_entrega || $cotizacion->condiciones || $cotizacion->notas)
    <div class="notas">
        @if ($cotizacion->tiempo_entrega)
            <strong>Tiempo de entrega:</strong> {{ $cotizacion->tiempo_entrega }}<br>
        @endif
        @if ($cotizacion->condiciones)
            <strong>Condiciones:</strong><br>
            {{ $cotizacion->condiciones }}<br>
        @endif
        El importe final ya incluye IVA.
    </div>
    @endif

    @if ($cotizacion->valida_hasta)
    <p style="font-size:11px; color:#666; margin-top:12px;">
        Cotización válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}.
    </p>
    @endif

    <div class="footer">
        Agradecemos su interés en nuestra propuesta. Si tiene alguna pregunta o necesita más información,
        no dude en ponerse en contacto con nosotros.<br><br>
        Cordialmente<br>
        <strong>Dream Roll</strong>
    </div>

</body>
</html>