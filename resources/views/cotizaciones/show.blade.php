<x-layouts::app :title="$cotizacion->folio">

    @php
        $estadoConfig = [
            'borrador'  => ['bg-gray-100',  'text-gray-600',   'Borrador'],
            'enviada'   => ['bg-blue-50',   'text-blue-700',   'Enviada'],
            'aceptada'  => ['bg-green-50',  'text-green-700',  'Aceptada'],
            'rechazada' => ['bg-red-50',    'text-red-700',    'Rechazada'],
            'expirada'  => ['bg-yellow-50', 'text-yellow-700', 'Expirada'],
        ];
        [$bgEstado, $textEstado, $labelEstado] = $estadoConfig[$cotizacion->estado] ?? ['bg-gray-100', 'text-gray-600', $cotizacion->estado];
    @endphp

    {{-- Encabezado --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
            <a href="{{ route('cotizaciones.index') }}"
               class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $cotizacion->folio }}</h1>
                <p class="text-sm text-gray-500">{{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Acciones de estado --}}
        <div class="flex items-center gap-2 flex-wrap">
            @if($cotizacion->estado === 'borrador')
                <form method="POST" action="{{ route('cotizaciones.estado', $cotizacion) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="enviada">
                    <button type="submit"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                            style="background-color:#002745;">
                        Marcar como enviada
                    </button>
                </form>
            @elseif($cotizacion->estado === 'enviada')
                <form method="POST" action="{{ route('cotizaciones.estado', $cotizacion) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="aceptada">
                    <button type="submit"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                            style="background-color:#002745;">
                        Marcar como aceptada
                    </button>
                </form>
                <form method="POST" action="{{ route('cotizaciones.estado', $cotizacion) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="rechazada">
                    <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        Rechazada
                    </button>
                </form>
            @endif

            {{-- Editar --}}
            <a href="{{ route('cotizaciones.edit', $cotizacion) }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                </svg>
                Editar
            </a>

            {{-- Descargar PDF --}}
            <a href="{{ route('cotizaciones.pdf', $cotizacion) }}"
            target="_blank"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Descargar PDF
            </a>

            {{-- WhatsApp --}}
            @php
                $urlPdf    = route('cotizaciones.pdf.ver', $cotizacion);
                $telefono  = preg_replace('/\D/', '', $cotizacion->medico?->telefono ?? '');
                $mensaje   = urlencode(
                    "Estimado(a) {$cotizacion->medico?->nombre_completo},\n\n" .
                    "Le compartimos la cotización *{$cotizacion->folio}* por un total de " .
                    "*$" . number_format($cotizacion->total, 2) . " MXN*.\n\n" .
                    "Puede consultarla en el siguiente enlace:\n{$urlPdf}\n\n" .
                    "Quedamos a sus órdenes."
                );
                $waUrl = $telefono
                    ? "https://wa.me/{$telefono}?text={$mensaje}"
                    : "https://wa.me/?text={$mensaje}";
            @endphp

            <a href="{{ $waUrl }}"
            target="_blank"
            class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                </svg>
                WhatsApp
            </a>

            {{-- Enviar por correo --}}
            <div x-data="{ abierto: false }" class="relative">
                <button type="button"
                        @click="abierto = !abierto"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    Enviar por correo
                </button>

                <div x-show="abierto"
                     @click.outside="abierto = false"
                     x-transition
                     class="absolute right-0 z-10 mt-2 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-lg">
                    <p class="mb-2 text-sm font-medium text-gray-700">Enviar cotización</p>
                    <form method="POST" action="{{ route('cotizaciones.enviar', $cotizacion) }}">
                        @csrf
                        <input type="email"
                               name="email"
                               value="{{ $cotizacion->medico?->email ?? '' }}"
                               placeholder="correo@ejemplo.com"
                               required
                               class="mb-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        <button type="submit"
                                class="w-full rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                                style="background-color:#002745;">
                            Enviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas info general --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Estado</p>
            <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $bgEstado }} {{ $textEstado }}">
                {{ $labelEstado }}
            </span>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Médico</p>
            <p class="mt-1 font-medium text-gray-900">{{ $cotizacion->medico?->nombre_completo ?? '—' }}</p>
            @if($cotizacion->medico?->especialidad)
                <p class="text-sm text-gray-400">{{ $cotizacion->medico->especialidad }}</p>
            @endif
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Institución</p>
            <p class="mt-1 font-medium text-gray-900">{{ $cotizacion->hospital?->nombre ?? '—' }}</p>
            @if($cotizacion->hospital?->procedencia)
                <span class="mt-1 inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                    {{ $cotizacion->hospital->procedencia }}
                </span>
            @endif
        </div>
    </div>

    {{-- Tabla de estudios --}}
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Estudios solicitados</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estudio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Cantidad</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Precio unitario</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($cotizacion->estudios as $renglon)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $renglon->estudio?->nombre ?? '—' }}</div>
                            @if($renglon->notas)
                                <div class="text-xs text-gray-400">{{ $renglon->notas }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $renglon->cantidad }}</td>
                        <td class="px-4 py-3 text-gray-600">${{ number_format($renglon->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">${{ number_format($renglon->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        @php
            $subtotalConDescuento = $cotizacion->subtotal - ($cotizacion->subtotal * ($cotizacion->descuento / 100));
            $iva = $subtotalConDescuento * 0.16;
            $totalConIva = $subtotalConDescuento + $iva;
        @endphp
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col items-end gap-1">
                <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($cotizacion->subtotal, 2) }}</span>
                </div>
                @if($cotizacion->descuento > 0)
                    <div class="flex w-full max-w-xs justify-between text-sm">
                        <span class="text-gray-600">Descuento ({{ $cotizacion->descuento }}%)</span>
                        <span class="text-red-500">-${{ number_format($cotizacion->subtotal * ($cotizacion->descuento / 100), 2) }}</span>
                    </div>
                @endif
                <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                    <span>IVA (16%)</span>
                    <span>${{ number_format($iva, 2) }}</span>
                </div>
                <div class="flex w-full max-w-xs justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900">
                    <span>Total</span>
                    <span>${{ number_format($totalConIva, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notas y vigencia --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @if($cotizacion->notas)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Notas</p>
                <p class="mt-2 text-sm text-gray-700">{{ $cotizacion->notas }}</p>
            </div>
        @endif
        @if($cotizacion->valida_hasta)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Vigencia</p>
                <p class="mt-2 text-sm text-gray-700">Válida hasta el {{ $cotizacion->valida_hasta?->format('d/m/Y') ?? '—' }}</p>
            </div>
        @endif
    </div>

</x-layouts::app>