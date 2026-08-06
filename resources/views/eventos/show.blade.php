<x-layouts::app :title="$evento->titulo">

    <div x-data="galeriaEvento()" x-init="init()">

        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('eventos.index') }}"
                   class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-secondary">{{ $evento->titulo }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $evento->fecha?->format('d/m/Y') }}
                        @if($evento->fecha && ($evento->lugar || $evento->cliente)) &middot; @endif
                        {{ $evento->lugar }}
                        @if($evento->lugar && $evento->cliente) &middot; @endif
                        {{ $evento->cliente }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('eventos.edit', $evento) }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Editar
                </a>
                <form method="POST" action="{{ route('eventos.destroy', $evento) }}"
                      onsubmit="return confirm('¿Eliminar este evento y todas sus imágenes?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Descripción --}}
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
            <p class="whitespace-pre-line text-sm text-gray-700">{{ $evento->descripcion }}</p>
            @if(!$evento->activo)
                <p class="mt-3 inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500">
                    No visible en el portafolio público
                </p>
            @endif
        </div>

        {{-- Dropzone --}}
        <div class="mb-6">
            <p class="mb-2 text-sm font-medium text-gray-700">Galería de imágenes</p>
            <div
                @click="$refs.inputArchivo.click()"
                @dragover.prevent="arrastrando = true"
                @dragleave.prevent="arrastrando = false"
                @drop.prevent="arrastrando = false; manejarArchivos($event.dataTransfer.files)"
                :class="arrastrando ? 'border-primary bg-primary/5' : 'border-gray-300 hover:border-gray-400'"
                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-10 text-center transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <p class="text-sm text-gray-500">
                    Arrastra imágenes aquí o <span class="font-medium text-secondary">haz clic para seleccionar</span>
                </p>
                <p class="text-xs text-gray-400">Se convertirán a WebP automáticamente. La primera imagen será la portada.</p>
                <input type="file" x-ref="inputArchivo" accept="image/*" multiple class="hidden"
                       @change="manejarArchivos($event.target.files)">
            </div>
        </div>

        {{-- Cola de subida en progreso --}}
        <div x-show="colaSubida.length > 0" x-cloak class="mb-6 space-y-2">
            <template x-for="item in colaSubida" :key="item.id">
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-3">
                    <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100">
                        <img :src="item.preview" class="h-full w-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="truncate text-xs text-gray-600" x-text="item.nombre"></p>
                        <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-primary transition-all" :style="'width: ' + item.progreso + '%'"></div>
                        </div>
                    </div>
                    <span class="shrink-0 text-xs" :class="item.error ? 'text-red-500' : 'text-gray-400'"
                          x-text="item.error ? 'Error' : (item.progreso + '%')"></span>
                </div>
            </template>
        </div>

        {{-- Galería (reordenable) --}}
        <div x-show="imagenes.length === 0 && colaSubida.length === 0" class="rounded-xl border border-gray-200 bg-white py-12 text-center text-sm text-gray-400">
            Aún no hay imágenes en este evento.
        </div>

        <div x-ref="galeria" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <template x-for="(img, index) in imagenes" :key="img.id">
                <div class="group relative aspect-square cursor-move overflow-hidden rounded-xl border border-gray-200 bg-gray-100"
                     :data-id="img.id">
                    <img :src="img.imagen_url" class="h-full w-full object-cover">

                    <div x-show="index === 0"
                         class="absolute left-2 top-2 rounded-full bg-secondary px-2 py-0.5 text-[10px] font-semibold text-white">
                        Portada
                    </div>

                    <button type="button" @click="eliminarImagen(img.id)"
                            class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-black/50 text-white opacity-0 transition-opacity hover:bg-red-500 group-hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        function galeriaEvento() {
            return {
                imagenes: @json($evento->galeria->map(fn($g) => [
                    'id' => $g->id,
                    'imagen_url' => $g->imagen_url,
                ])),
                colaSubida: [],
                arrastrando: false,
                siguienteColaId: 1,

                init() {
                    this.$nextTick(() => {
                        Sortable.create(this.$refs.galeria, {
                            animation: 150,
                            onEnd: () => this.guardarOrden(),
                        });
                    });
                },

                manejarArchivos(fileList) {
                    const archivos = Array.from(fileList).filter(f => f.type.startsWith('image/'));
                    archivos.forEach(file => this.subirArchivo(file));
                },

                subirArchivo(file) {
                    const colaId = this.siguienteColaId++;
                    const preview = URL.createObjectURL(file);

                    this.colaSubida.push({
                        id: colaId,
                        nombre: file.name,
                        preview: preview,
                        progreso: 0,
                        error: false,
                    });

                    const formData = new FormData();
                    formData.append('imagen', file);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', "{{ route('eventos.imagenes.subir', $evento) }}");
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            const item = this.colaSubida.find(i => i.id === colaId);
                            if (item) item.progreso = Math.round((e.loaded / e.total) * 100);
                        }
                    });

                    xhr.onload = () => {
                        const item = this.colaSubida.find(i => i.id === colaId);
                        if (xhr.status >= 200 && xhr.status < 300) {
                            const data = JSON.parse(xhr.responseText);
                            this.imagenes.push({ id: data.id, imagen_url: data.imagen_url });
                            this.colaSubida = this.colaSubida.filter(i => i.id !== colaId);
                            this.$nextTick(() => {
                                // Re-sincroniza Sortable con el nuevo elemento
                            });
                        } else {
                            if (item) {
                                item.error = true;
                                item.progreso = 0;
                            }
                        }
                    };

                    xhr.onerror = () => {
                        const item = this.colaSubida.find(i => i.id === colaId);
                        if (item) {
                            item.error = true;
                        }
                    };

                    xhr.send(formData);
                },

                guardarOrden() {
                    const ids = Array.from(this.$refs.galeria.children).map(el => parseInt(el.dataset.id));

                    // Reordena el array local para que coincida con el DOM
                    this.imagenes.sort((a, b) => ids.indexOf(a.id) - ids.indexOf(b.id));

                    fetch("{{ route('eventos.imagenes.reordenar', $evento) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ orden: ids }),
                    });
                },

                eliminarImagen(id) {
                    if (!confirm('¿Eliminar esta imagen?')) return;

                    fetch(`{{ route('eventos.imagenes.eliminar', [$evento, '__ID__']) }}`.replace('__ID__', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    }).then(() => {
                        this.imagenes = this.imagenes.filter(img => img.id !== id);
                    });
                },
            };
        }
    </script>

</x-layouts::app>