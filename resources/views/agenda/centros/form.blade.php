<x-layouts::app :title="__( isset($centro) ? 'Editar centro' : 'Nuevo centro')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('agenda.centros.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ isset($centro) ? 'Editar centro' : 'Nuevo centro' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ isset($centro) ? 'Actualiza los datos del centro' : 'Registra un nuevo centro' }}
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($centro) ? route('agenda.centros.update', $centro) : route('agenda.centros.store') }}"
          class="flex flex-col gap-6 max-w-2xl">
        @csrf
        @isset($centro)
            @method('PUT')
        @endisset

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Datos del centro</h2>

            <div class="flex flex-col gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nombre"
                           value="{{ old('nombre', $centro->nombre ?? '') }}"
                           placeholder="Nombre del centro o laboratorio"
                           class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}" />
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Dirección</label>
                    <textarea name="direccion"
                              rows="2"
                              placeholder="Calle, número, colonia..."
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('direccion', $centro->direccion ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox"
                           name="activo"
                           id="activo"
                           value="1"
                           {{ old('activo', $centro->activo ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <label for="activo" class="text-sm font-medium text-gray-700">Centro activo</label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agenda.centros.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                {{ isset($centro) ? 'Guardar cambios' : 'Registrar centro' }}
            </button>
        </div>
    </form>

</x-layouts::app>