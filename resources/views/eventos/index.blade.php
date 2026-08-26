<x-layouts::app :title="__('Eventos')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Eventos</h1>
            <p class="text-sm text-gray-500">Galería de portafolio</p>
        </div>
        <a href="{{ route('eventos.create') }}"
           class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
            + Nuevo evento
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('eventos.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-500">Buscar</label>
            <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                   placeholder="Título o cliente"
                   class="w-64 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
        </div>
        <button type="submit"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Filtrar
        </button>
        @if(request('busqueda'))
            <a href="{{ route('eventos.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($eventos as $evento)
            <a href="{{ route('eventos.show', $evento) }}"
               class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition-shadow hover:shadow-md">
                <div class="aspect-video overflow-hidden bg-gray-100">
                    @if($evento->portada_url)
                        <img src="{{ $evento->portada_url }}" alt="{{ $evento->titulo }}"
                             class="h-full w-full object-cover transition-transform group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <div class="mb-1 flex items-center justify-between gap-2">
                        <p class="font-semibold text-gray-900">{{ $evento->titulo }}</p>
                        @if(!$evento->activo)
                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-500">Inactivo</span>
                        @endif
                    </div>
                    <p class="mb-2 text-sm text-gray-500 line-clamp-2">{{ $evento->descripcion }}</p>
                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-400">
                        @if($evento->fecha)
                            <span>{{ $evento->fecha }}</span>
                        @endif
                        @if($evento->lugar)
                            <span>{{ $evento->lugar }}</span>
                        @endif
                        @if($evento->cliente)
                            <span>{{ $evento->cliente }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-gray-200 bg-white py-16 text-center text-gray-400">
                No hay eventos registrados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $eventos->links() }}
    </div>

</x-layouts::app>