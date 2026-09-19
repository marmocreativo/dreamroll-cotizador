<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #333; margin: 0; padding: 14px; }
        .header-tabla { width: 100%; margin-bottom: 4px; }
        .header-tabla td { vertical-align: top; padding: 0; }
        .col-datos { width: 68%; }
        .col-logo { width: 32%; text-align: right; }
        .col-logo img { width: 130px; height: auto; }
        .fecha { font-size: 8.5px; color: #555; margin-bottom: 16px; white-space: nowrap; }
        .separador { width: 100%; height: 3px; background: #3bccf9; margin: 6px 0 20px 0; border-radius: 2px; }
        .col-datos strong.titulo {
            display: block;
            font-size: 10px;
            color: #3bccf9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .col-datos .contacto { font-size: 13px; font-weight: bold; color: #083081; }
        .col-datos .puesto { font-size: 11px; color: #555; }
        .col-datos .empresa { font-size: 11px; font-style: italic; color: #083081; }
        .intro { margin-top: 4px; margin-bottom: 20px; color: #444; }
        table.productos { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.productos thead { display: table-header-group; }
        table.productos thead tr { background-color: #083081; color: #fff; }
        table.productos thead th { padding: 5px 6px; text-align: left; font-size: 8px; text-transform: uppercase; }
        table.productos tbody tr { border-bottom: 1px solid #e5e7eb; page-break-inside: avoid; }
        table.productos tbody td { padding: 4px 6px; font-size: 8.5px; }
        table.productos tbody tr.grupo-header td { padding: 4px 6px; font-size: 8px; font-weight: bold; text-transform: uppercase; background: #f3f4f6; }
        table.productos tbody tr.grupo-header { page-break-after: avoid; }
        .totales { page-break-inside: avoid; }
        .text-right { text-align: right; white-space: nowrap; }
        .text-center { text-align: center; white-space: nowrap; }
        .totales { width: 360px; margin-left: auto; margin-top: 8px; }
        .totales td { padding: 4px 8px; font-size: 9px; }
        .totales .total-row { font-weight: bold; font-size: 10px; border-top: 2px solid #ff9f12; color: #083081; }
        .pie-tabla { width: 100%; margin-top: 16px; border-collapse: collapse; }
        .pie-tabla td { vertical-align: top; padding: 0; }
        .col-firma { width: 38%; padding-right: 14px; }
        .col-condiciones { width: 62%; text-align: left; }
        .condiciones { padding: 8px; background: #f2fafe; border-left: 3px solid #3bccf9; font-size: 7.5px; line-height: 1.35; }
        .condiciones strong { font-size: 8px; }
        .condiciones .vigencia { margin-top: 6px; }
        .footer-texto { font-size: 8.5px; color: #888; }
        .firma { margin-top: 14px; }
        .firma img { height: 36px; width: auto; display: block; margin-bottom: 4px; }
        .firma .nombre { font-size: 10px; font-weight: bold; color: #083081; }
        .firma .puesto { font-size: 9px; color: #666; }
    </style>
</head>
<body>

    <table class="header-tabla">
        <tr>
            <td class="col-datos">
                <div class="fecha">Ciudad de México a {{ now()->format('d/m/Y') }}.</div>

                <strong class="titulo">Cliente</strong>
                <div class="contacto">{{ $cotizacion->cliente_nombre_completo }}</div>
                @if ($cotizacion->cliente_puesto)
                    <div class="puesto">{{ $cotizacion->cliente_puesto }}</div>
                @endif
                @if ($cotizacion->cliente_empresa)
                    <div class="empresa">{{ $cotizacion->cliente_empresa }}</div>
                @endif
            </td>
            <td class="col-logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo_latimer.png'))) }}" />
            </td>
        </tr>
    </table>

    <div class="separador"></div>

    <p class="intro">
        A continuación, presentamos nuestra propuesta económica conforme a su solicitud:
    </p>

    <table class="productos">
        <thead>
            <tr>
                <th style="width: 32px;"></th>
                <th>Descripción</th>
                @if ($cotizacion->tipo === 'avanzado')
                    <th class="text-center">Día</th>
                    <th class="text-center">Horario</th>
                    <th class="text-center">Lugar</th>
                @endif
                <th class="text-center">Cantidad</th>
                <th class="text-center">Precio unitario</th>
                <th class="text-center">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->productos->groupBy('grupo') as $nombreGrupo => $renglonesGrupo)
                @if ($nombreGrupo)
                    <tr class="grupo-header">
                        <td colspan="{{ $cotizacion->tipo === 'avanzado' ? 8 : 5 }}">{{ $nombreGrupo }}</td>
                    </tr>
                @endif
                @foreach ($renglonesGrupo as $renglon)
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
                            <img src="{{ $imagenBase64 }}" style="width: 26px; height: 26px; object-fit: cover; border-radius: 3px;" />
                        @endif
                    </td>
                    <td>
                        <strong>{{ $renglon->producto?->nombre ?? '—' }}</strong>
                    </td>
                    @if ($cotizacion->tipo === 'avanzado')
                        <td class="text-center">{{ $renglon->dia ?: '—' }}</td>
                        <td class="text-center">{{ $renglon->horario ?: '—' }}</td>
                        <td class="text-center">{{ $renglon->lugar ?: '—' }}</td>
                    @endif
                    <td class="text-center">{{ $renglon->cantidad }}</td>
                    <td class="text-center">${{ number_format($renglon->precio_unitario, 2) }}</td>
                    <td class="text-center">${{ number_format($renglon->subtotal, 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if ($cotizacion->tipo === 'avanzado' && $cotizacion->hospedajes->isNotEmpty())
        <table class="productos">
            <thead>
                <tr>
                    <th>Hospedaje</th>
                    <th class="text-center">Check in</th>
                    <th class="text-center">Check out</th>
                    <th class="text-center">Noches</th>
                    <th class="text-center">Habs</th>
                    <th class="text-center">Costo unit.</th>
                    <th class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cotizacion->hospedajes as $h)
                <tr>
                    <td><strong>{{ $h->nombre }}</strong></td>
                    <td class="text-center">{{ $h->checkin ?: '—' }}</td>
                    <td class="text-center">{{ $h->checkout ?: '—' }}</td>
                    <td class="text-center">{{ $h->noches }}</td>
                    <td class="text-center">{{ $h->habitaciones }}</td>
                    <td class="text-center">${{ number_format($h->costo_unitario, 2) }}</td>
                    <td class="text-center">${{ number_format($h->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php
        $totalProductosPdf = $cotizacion->productos->sum('subtotal');
        $totalHospedajesPdf = $cotizacion->hospedajes->sum('total');
        $baseFeePdf = $totalProductosPdf + $cotizacion->iva + $totalHospedajesPdf;
        $descuentoMontoPdf = $baseFeePdf > 0
            ? ($baseFeePdf + $cotizacion->fee_agencia) * ($cotizacion->descuento / 100)
            : 0;
    @endphp
    <table class="totales">
        <tr>
            <td>SUBTOTAL PRODUCTOS</td>
            <td class="text-right">${{ number_format($totalProductosPdf, 2) }}</td>
        </tr>
        <tr>
            <td>IVA PRODUCTOS (16%)</td>
            <td class="text-right">${{ number_format($cotizacion->iva, 2) }}</td>
        </tr>
        @if ($cotizacion->tipo === 'avanzado' && $cotizacion->hospedajes->isNotEmpty())
        <tr>
            <td>SUBTOTAL HOSPEDAJES</td>
            <td class="text-right">${{ number_format($totalHospedajesPdf, 2) }}</td>
        </tr>
        @endif
        @if ($cotizacion->fee_porcentaje > 0)
        <tr>
            <td>FEE DE AGENCIA ({{ number_format($cotizacion->fee_porcentaje, 2) }}%)</td>
            <td class="text-right">${{ number_format($cotizacion->fee_agencia, 2) }}</td>
        </tr>
        @endif
        @if ($cotizacion->descuento > 0)
        <tr>
            <td>DESCUENTO ({{ $cotizacion->descuento }}%)</td>
            <td class="text-right">-${{ number_format($descuentoMontoPdf, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL ({{ $cotizacion->moneda ?? 'MXN' }})</td>
            <td class="text-right">${{ number_format($cotizacion->total, 2) }}</td>
        </tr>
    </table>

    <table class="pie-tabla">
        <tr>
            <td class="col-firma">
                <div class="footer-texto">
                    Confiamos en que esta propuesta responda a sus necesidades y quedamos a su disposición para cualquier duda.<br><br>
                    Atentamente
                </div>

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
                    <div class="firma"><strong>Latimer Publicidad y Medios</strong></div>
                @endif
            </td>
            <td class="col-condiciones">
                @if ($cotizacion->tiempo_entrega || $cotizacion->condiciones || $cotizacion->valida_hasta)
                    <div class="condiciones">
                        <strong>Con las siguientes condiciones:</strong><br>
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
            </td>
        </tr>
    </table>

</body>
</html>