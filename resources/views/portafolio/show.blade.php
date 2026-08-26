<x-layouts::public :title="$evento->titulo">

    <style>
        .masonry {
            columns: 1;
            column-gap: 1rem;
        }
        @media (min-width: 640px) {
            .masonry { columns: 2; }
        }
        @media (min-width: 1024px) {
            .masonry { columns: 3; }
        }
        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1rem;
            border-radius: 1rem;
            overflow: hidden;
            cursor: zoom-in;
            display: block;
        }
        .masonry-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.4s ease;
        }
        .masonry-item:hover img {
            transform: scale(1.03);
        }
    </style>

    {{-- ══════════════ NAVBAR ══════════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-black/5 bg-white/80 backdrop-blur-md">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-0 sm:px-10">
            <img src="{{ asset('logo_principal.png') }}" alt="Dream Roll" class="h-32 w-auto">

            <div class="hidden items-center gap-8 text-sm font-medium text-gray-500 md:flex">
                <a href="{{ route('welcome') }}#quienes-somos" class="transition-colors hover:text-[#1b2d4f]">Quiénes somos</a>
                <a href="{{ route('welcome') }}#servicios" class="transition-colors hover:text-[#1b2d4f]">Servicios</a>
                <a href="{{ route('welcome') }}#eventos" class="transition-colors hover:text-[#1b2d4f]">Eventos</a>
                <a href="{{ route('welcome') }}#comenzamos" class="transition-colors hover:text-[#1b2d4f]">Contacto</a>
            </div>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-full px-5 py-2.5 text-sm font-semibold text-white transition-transform hover:scale-105"
                   style="background-color:#1b2d4f;">
                    Ir al dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-full px-5 py-2.5 text-sm font-semibold text-white transition-transform hover:scale-105"
                   style="background-color:#1b2d4f;">
                    Iniciar sesión
                </a>
            @endauth
        </nav>
    </header>

    <section class="bg-white py-16 sm:py-20" x-data="lightboxGaleria()">
        <div class="mx-auto max-w-6xl px-6 sm:px-10">

            <a href="{{ route('portafolio.index') }}"
               class="mb-8 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-secondary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al portafolio
            </a>

            <div class="mb-10">
                <p class="mb-2 text-xs font-semibold uppercase tracking-widest" style="color:#f5a623;">
                    {{ $evento->cliente ?: 'Proyecto' }}
                </p>
                <h1 class="text-3xl font-extrabold sm:text-4xl" style="color:#1b2d4f;">{{ $evento->titulo }}</h1>
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-400">
                    @if($evento->fecha)
                        <span>{{ $evento->fecha }}</span>
                    @endif
                    @if($evento->lugar)
                        <span>{{ $evento->lugar }}</span>
                    @endif
                </div>
                <p class="mt-5 max-w-2xl whitespace-pre-line leading-relaxed text-gray-600">{{ $evento->descripcion }}</p>
            </div>

            @if($evento->galeria->isNotEmpty())
                <div class="masonry">
                    @foreach($evento->galeria as $index => $imagen)
                        <button type="button" @click="abrir({{ $index }})" class="masonry-item w-full text-left">
                            <img src="{{ $imagen->imagen_url }}" alt="{{ $evento->titulo }}" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400">Este evento aún no tiene imágenes en la galería.</p>
            @endif

        </div>

        {{-- Lightbox --}}
        <div x-show="abierto" x-cloak
             @keydown.escape.window="cerrar()"
             @keydown.arrow-right.window="siguiente()"
             @keydown.arrow-left.window="anterior()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 sm:p-8">

            <button type="button" @click="cerrar()"
                    class="absolute right-4 top-4 flex size-10 items-center justify-center rounded-full text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <button type="button" @click.stop="anterior()"
                    class="absolute left-2 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full text-white/70 hover:bg-white/10 hover:text-white transition-colors sm:left-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" @click.stop="siguiente()"
                    class="absolute right-2 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full text-white/70 hover:bg-white/10 hover:text-white transition-colors sm:right-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <img @click.stop :src="imagenes[indice]"
                 class="max-h-full max-w-full rounded-lg object-contain shadow-2xl"
                 @click.away="cerrar()">

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/40 px-3 py-1 text-xs text-white/80"
                 x-text="(indice + 1) + ' / ' + imagenes.length"></div>
        </div>
    </section>

    <footer class="py-12" style="background-color:#1b2d4f;">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-6 text-center sm:px-10">
            <img src="{{ asset('logo_blanco.png') }}" alt="Dream Roll" class="h-8 w-auto opacity-90">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Dream Roll. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        function lightboxGaleria() {
            return {
                imagenes: @json($evento->galeria->pluck('imagen_url')),
                indice: 0,
                abierto: false,

                abrir(index) {
                    this.indice = index;
                    this.abierto = true;
                },
                cerrar() {
                    this.abierto = false;
                },
                siguiente() {
                    if (!this.abierto) return;
                    this.indice = (this.indice + 1) % this.imagenes.length;
                },
                anterior() {
                    if (!this.abierto) return;
                    this.indice = (this.indice - 1 + this.imagenes.length) % this.imagenes.length;
                },
            };
        }
    </script>

</x-layouts::public>