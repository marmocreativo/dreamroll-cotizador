<x-layouts::app :title="$producto ?? null ? __('Editar producto') : __('Nuevo producto')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('productos.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">
                {{ isset($producto) ? 'Editar producto' : 'Nuevo producto' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ isset($producto) ? 'Actualiza los datos del producto' : 'Agrega un producto al catálogo' }}
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($producto) ? route('productos.update', $producto) : route('productos.store') }}"
          enctype="multipart/form-data"
          class="max-w-lg flex flex-col gap-6">
        @csrf
        @if(isset($producto))
            @method('PUT')
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4"
             x-data="{ preview: {{ isset($producto) && $producto->imagen_url ? "'".$producto->imagen_url."'" : 'null' }} }">

            {{-- Imagen --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Imagen</label>
                <div class="flex items-center gap-4">
                    <div class="flex size-20 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                        <template x-if="preview">
                            <img :src="preview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!preview">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen" accept="image/*"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-gray-700 hover:file:bg-gray-200" />
                        <p class="mt-1 text-xs text-gray-400">Se recortará a 600x600px automáticamente.</p>
                        @error('imagen')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('nombre')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('descripcion') ? 'border-red-400' : 'border-gray-200' }}">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Precio unitario <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                    <input type="number" name="precio_unitario" step="0.01" min="0"
                           value="{{ old('precio_unitario', $producto->precio_unitario ?? '') }}"
                           class="w-full rounded-lg border pl-7 pr-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('precio_unitario') ? 'border-red-400' : 'border-gray-200' }}" />
                </div>
                @error('precio_unitario')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" id="activo" name="activo" value="1"
                       @checked(old('activo', $producto->activo ?? true))
                       class="rounded border-gray-300 text-secondary focus:ring-secondary">
                <label for="activo" class="text-sm text-gray-700">Producto activo</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('productos.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                {{ isset($producto) ? 'Guardar cambios' : 'Crear producto' }}
            </button>
        </div>
    </form>

</x-layouts::app>