<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 32px; }
        h1 { color: #083081; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #083081; color: #fff; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        .total { font-weight: bold; font-size: 15px; color: #083081; }
        .footer { margin-top: 32px; font-size: 12px; color: #888; border-top: 2px solid #ff9f12; padding-top: 16px; }
    </style>
</head>
<body>
    <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('logo_latimer.png'))) }}"
        style="height: 45px; width: auto; margin-bottom: 16px;" /><br>
    <h1>Cotización {{ $cotizacion->folio }}</h1>

    <p>Estimado(a) {{ $cotizacion->cliente_nombre_completo }},</p>
    <p>A continuación presentamos nuestra propuesta económica conforme a su solicitud.</p>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;"></th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->productos as $renglon)
            <tr>
                <td>
                    @if ($renglon->producto?->imagen_url)
                        <img src="{{ $renglon->producto->imagen_url }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" />
                    @endif
                </td>
                <td>{{ $renglon->producto?->nombre ?? '—' }}</td>
                <td>{{ $renglon->cantidad }}</td>
                <td>${{ number_format($renglon->precio_unitario, 2) }}</td>
                <td>${{ number_format($renglon->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

     @if ($cotizacion->fee_porcentaje > 0)
    <p>FEE DE AGENCIA ({{ number_format($cotizacion->fee_porcentaje, 2) }}%): ${{ number_format($cotizacion->fee_agencia, 2) }}</p>
    @endif
    <p>SUBTOTAL: ${{ number_format($cotizacion->subtotal, 2) }}</p>
    @if ($cotizacion->descuento > 0)
    <p>DESCUENTO ({{ $cotizacion->descuento }}%): -${{ number_format(($cotizacion->subtotal + $cotizacion->fee_agencia) * ($cotizacion->descuento / 100), 2) }}</p>
    @endif
    <p>IVA (16%): ${{ number_format($cotizacion->iva, 2) }}</p>
    <p class="total">TOTAL: ${{ number_format($cotizacion->total, 2) }}</p>

    @if ($cotizacion->tiempo_entrega)
    <p>Tiempo de entrega: {{ $cotizacion->tiempo_entrega }}</p>
    @endif

    @if ($cotizacion->valida_hasta)
    <p>Cotización válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}.</p>
    @endif

    <div class="footer">
        Agradecemos su interés. Si tiene alguna pregunta, no dude en contactarnos.<br>
        <strong>Latimer Publicidad y Medios</strong>
    </div>
</body>
</html>