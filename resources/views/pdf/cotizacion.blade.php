<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 14px; }
        .header { margin-bottom: 24px; }
        .header-logo { margin-bottom: 4px; }
        .pleca { width: 100%; margin-bottom: 20px; }
        .datos-tabla { width: 100%; margin-bottom: 6px; }
        .datos-tabla td { vertical-align: top; padding: 0; }
        .col-cliente { width: 78%; }
        .col-fecha { width: 22%; text-align: right; }
        .col-fecha .titulo { font-size: 9px; }
        .col-fecha .dato { font-size: 10px; }
        .col-cliente strong.titulo,
        .col-fecha strong.titulo {
            display: block;
            font-size: 11px;
            color: #f5a623;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .col-cliente .empresa { font-size: 14px; font-weight: bold; color: #1b2d4f; }
        .col-cliente .contacto { font-size: 12px; color: #333; }
        .col-cliente .puesto { font-size: 11px; color: #666; }
        .col-fecha .dato { font-size: 12px; color: #333; }
        .intro { margin-top: 4px; margin-bottom: 20px; color: #444; }
        table.productos { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.productos thead tr { background-color: #1b2d4f; color: #fff; }
        table.productos thead th { padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.productos tbody tr { border-bottom: 1px solid #e5e7eb; }
        table.productos tbody td { padding: 8px 10px; }
        .text-right { text-align: right; }
        .totales { width: 360px; margin-left: auto; margin-top: 8px; }
        .totales td { padding: 4px 8px; font-size: 12px; }
        .totales .total-row { font-weight: bold; font-size: 13px; border-top: 2px solid #f5a623; color: #1b2d4f; }
        .condiciones { margin-top: 20px; padding: 10px; background: #f9fafb; border-left: 3px solid #f5a623; font-size: 11px; }
        .condiciones .vigencia { margin-top: 8px; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        .firma { margin-top: 30px; }
        .firma img { height: 60px; width: auto; display: block; margin-bottom: 4px; }
        .firma .nombre { font-size: 12px; font-weight: bold; color: #1b2d4f; }
        .firma .puesto { font-size: 11px; color: #666; }
    </style>
</head>
<body>

    <div class="pleca">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pleca_pdf.png'))) }}"
            style="width: 100%; height: auto; display: block;" />
    </div>

    <table class="datos-tabla">
        <tr>
            <td class="col-cliente">
                <strong class="titulo">Cliente</strong>
                @if ($cotizacion->cliente_empresa)
                    <div class="empresa">{{ $cotizacion->cliente_empresa }}</div>
                @endif
                <div class="contacto">{{ $cotizacion->cliente_nombre_completo }}</div>
                @if ($cotizacion->cliente_puesto)
                    <div class="puesto">{{ $cotizacion->cliente_puesto }}</div>
                @endif
            </td>
            <td class="col-fecha">
                <strong class="titulo">Fecha</strong>
                <div class="dato">{{ now()->isoFormat('D [de] MMMM [de] YYYY') }}</div>
            </td>
        </tr>
    </table>

    <p class="intro">
        En atención a su amable solicitud, presentamos nuestra propuesta económica, queda de la siguiente manera:
    </p>

    <table class="productos">
        <thead>
            <tr>
                <th style="width: 50px;"></th>
                <th>Descripción</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->productos as $renglon)
            @php
                $imagenBase64 = null;
                if ($renglon->producto?->imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($renglon->producto->imagen)) {
                    $imagenBase64 = 'data:image/webp;base64,' . base64_encode(
                        \Illuminate\Support\Facades\Storage::disk('public')->get($renglon->producto->imagen)
                    );
                }
            @endphp
            <tr>
                <td>
                    @if ($imagenBase64)
                        <img src="{{ $imagenBase64 }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" />
                    @endif
                </td>
                <td>
                    <strong>{{ $renglon->producto?->nombre ?? '—' }}</strong>
                </td>
                <td class="text-right">{{ $renglon->cantidad }}</td>
                <td class="text-right">${{ number_format($renglon->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($renglon->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        @if ($cotizacion->fee_porcentaje > 0)
        <tr>
            <td>FEE DE AGENCIA ({{ number_format($cotizacion->fee_porcentaje, 2) }}%)</td>
            <td class="text-right">${{ number_format($cotizacion->fee_agencia, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>SUBTOTAL</td>
            <td class="text-right">${{ number_format($cotizacion->subtotal, 2) }}</td>
        </tr>
        @if ($cotizacion->descuento > 0)
        <tr>
            <td>DESCUENTO ({{ $cotizacion->descuento }}%)</td>
            <td class="text-right">-${{ number_format(($cotizacion->subtotal + $cotizacion->fee_agencia) * ($cotizacion->descuento / 100), 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>IVA (16%)</td>
            <td class="text-right">${{ number_format($cotizacion->iva, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="text-right">${{ number_format($cotizacion->total, 2) }}</td>
        </tr>
    </table>

    @if ($cotizacion->tiempo_entrega || $cotizacion->condiciones || $cotizacion->valida_hasta)
        <div class="condiciones">
            <strong>Bajo las siguientes condiciones:</strong><br>
            @if ($cotizacion->tiempo_entrega)
                Tiempo de entrega: {{ $cotizacion->tiempo_entrega }}<br>
            @endif
            @if ($cotizacion->condiciones)
                {!! $cotizacion->condiciones !!}<br>
            @endif
            El importe final ya incluye IVA.

            @if ($cotizacion->valida_hasta)
                <div class="vigencia">
                    <strong>Vigencia:</strong> Válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}
                </div>
            @endif
        </div>
    @endif

    <div class="footer">
        Esperamos que nuestra oferta cumpla con sus expectativas.<br><br>
        Cordialmente

        @if ($usuario ?? null)
            <div class="firma">
                @if ($usuario->imagen_firma_url)
                    <img src="data:image/png;base64,{{ base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($usuario->imagen_firma)) }}" />
                @endif
                <div class="nombre">{{ $usuario->nombre_completo }}</div>
                @if ($usuario->puesto)
                    <div class="puesto">{{ $usuario->puesto }}</div>
                @endif
            </div>
        @else
            <br><strong>Dream Roll</strong>
        @endif
    </div>

</body>
</html>