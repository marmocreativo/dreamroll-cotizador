<x-layouts::app :title="__('Productos')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Productos</h1>
            <p class="text-sm text-gray-500">Catálogo de productos publicitarios</p>
        </div>
        <a href="{{ route('productos.create') }}"
           class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
            + Nuevo producto
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('productos.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Buscar</label>
            <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                   placeholder="Nombre o descripción"
                   class="w-64 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Estado</label>
            <select name="activo"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">Todos</option>
                <option value="1" @selected(request('activo') === '1')>Activos</option>
                <option value="0" @selected(request('activo') === '0')>Inactivos</option>
            </select>
        </div>
        <button type="submit"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Filtrar
        </button>
        @if(request('busqueda') || request()->has('activo'))
            <a href="{{ route('productos.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Producto</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Descripción</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Precio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($productos as $producto)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($producto->imagen_url)
                                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                         class="size-10 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="flex size-10 items-center justify-center rounded-lg bg-gray-100 text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900">{{ $producto->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ Str::limit($producto->descripcion, 60) ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 font-medium">
                            ${{ number_format($producto->precio_unitario, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            @if($producto->activo)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activo</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('productos.destroy', $producto) }}"
                                      onsubmit="return confirm('¿Eliminar este producto?')">
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
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>

</x-layouts::app>