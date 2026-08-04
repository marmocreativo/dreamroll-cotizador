<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeneticLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            min-height: 100vh;
            background: #001a2e;
            color: #fff;
        }

        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Fondo con imagen de ADN — si tienes una imagen ponla aquí,
           si no, usamos un gradiente que simula el azul profundo */
        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(0,20,50,0.82) 0%, rgba(0,40,80,0.70) 50%, rgba(0,20,40,0.85) 100%),
                url('/images/adn-hero.jpg') center/cover no-repeat;
            z-index: 0;
        }

        /* Overlay azul adicional para el tono de la captura */
        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 60, 120, 0.35);
        }

        /* ── Navbar ── */
        nav {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 3rem;
        }

        .nav-logo {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.01em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link {
            display: inline-block;
            padding: 0.4rem 1.1rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255,255,255,0.55);
            color: #fff;
            font-size: 0.85rem;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.8);
        }

        .nav-link.whatsapp {
            background: #25D366;
            border-color: #25D366;
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-link.whatsapp:hover {
            background: #1ebe5d;
            border-color: #1ebe5d;
        }

        /* ── Hero content ── */
        .hero-body {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 1.5rem 5rem;
            gap: 1.25rem;
        }

        .hero-eyebrow {
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 800;
            line-height: 1.12;
            max-width: 700px;
            color: #fff;
            text-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1rem;
            color: rgba(255,255,255,0.75);
            max-width: 520px;
            line-height: 1.65;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .btn-primary {
            display: inline-block;
            padding: 0.65rem 1.75rem;
            border-radius: 999px;
            background: #fff;
            color: #002745;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary:hover { background: #e8f0fe; }

        .btn-outline {
            display: inline-block;
            padding: 0.65rem 1.75rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255,255,255,0.6);
            color: #fff;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-outline:hover { background: rgba(255,255,255,0.1); }

        /* ── Stats ── */
        .hero-stats {
            display: flex;
            gap: 3rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.18);
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-item { text-align: center; }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.5);
            margin-top: 0.2rem;
        }
    </style>
</head>
<body>
<section class="hero">
    <div class="hero-bg"></div>

    {{-- Navbar --}}
    <nav>
        <img src="{{ asset('images/logo_blanco.png') }}" alt="GeneticLab" style="height: 40px;">
    </nav>

    {{-- Hero body --}}
    <div class="hero-body">
        <img src="{{ asset('images/logo_blanco.png') }}" alt="GeneticLab" style="height: 40px;">
        <h1 class="hero-title">
            Sistema interno de cotizaciones.
        </h1>
        <p class="hero-subtitle">
            Más de 5,300 pruebas procesadas desde 2019. Resultados en máximo 72 horas con interpretación clínica directa al médico tratante.
        </p>
        <div class="hero-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary">Ir al dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Iniciar sesión</a>
            @endauth
        </div>
    </div>
</section>
</body>
</html>