<x-layouts::app :title="__('Cotizaciones')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Cotizaciones</h1>
            <p class="text-sm text-gray-500">Historial de cotizaciones generadas</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cotizaciones.exportar', request()->only(['busqueda', 'estado'])) }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Exportar Excel
            </a>
            <a href="{{ route('cotizaciones.create') }}"
               class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                + Nueva cotización
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('cotizaciones.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Buscar</label>
            <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                   placeholder="Folio, cliente o empresa"
                   class="w-64 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Estado</label>
            <select name="estado"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">Todos</option>
                @foreach(['borrador', 'enviada', 'aceptada', 'rechazada', 'expirada'] as $estado)
                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                        {{ ucfirst($estado) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Filtrar
        </button>
        @if(request('busqueda') || request('estado'))
            <a href="{{ route('cotizaciones.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Folio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Cliente</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Empresa</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Total</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Fecha</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $badgeColores = [
                        'borrador'  => 'bg-gray-100 text-gray-600',
                        'enviada'   => 'bg-blue-50 text-blue-700',
                        'aceptada'  => 'bg-emerald-50 text-emerald-700',
                        'rechazada' => 'bg-red-50 text-red-700',
                        'expirada'  => 'bg-amber-50 text-amber-700',
                    ];
                @endphp
                @forelse($cotizaciones as $cotizacion)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="window.location='{{ route('cotizaciones.show', $cotizacion) }}'">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cotizacion->folio }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $cotizacion->cliente_nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cotizacion->cliente_empresa ?: '—' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">${{ number_format($cotizacion->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeColores[$cotizacion->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($cotizacion->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ $cotizacion->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('cotizaciones.edit', $cotizacion) }}"
                                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('cotizaciones.destroy', $cotizacion) }}"
                                      onsubmit="return confirm('¿Eliminar esta cotización?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            No hay cotizaciones registradas.
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