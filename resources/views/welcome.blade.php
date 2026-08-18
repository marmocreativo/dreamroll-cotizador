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
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-0 sm:px-10">
            <img src="{{ asset('logo_principal.png') }}" alt="Dream Roll" class="h-32 w-auto">

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
    <section class="relative min-h-screen overflow-hidden pt-24"
             style="background-image: url('{{ asset('images/bg_hero.jpg') }}'); background-size: cover; background-position: top center;">
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(255,255,255,0.2) 100%);"></div>

        <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-stretch gap-0 px-6 py-16 sm:px-10 lg:grid-cols-2 lg:gap-12 lg:py-24">
            <div class="rounded-3xl p-8 sm:p-10 lg:p-12 flex flex-col justify-center" style="background-color:#1b2d4f;">
                <span class="mb-5 inline-flex w-fit items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest" style="background-color:#f5a623; color:#1b2d4f;">
                    Agencia creativa
                </span>
                <h1 class="text-5xl font-extrabold leading-[1.05] text-white sm:text-6xl">
                    Hacemos que tu marca <span style="color:#ffc000;">destaque</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-relaxed text-white/70">
                    Marketing estratégico, creativo e innovador. Combinamos innovación digital con
                    ejecución impecable en campo para generar valor comercial y emocional.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#comenzamos"
                       class="rounded-full px-7 py-3.5 text-sm font-semibold shadow-lg transition-transform hover:scale-105"
                       style="background-color:#f5a623; color:#1b2d4f;">
                        Empezar un proyecto
                    </a>
                    <a href="#eventos"
                       class="rounded-full border-2 px-7 py-3.5 text-sm font-semibold text-white transition-colors hover:bg-white/10"
                       style="border-color:#ffc000;">
                        Ver portafolio
                    </a>
                </div>

                <div class="mt-14 flex flex-wrap gap-10">
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#ffc000;">+120</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-white/50">Proyectos activados</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#ffc000;">+40</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-white/50">Marcas atendidas</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold" style="color:#ffc000;">100%</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-white/50">Ejecución impecable</p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center">
                <div class="w-full rounded-3xl bg-white p-8 shadow-2xl">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Contáctanos</p>
                    <p class="mt-1 text-xl font-extrabold" style="color:#1b2d4f;">Cuéntanos tu proyecto</p>

                    <form id="form-contacto-hero" class="mt-6 space-y-4">
                        <div>
                            <label for="hero-nombre" class="mb-1 block text-xs font-semibold text-gray-500">Nombre</label>
                            <input type="text" id="hero-nombre" required
                                   class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                                   style="--tw-ring-color:#f5a623;">
                        </div>
                        <div>
                            <label for="hero-empresa" class="mb-1 block text-xs font-semibold text-gray-500">Empresa</label>
                            <input type="text" id="hero-empresa"
                                   class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                                   style="--tw-ring-color:#f5a623;">
                        </div>
                        <div>
                            <label for="hero-mensaje" class="mb-1 block text-xs font-semibold text-gray-500">Mensaje</label>
                            <textarea id="hero-mensaje" rows="3"
                                      class="w-full resize-none rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                                      style="--tw-ring-color:#f5a623;"
                                      placeholder="Cuéntanos qué necesitas..."></textarea>
                        </div>
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-semibold text-white shadow-lg transition-transform hover:scale-105"
                                style="background-color:#1b2d4f;">
                            Enviar por WhatsApp
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('form-contacto-hero')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const nombre  = document.getElementById('hero-nombre').value.trim();
                const empresa = document.getElementById('hero-empresa').value.trim();
                const mensaje = document.getElementById('hero-mensaje').value.trim();

                let texto = `Hola, soy ${nombre}`;
                if (empresa) texto += ` de ${empresa}`;
                texto += '. Me gustaría cotizar un proyecto con Dream Roll.';
                if (mensaje) texto += ` ${mensaje}`;

                const url = 'https://wa.me/5215540590595?text=' + encodeURIComponent(texto);
                window.open(url, '_blank');
            });
        </script>
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
                    $iconoClases = 'size-7 text-white';

                    $servicios = [
                        [
                            'titulo' => 'Marketing con impacto',
                            'items' => ['Campañas ATL y BTL', 'Materiales promocionales', 'Piezas para fuerza de ventas', 'Coordinación editorial científica'],
                            'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$iconoClases.'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" /></svg>',
                        ],
                        [
                            'titulo' => 'Eventos científicos',
                            'items' => ['Congresos nacionales e internacionales', 'Simposios satélite y advisory boards', 'Rutas de visita inmersiva', 'Experiencias itinerantes'],
                            'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$iconoClases.'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>',
                        ],
                        [
                            'titulo' => 'Experiencias inmersivas',
                            'items' => ['Experiencias de marca a medida', 'Escape rooms temáticos', 'Realidad virtual y aumentada', 'Dinámicas interactivas'],
                            'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$iconoClases.'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" /></svg>',
                        ],
                        [
                            'titulo' => 'Nuestra metodología',
                            'items' => ['1. Planificar', '2. Producir', '3. Implementar y ejecutar', '4. Cautivar'],
                            'icono' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$iconoClases.'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>',
                        ],
                    ];
                @endphp

                @foreach($servicios as $servicio)
                    <div class="card-lift relative rounded-2xl bg-white p-7 pt-12 shadow-sm">
                        <div class="absolute -top-8 left-7 flex size-16 items-center justify-center rounded-full shadow-lg" style="background: linear-gradient(135deg, #f5a623 0%, #ffc000 100%);">
                            {!! $servicio['icono'] !!}
                        </div>
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

        <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 sm:px-10 lg:grid-cols-2">
            <div>
                <h2 class="text-4xl font-extrabold sm:text-5xl" style="color:#1b2d4f;">¿Comenzamos?</h2>
                <p class="mt-6 max-w-xl text-base leading-relaxed" style="color:#1b2d4f;">
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

            <div class="w-full rounded-3xl bg-white p-8 shadow-2xl">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Contáctanos</p>
                <p class="mt-1 text-xl font-extrabold" style="color:#1b2d4f;">Cuéntanos tu proyecto</p>

                <form id="form-contacto-comenzamos" class="mt-6 space-y-4">
                    <div>
                        <label for="comenzamos-nombre" class="mb-1 block text-xs font-semibold text-gray-500">Nombre</label>
                        <input type="text" id="comenzamos-nombre" required
                               class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                               style="--tw-ring-color:#f5a623;">
                    </div>
                    <div>
                        <label for="comenzamos-empresa" class="mb-1 block text-xs font-semibold text-gray-500">Empresa</label>
                        <input type="text" id="comenzamos-empresa"
                               class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                               style="--tw-ring-color:#f5a623;">
                    </div>
                    <div>
                        <label for="comenzamos-mensaje" class="mb-1 block text-xs font-semibold text-gray-500">Mensaje</label>
                        <textarea id="comenzamos-mensaje" rows="3"
                                  class="w-full resize-none rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2"
                                  style="--tw-ring-color:#f5a623;"
                                  placeholder="Cuéntanos qué necesitas..."></textarea>
                    </div>
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-semibold text-white shadow-lg transition-transform hover:scale-105"
                            style="background-color:#1b2d4f;">
                        Enviar por WhatsApp
                    </button>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('form-contacto-comenzamos')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const nombre  = document.getElementById('comenzamos-nombre').value.trim();
                const empresa = document.getElementById('comenzamos-empresa').value.trim();
                const mensaje = document.getElementById('comenzamos-mensaje').value.trim();

                let texto = `Hola, soy ${nombre}`;
                if (empresa) texto += ` de ${empresa}`;
                texto += '. Me gustaría cotizar un proyecto con Dream Roll.';
                if (mensaje) texto += ` ${mensaje}`;

                const url = 'https://wa.me/5215540590595?text=' + encodeURIComponent(texto);
                window.open(url, '_blank');
            });
        </script>
    </section>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <footer class="py-12" style="background-color:#1b2d4f;">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-6 text-center sm:px-10">
            <img src="{{ asset('logo_blanco.png') }}" alt="Dream Roll" class="h-32 w-auto opacity-90">
            <p class="text-xs text-white/40">&copy; {{ date('Y') }} Dream Roll. Todos los derechos reservados.</p>
        </div>
    </footer>

</x-layouts::public>