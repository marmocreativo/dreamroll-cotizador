<x-layouts::app :title="__('Cotización') . ' ' . $cotizacion->folio">

    <div x-data="cotizacionShow()">

        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('cotizaciones.index') }}"
                   class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <img src="{{ asset($cotizacion->origen === 'latimer' ? 'logo_latimer.png' : 'logo_principal.png') }}"
                     alt="{{ $cotizacion->origen === 'latimer' ? 'Latimer' : 'Dream Roll' }}"
                     class="h-9 w-auto object-contain" />
                <div>
                    <h1 class="text-2xl font-semibold text-secondary">{{ $cotizacion->folio }}</h1>
                    <p class="text-sm text-gray-500">Creada el {{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('cotizaciones.pdf', $cotizacion) }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Descargar PDF
                </a>
                <a href="{{ route('cotizaciones.edit', $cotizacion) }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Editar
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Columna principal --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Datos del cliente --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Datos del cliente</p>
                    <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Nombre</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_nombre_completo }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Puesto</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_puesto ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Empresa</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_empresa ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Teléfono</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_telefono ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_email ?: '—' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-gray-400">Dirección</p>
                            <p class="text-gray-900">{{ $cotizacion->cliente_direccion ?: '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Productos --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Productos/Servicios</p>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
<table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-3 py-2 w-14"></th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Producto/Servicio</th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Cant.</th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Costo</th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Aum. %</th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Precio unit.</th>
                                    <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($cotizacion->productos as $item)
                                    <tr>
                                        <td class="px-3 py-2">
                                            @if($item->producto?->imagen_url)
                                                <img src="{{ $item->producto->imagen_url }}"
                                                     alt="{{ $item->producto->nombre }}"
                                                     class="size-10 rounded object-cover border border-gray-200" />
                                            @else
                                                <div class="flex size-10 items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3 15h18M2.25 4.5h19.5M4.5 4.5v15h15v-15" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-gray-900">
                                            {{ $item->producto->nombre ?? 'Producto eliminado' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-600">{{ $item->cantidad }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $item->costo !== null ? '$'.number_format($item->costo, 2) : '—' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $item->aumento_porcentaje !== null ? number_format($item->aumento_porcentaje, 2).'%' : '—' }}</td>
                                        <td class="px-3 py-2 text-gray-600">${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <div class="w-full max-w-xs space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Importe productos</span>
                                <span class="text-gray-900">${{ number_format($cotizacion->subtotal, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Fee de agencia ({{ number_format($cotizacion->fee_porcentaje, 2) }}%)</span>
                                <span class="text-gray-900">${{ number_format($cotizacion->fee_agencia, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="text-gray-900">${{ number_format($cotizacion->subtotal + $cotizacion->fee_agencia, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Descuento ({{ $cotizacion->descuento }}%)</span>
                                <span class="text-gray-900">-${{ number_format(($cotizacion->subtotal + $cotizacion->fee_agencia) * $cotizacion->descuento / 100, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Importe con descuento</span>
                                <span class="text-gray-900">${{ number_format(($cotizacion->subtotal + $cotizacion->fee_agencia) - (($cotizacion->subtotal + $cotizacion->fee_agencia) * $cotizacion->descuento / 100), 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">IVA (16%)</span>
                                <span class="text-gray-900">${{ number_format($cotizacion->iva, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-2 text-base">
                                <span class="font-semibold text-gray-900">Total</span>
                                <span class="font-semibold text-secondary">${{ number_format($cotizacion->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Entrega y condiciones --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Entrega y condiciones</p>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Tiempo de entrega</p>
                            <p class="text-gray-900">{{ $cotizacion->tiempo_entrega ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Válida hasta</p>
                            <p class="text-gray-900">{{ $cotizacion->valida_hasta?->format('d/m/Y') ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Condiciones</p>
                            <p class="text-gray-900 whitespace-pre-line">{{ $cotizacion->condiciones ?: '—' }}</p>
                        </div>
                        @if($cotizacion->notas)
                            <div>
                                <p class="text-xs text-gray-400">Notas internas</p>
                                <p class="text-gray-900 whitespace-pre-line">{{ $cotizacion->notas }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Columna lateral: estado y envío --}}
            <div class="space-y-6">

                {{-- Estado --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Estado</p>
                    <form method="POST" action="{{ route('cotizaciones.estado', $cotizacion) }}">
                        @csrf
                        @method('PATCH')
                        <select name="estado" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            @foreach(['borrador', 'enviada', 'aceptada', 'rechazada', 'expirada'] as $estado)
                                <option value="{{ $estado }}" @selected($cotizacion->estado === $estado)>
                                    {{ ucfirst($estado) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Firmante --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Firma en PDF / correo</p>
                    <form method="POST" action="{{ route('cotizaciones.firmante', $cotizacion) }}">
                        @csrf
                        @method('PATCH')
                        <select name="firmante_id" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}"
                                    @selected(($cotizacion->firmante_id ?? $cotizacion->created_by) === $usuario->id)>
                                    {{ $usuario->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-400">
                            El PDF y el correo se firman con la firma del usuario seleccionado.
                        </p>
                    </form>
                </div>

                {{-- Enviar --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-3">
                    <p class="mb-1 text-sm font-medium text-gray-700">Enviar cotización</p>

                    {{-- Botón: abrir modal de correo --}}
                    <button type="button" @click="modalEmailAbierto = true; emailDestino = '{{ $cotizacion->cliente_email }}'"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        Enviar por correo
                    </button>

                    {{-- Botón WhatsApp: solo si hay teléfono --}}
                    @if($cotizacion->cliente_telefono)
                        <button type="button" @click="confirmarWhatsapp('{{ $cotizacion->cliente_telefono }}')"
                                class="flex w-full items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm0 18.15h-.01a8.2 8.2 0 01-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.22 8.22 0 01-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 012.41 5.83c0 4.55-3.7 8.24-8.24 8.24zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.13-.17.24-.64.8-.78.97-.14.17-.29.19-.53.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.24-.02-.37.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.24-.42.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.24 3.74.59.26 1.06.41 1.42.52.6.19 1.14.16 1.57.1.48-.07 1.47-.6 1.68-1.19.21-.58.21-1.08.15-1.19-.06-.1-.23-.16-.48-.28z"/>
                            </svg>
                            Enviar por WhatsApp
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal: confirmar correo --}}
        <div x-show="modalEmailAbierto" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div @click.outside="modalEmailAbierto = false"
                 class="w-full max-w-sm rounded-xl bg-white p-6 shadow-lg">
                <p class="mb-1 text-sm font-medium text-gray-900">Enviar cotización por correo</p>
                <p class="mb-4 text-xs text-gray-500">Confirma o edita el correo de destino.</p>

                <form method="POST" action="{{ route('cotizaciones.enviar', $cotizacion) }}">
                    @csrf
                    <label class="mb-1 block text-sm font-medium text-gray-700">Correo destino</label>
                    <input type="email" name="email" x-model="emailDestino" required
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />

                    <div class="mt-5 flex items-center justify-end gap-2">
                        <button type="button" @click="modalEmailAbierto = false"
                                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                            Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function cotizacionShow() {
            return {
                modalEmailAbierto: false,
                emailDestino: '',

                confirmarWhatsapp(telefonoGuardado) {
                    const numero = prompt('Confirma el número de WhatsApp (con lada, ej. 5215512345678):', telefonoGuardado);
                    if (!numero) return;

                    const url = "{{ route('cotizaciones.whatsapp', $cotizacion) }}?telefono=" + encodeURIComponent(numero);
                    window.location.href = url;
                },
            };
        }
    </script>

</x-layouts::app>