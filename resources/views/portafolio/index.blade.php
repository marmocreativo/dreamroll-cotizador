<x-layouts::public :title="__('Portafolio de eventos')">

    <style>
        .gallery-item {
            position: relative;
            overflow: hidden;
        }
        .gallery-item::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(27,45,79,0.85) 0%, rgba(27,45,79,0.1) 55%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .gallery-item:hover::after { opacity: 1; }
        .gallery-caption {
            position: absolute;
            left: 1.25rem;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 2;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .gallery-item:hover .gallery-caption {
            opacity: 1;
            transform: translateY(0);
        }
        .gallery-item img {
            transition: transform 0.5s ease;
        }
        .gallery-item:hover img {
            transform: scale(1.06);
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

    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 sm:px-10">
            <div class="mb-12 text-center">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest" style="color:#f5a623;">Portafolio</p>
                <h1 class="text-3xl font-extrabold sm:text-4xl" style="color:#1b2d4f;">Eventos que hemos creado</h1>
                <p class="mx-auto mt-4 max-w-xl text-base text-gray-500">
                    Explora las experiencias, activaciones y congresos que hemos producido.
                </p>
            </div>

            @if($eventos->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($eventos as $evento)
                        <a href="{{ route('portafolio.show', $evento) }}"
                           class="gallery-item aspect-4/3 rounded-2xl">
                            @if($evento->portada_url)
                                <img src="{{ $evento->portada_url }}" alt="{{ $evento->titulo }}"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center" style="background: linear-gradient(135deg, #1b2d4f 0%, #33507f 100%);"></div>
                            @endif
                            <div class="gallery-caption">
                                @if($evento->cliente)
                                    <p class="text-xs font-semibold uppercase tracking-wide text-white/70">{{ $evento->cliente }}</p>
                                @endif
                                <p class="text-lg font-bold text-white">{{ $evento->titulo }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $eventos->links() }}
                </div>
            @else
                <p class="text-center text-gray-400">Próximamente compartiremos nuestros proyectos aquí.</p>
            @endif
        </div>
    </section>

    <footer class="py-12" style="background-color:#1b2d4f;">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-6 text-center sm:px-10">
            <img src="{{ asset('logo_blanco.png') }}" alt="Dream Roll" class="h-8 w-auto opacity-90">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Dream Roll. Todos los derechos reservados.</p>
        </div>
    </footer>

</x-layouts::public>