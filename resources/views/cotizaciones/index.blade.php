<x-layouts::app :title="__('Cotizaciones')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Cotizaciones</h1>
            <p class="text-sm text-gray-500">Historial y seguimiento de cotizaciones</p>
        </div>
        <a href="{{ route('cotizaciones.create') }}"
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
           style="background-color:#002745;">
            + Nueva cotización
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('cotizaciones.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <input type="text"
                   name="busqueda"
                   value="{{ request('busqueda') }}"
                   placeholder="Buscar por folio, médico o institución..."
                   class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
        </div>
        <select name="estado"
                onchange="this.form.submit()"
                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:w-48">
            <option value="">Todos los estados</option>
            <option value="borrador"  @selected(request('estado') === 'borrador')>Borrador</option>
            <option value="enviada"   @selected(request('estado') === 'enviada')>Enviada</option>
            <option value="aceptada"  @selected(request('estado') === 'aceptada')>Aceptada</option>
            <option value="rechazada" @selected(request('estado') === 'rechazada')>Rechazada</option>
            <option value="expirada"  @selected(request('estado') === 'expirada')>Expirada</option>
        </select>
        <button type="submit"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Buscar
        </button>
        @if(request('busqueda') || request('estado'))
            <a href="{{ route('cotizaciones.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Folio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Médico</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Institución</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Total</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Fecha</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($cotizaciones as $cotizacion)
                    @php
                        $estadoConfig = [
                            'borrador'  => 'bg-gray-100 text-gray-600',
                            'enviada'   => 'bg-blue-50 text-blue-700',
                            'aceptada'  => 'bg-green-50 text-green-700',
                            'rechazada' => 'bg-red-50 text-red-700',
                            'expirada'  => 'bg-yellow-50 text-yellow-700',
                        ];
                        $labels = [
                            'borrador'  => 'Borrador',
                            'enviada'   => 'Enviada',
                            'aceptada'  => 'Aceptada',
                            'rechazada' => 'Rechazada',
                            'expirada'  => 'Expirada',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono font-medium text-gray-900">{{ $cotizacion->folio }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $cotizacion->medico?->nombre_completo ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $cotizacion->hospital?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900">${{ number_format($cotizacion->total, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $estadoConfig[$cotizacion->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $labels[$cotizacion->estado] ?? $cotizacion->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $cotizacion->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('cotizaciones.show', $cotizacion) }}"
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                   title="Ver detalle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>

                                <a href="{{ route('cotizaciones.edit', $cotizacion) }}"
                                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                <div x-data="{ abierto: false }">
                                    <button @click="abierto = true"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                                            title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>

                                    <div x-show="abierto"
                                         x-transition
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                                            <h3 class="text-lg font-semibold text-gray-900">¿Eliminar cotización?</h3>
                                            <p class="mt-1 text-sm text-gray-500">Esta acción eliminará la cotización y todos sus renglones.</p>
                                            <div class="mt-6 flex justify-end gap-3">
                                                <button @click="abierto = false"
                                                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                    Cancelar
                                                </button>
                                                <form method="POST" action="{{ route('cotizaciones.destroy', $cotizacion) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            No se encontraron cotizaciones.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cotizaciones->links() }}
    </div>

</x-layouts::app>