<x-layouts::app :title="isset($evento) ? __('Editar evento') : __('Nuevo evento')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ isset($evento) ? route('eventos.show', $evento) : route('eventos.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">
                {{ isset($evento) ? 'Editar evento' : 'Nuevo evento' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ isset($evento) ? 'Actualiza los datos del evento' : 'Después podrás agregar imágenes a la galería' }}
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($evento) ? route('eventos.update', $evento) : route('eventos.store') }}"
          class="max-w-2xl flex flex-col gap-6">
        @csrf
        @if(isset($evento))
            @method('PUT')
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Título <span class="text-red-500">*</span>
                </label>
                <input type="text" name="titulo" value="{{ old('titulo', $evento->titulo ?? '') }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('titulo') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('titulo')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <textarea name="descripcion" rows="4"
                          class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('descripcion') ? 'border-red-400' : 'border-gray-200' }}">{{ old('descripcion', $evento->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha" value="{{ old('fecha', isset($evento) && $evento->fecha ? $evento->fecha->toDateString() : '') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Lugar</label>
                    <input type="text" name="lugar" value="{{ old('lugar', $evento->lugar ?? '') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Cliente</label>
                    <input type="text" name="cliente" value="{{ old('cliente', $evento->cliente ?? '') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" id="activo" name="activo" value="1"
                       @checked(old('activo', $evento->activo ?? true))
                       class="rounded border-gray-300 text-secondary focus:ring-secondary">
                <label for="activo" class="text-sm text-gray-700">Visible en el portafolio público</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ isset($evento) ? route('eventos.show', $evento) : route('eventos.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                {{ isset($evento) ? 'Guardar cambios' : 'Crear evento' }}
            </button>
        </div>
    </form>

</x-layouts::app>