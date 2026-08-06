<x-layouts::public :title="__('Agencia de Marketing Estratégico')">

    <style>
        .text-gradient {
            background: linear-gradient(120deg, #f5a623 0%, #ffc000 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .card-lift {
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(27,45,79,0.25);
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
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
        .gallery-item img, .gallery-item .ph {
            transition: transform 0.5s ease;
        }
        .gallery-item:hover img,
        .gallery-item:hover .ph {
            transform: scale(1.06);
        }

        .blob {
            border-radius: 42% 58% 65% 35% / 45% 40% 60% 55%;
        }

        .marquee-track {
            display: flex;
            width: max-content;
            animation: marquee 26s linear infinite;
        }
        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        html { scroll-behavior: smooth; }
    </style>

    {{-- ══════════════ NAVBAR ══════════════ --}}
    <header class="fixed inset-x-0 top-0 z-50 border-b border-black/5 bg-white/80 backdrop-blur-md">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 sm:px-10">
            <img src="{{ asset('logo_principal.png') }}" alt="Dream Roll" class="h-9 w-auto">

            <div class="hidden items-center gap-8 text-sm font-medium text-gray-500 md:flex">
                <a href="#quienes-somos" class="transition-colors hover:text-[#1b2d4f]">Quiénes somos</a>
                <a href="#servicios" class="transition-colors hover:text-[#1b2d4f]">Servicios</a>
                <a href="#eventos" class="transition-colors hover:text-[#1b2d4f]">Eventos</a>
                <a href="#comenzamos" class="transition-colors hover:text-[#1b2d4f]">Contacto</a>
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

    {{-- ══════════════ HERO ══════════════ --}}
    <section class="relative min-h-screen overflow-hidden bg-white pt-24">
        <div class="blob absolute -right-32 -top-20 h-[520px] w-[520px] opacity-90" style="background: linear-gradient(135deg, #f5a623 0%, #ffc000 100%);"></div>
        <div class="blob absolute -left-40 bottom-0 h-[380px] w-[380px] opacity-10" style="background-color:#1b2d4f;"></div>

        <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 py-16 sm:px-10 lg:grid-cols-2 lg:py-24">
            <div>
                <span class="mb-5 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white" style="background-color:#1b2d4f;">
                    Agencia creativa
                </span>
                <h1 class="text-5xl font-extrabold leading-[1.05] sm:text-6xl">
                    Hacemos que tu marca <span class="text-gradient">destaque</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-gray-500">
                    Marketing estratégico, creativo e innovador. Combinamos innovación digital con
                    ejecución impecable en campo para generar valor comercial y emocional.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#comenzamos"
                       class="rounded-full px-7 py-3.5 text-sm font-semibold text-white shadow-lg transition-transform hover:scale-105"
                       style="background-color:#1b2d4f;">
                        Empezar un proyecto
                    </a>
                    <a href="#eventos"
                       class="rounded-full border-2 px-7 py-3.5 text-sm font-semibold transition-colors hover:bg-gray-50"
                       style="border-color:#1b2d4f; color:#1b2d4f;">
                        Ver portafolio
                    </a>
                </div>

                <div class="mt-14 flex flex-wrap gap-10">
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#1b2d4f;">+120</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Proyectos activados</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#1b2d4f;">+40</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Marcas atendidas</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#1b2d4f;">100%</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Ejecución impecable</p>
                    </div>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="relative aspect-square overflow-hidden rounded-[2.5rem] shadow-2xl">
                    <img src="{{ asset('logo_principal.png') }}" alt="Dream Roll"
                         class="h-full w-full object-contain bg-gray-50 p-16">
                </div>
                <div class="absolute -bottom-6 -left-6 rounded-2xl bg-white p-5 shadow-xl">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Metodología</p>
                    <p class="text-sm font-bold" style="color:#1b2d4f;">Planificar · Producir · Ejecutar · Cautivar</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════ QUIÉNES SOMOS ══════════════ --}}
    <section id="quienes-somos" class="bg-white py-24">
        <div class="mx-auto max-w-4xl px-6 text-center sm:px-10">
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest" style="color:#f5a623;">Quiénes somos</p>
            <h2 class="text-3xl font-extrabold sm:text-4xl" style="color:#1b2d4f;">
                Una agencia especializada en soluciones de marketing estratégico, creativo e innovador
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-gray-500">
                Nos especializamos en crear estrategias efectivas para ayudar a nuestros clientes a aumentar
                su visibilidad y atraer a su público objetivo, combinando innovación digital con ejecución
                impecable en campo para generar valor comercial y emocional.
            </p>
        </div>
    </section>

    {{-- ══════════════ SERVICIOS ══════════════ --}}
    <section id="servicios" class="py-24" style="background-color:#faf9f7;">
        <div class="mx-auto max-w-7xl px-6 sm:px-10">
            <div class="mb-14 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-widest" style="color:#f5a623;">Lo que hacemos</p>
                    <h2 class="text-3xl font-extrabold sm:text-4xl" style="color:#1b2d4f;">Servicios especializados</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @php
                    $servicios = [
                        [
                            'titulo' => 'Marketing con impacto',
                            'items' => ['Campañas ATL y BTL', 'Materiales promocionales', 'Piezas para fuerza de ventas', 'Coordinación editorial científica'],
                        ],
                        [
                            'titulo' => 'Eventos científicos',
                            'items' => ['Congresos nacionales e internacionales', 'Simposios satélite y advisory boards', 'Rutas de visita inmersiva', 'Experiencias itinerantes'],
                        ],
                        [
                            'titulo' => 'Experiencias inmersivas',
                            'items' => ['Experiencias de marca a medida', 'Escape rooms temáticos', 'Realidad virtual y aumentada', 'Dinámicas interactivas'],
                        ],
                        [
                            'titulo' => 'Nuestra metodología',
                            'items' => ['1. Planificar', '2. Producir', '3. Implementar y ejecutar', '4. Cautivar'],
                        ],
                    ];
                @endphp

                @foreach($servicios as $servicio)
                    <div class="card-lift rounded-2xl bg-white p-7 shadow-sm">
                        <div class="mb-5 size-11 rounded-xl" style="background: linear-gradient(135deg, #f5a623 0%, #ffc000 100%);"></div>
                        <p class="mb-4 text-lg font-bold" style="color:#1b2d4f;">{{ $servicio['titulo'] }}</p>
                        <ul class="space-y-2.5">
                            @foreach($servicio['items'] as $item)
                                <li class="flex items-start gap-2 text-sm text-gray-500">
                                    <span class="mt-1.5 size-1 shrink-0 rounded-full" style="background-color:#f5a623;"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════ EVENTOS — GALERÍA DE PORTAFOLIO ══════════════ --}}
    <section id="eventos" class="bg-white py-24">
        <div class="mx-auto max-w-7xl px-6 sm:px-10">
            <div class="mb-14 text-center">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest" style="color:#f5a623;">Portafolio</p>
                <h2 class="text-3xl font-extrabold sm:text-4xl" style="color:#1b2d4f;">Eventos que hemos creado</h2>
                <p class="mx-auto mt-4 max-w-xl text-base text-gray-500">
                    Un vistazo a algunas de las experiencias, activaciones y congresos que hemos producido.
                </p>
            </div>

            @php
                $eventosDestacados = \App\Models\Evento::where('activo', true)
                    ->with('galeria')
                    ->orderByDesc('fecha')
                    ->orderByDesc('created_at')
                    ->take(5)
                    ->get();
            @endphp

            @if($eventosDestacados->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:auto-rows-[260px]">
                    @foreach($eventosDestacados as $index => $evento)
                        <a href="{{ route('portafolio.show', $evento) }}"
                           class="gallery-item rounded-2xl {{ $index === 0 ? 'lg:col-span-2 lg:row-span-2' : '' }}">
                            @if($evento->portada_url)
                                <img src="{{ $evento->portada_url }}" alt="{{ $evento->titulo }}"
                                     class="h-full min-h-[260px] w-full object-cover lg:min-h-0">
                            @else
                                <div class="ph flex h-full min-h-[260px] items-center justify-center lg:min-h-0" style="background: linear-gradient(135deg, #1b2d4f 0%, #33507f 100%);"></div>
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

                <div class="mt-12 text-center">
                    <a href="{{ route('portafolio.index') }}"
                       class="inline-block rounded-full border-2 px-7 py-3.5 text-sm font-semibold transition-colors hover:bg-gray-50"
                       style="border-color:#1b2d4f; color:#1b2d4f;">
                        Ver todos los eventos
                    </a>
                </div>
            @else
                <p class="text-center text-gray-400">Próximamente compartiremos nuestros proyectos aquí.</p>
            @endif
        </div>
    </section>

    {{-- ══════════════ MARQUEE DE VALORES ══════════════ --}}
    <div class="overflow-hidden border-y border-black/5 py-6" style="background-color:#1b2d4f;">
        <div class="marquee-track">
            @for($i = 0; $i < 2; $i++)
                @foreach(['Estrategia', 'Creatividad', 'Ciencia', 'Innovación', 'Ejecución', 'Resultados'] as $palabra)
                    <span class="mx-8 text-2xl font-extrabold uppercase text-white/20">{{ $palabra }}</span>
                    <span class="mx-2 text-2xl" style="color:#f5a623;">&bull;</span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- ══════════════ ¿COMENZAMOS? ══════════════ --}}
    <section id="comenzamos" class="relative overflow-hidden py-28">
        <div class="absolute inset-0" style="background: linear-gradient(120deg, #f5a623 0%, #ffc000 100%);"></div>
        <div class="blob absolute -right-24 -top-24 h-72 w-72 opacity-20" style="background-color:#1b2d4f;"></div>

        <div class="relative z-10 mx-auto max-w-3xl px-6 text-center sm:px-10">
            <h2 class="text-4xl font-extrabold sm:text-5xl" style="color:#1b2d4f;">¿Comenzamos?</h2>
            <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed" style="color:#1b2d4f;">
                Ofrecemos consultoría inicial sin costo para conocer tu marca, producto y objetivos.
                A partir de ahí, construimos juntos una estrategia que combine ciencia, creatividad y resultados.
            </p>
            <div class="mt-10">
                @auth
                    <a href="{{ route('cotizaciones.create') }}"
                       class="inline-block rounded-full px-8 py-4 text-sm font-semibold text-white shadow-lg transition-transform hover:scale-105"
                       style="background-color:#1b2d4f;">
                        Crear nueva cotización
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-block rounded-full px-8 py-4 text-sm font-semibold text-white shadow-lg transition-transform hover:scale-105"
                       style="background-color:#1b2d4f;">
                        Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <footer class="py-12" style="background-color:#1b2d4f;">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-6 text-center sm:px-10">
            <img src="{{ asset('logo_blanco.png') }}" alt="Dream Roll" class="h-8 w-auto opacity-90">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Dream Roll. Todos los derechos reservados.</p>
        </div>
    </footer>

</x-layouts::public>