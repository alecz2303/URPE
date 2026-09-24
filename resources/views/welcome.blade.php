<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="URPE, Unidad de Rehabilitación Pediátrica Evolutiva en Tuxtla Gutiérrez, brinda rehabilitación neurológica infantil especializada y acompañamiento cercano a niños y sus familias.">
    <meta name="robots" content="index,follow">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:locale" content="es_MX">
    <meta property="og:type" content="website">
    <meta property="og:title" content="URPE | Unidad de Rehabilitación Pediátrica Evolutiva">
    <meta property="og:description" content="Atención especializada en rehabilitación neurológica infantil y acompañamiento cercano a cada niño y su familia.">
    <meta property="og:image" content="{{ asset('images/website/hero-urpe.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <title>URPE | Unidad de Rehabilitación Pediátrica Evolutiva</title>
    <link rel="preconnect" href="https://images.pexels.com" crossorigin>
    <link rel="dns-prefetch" href="//images.pexels.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }
        [data-reveal] { opacity: 0; transform: translateY(28px); transition: opacity .75s cubic-bezier(.2,.75,.25,1), transform .75s cubic-bezier(.2,.75,.25,1); }
        [data-reveal="left"] { transform: translate(-34px, 18px); }
        [data-reveal="right"] { transform: translate(34px, 18px); }
        [data-reveal].is-visible { opacity: 1; transform: translate(0, 0); }
        [data-motion="header"] { transition: box-shadow .3s ease, background-color .3s ease; }
        [data-motion="header"].is-scrolled { box-shadow: 0 12px 34px rgb(15 23 42 / .07); }
        [data-float] { animation: urpe-float 6s ease-in-out infinite; will-change: transform; }
        [data-float="fast"] { animation-duration: 4.8s; }
        [data-float="reverse"] { animation-duration: 7s; animation-direction: alternate-reverse; }
        @keyframes urpe-float { 0%,100% { transform: translate3d(0,0,0) rotate(0deg); } 50% { transform: translate3d(0,-12px,0) rotate(3deg); } }
        @keyframes urpe-logo-pulse { 0%,100% { transform: scale(.96); opacity: .88; } 50% { transform: scale(1.035); opacity: 1; } }
        @keyframes urpe-dot { 0%,100% { transform: translateY(0) scale(.8); opacity: .45; } 50% { transform: translateY(-7px) scale(1); opacity: 1; } }
        #urpe-loader.is-leaving { opacity: 0; pointer-events: none; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            [data-reveal] { opacity: 1; transform: none; transition: none; }
            [data-float] { animation: none; }
            [data-motion="header"] { transition: none; }
            #urpe-loader img, #urpe-loader span { animation: none !important; }
        }

        [data-motion="header"] {
            transition: padding .38s ease, background-color .38s ease, border-color .38s ease;
        }
        [data-motion="header"] .urpe-nav-shell {
            transition: max-width .42s cubic-bezier(.2,.8,.2,1), margin .42s cubic-bezier(.2,.8,.2,1), padding .38s ease, border-radius .38s ease, box-shadow .38s ease, background-color .38s ease;
        }
        [data-motion="header"] .urpe-nav-logo {
            transition: width .38s cubic-bezier(.2,.8,.2,1), transform .38s ease;
        }
        [data-motion="header"] .urpe-nav-accent {
            opacity: 0;
            transform: scaleX(.65);
            transition: opacity .35s ease, transform .42s ease;
        }
        [data-motion="header"].is-scrolled {
            padding: .65rem 1rem 0;
            border-color: transparent;
            background: transparent;
            backdrop-filter: none;
        }
        [data-motion="header"].is-scrolled .urpe-nav-shell {
            max-width: 70rem;
            padding-top: .55rem;
            padding-bottom: .55rem;
            border-radius: 9999px;
            background: rgba(255,255,255,.94);
            box-shadow: 0 18px 55px rgba(15,23,42,.14), 0 0 0 1px rgba(148,163,184,.13);
            backdrop-filter: blur(20px);
        }
        [data-motion="header"].is-scrolled .urpe-nav-logo {
            width: 7.5rem;
            transform: translateY(-1px);
        }
        [data-motion="header"].is-scrolled .urpe-nav-accent {
            opacity: 1;
            transform: scaleX(1);
        }
        @media (max-width: 639px) {
            [data-motion="header"].is-scrolled { padding: .5rem .55rem 0; }
            [data-motion="header"].is-scrolled .urpe-nav-logo { width: 6.4rem; }
        }
    </style>
</head>
<body id="inicio" class="bg-white text-slate-800 antialiased motion-page">
<a href="#contenido-principal" class="fixed left-4 top-4 z-[100] -translate-y-24 rounded-full bg-slate-900 px-5 py-3 font-bold text-white shadow-xl transition focus:translate-y-0">Saltar al contenido</a>
<div class="h-[76px] sm:h-[82px] lg:h-[88px]" aria-hidden="true"></div>
<div id="urpe-loader" class="fixed inset-0 z-[100] flex items-center justify-center bg-white transition-opacity duration-500" role="status" aria-label="Cargando sitio de URPE">
    <div class="relative flex flex-col items-center">
        <div aria-hidden="true" class="absolute h-48 w-48 rounded-full bg-cyan-100/60 blur-2xl"></div>
        <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="" class="relative w-52 max-w-[68vw] animate-[urpe-logo-pulse_1.45s_ease-in-out_infinite] sm:w-60">
        <div aria-hidden="true" class="relative mt-7 flex items-center gap-2">
            <span class="h-2.5 w-2.5 animate-[urpe-dot_1.2s_ease-in-out_infinite] rounded-full bg-cyan-400"></span>
            <span class="h-2.5 w-2.5 animate-[urpe-dot_1.2s_.15s_ease-in-out_infinite] rounded-full bg-pink-500"></span>
            <span class="h-2.5 w-2.5 animate-[urpe-dot_1.2s_.3s_ease-in-out_infinite] rounded-full bg-lime-500"></span>
            <span class="h-2.5 w-2.5 animate-[urpe-dot_1.2s_.45s_ease-in-out_infinite] rounded-full bg-yellow-400"></span>
        </div>
        <span class="sr-only">Cargando…</span>
    </div>
</div>
<div class="relative min-h-screen overflow-hidden bg-white">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="absolute -left-24 top-32 h-72 w-72 rounded-full bg-cyan-100/55 blur-3xl"></div>
        <div class="absolute right-[-7rem] top-[-5rem] h-80 w-80 rounded-full bg-pink-100/55 blur-3xl"></div>
        <div class="absolute bottom-[-7rem] left-[38%] h-72 w-72 rounded-full bg-lime-100/45 blur-3xl"></div>
    </div>

    <header data-motion="header" class="fixed inset-x-0 top-0 z-50 border-b border-transparent bg-transparent">
        <div class="urpe-nav-shell relative mx-auto flex max-w-7xl items-center justify-between gap-6 overflow-visible rounded-none bg-white/95 px-5 py-3 shadow-none backdrop-blur-xl sm:px-7 lg:px-8">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="URPE, inicio">
                <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE - Unidad de Rehabilitación Pediátrica Evolutiva" class="urpe-nav-logo h-auto w-32 object-contain sm:w-36 lg:w-40">
            </a>

            <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 lg:flex" aria-label="Navegación principal">
                <a href="{{ route('home') }}" class="text-pink-600">Inicio</a>
                <a href="#nosotros" class="transition hover:text-pink-600">Nosotros</a>
                <a href="#terapias" class="transition hover:text-pink-600">Terapias</a>
                <a href="#equipo" class="transition hover:text-pink-600">Equipo</a>
                <a href="#contacto" class="transition hover:text-pink-600">Contacto</a>
            </nav>
            <div class="flex items-center gap-2">
                <a
                    href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE."
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden items-center justify-center rounded-full bg-pink-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-pink-200/70 transition hover:-translate-y-0.5 hover:bg-pink-700 focus:outline-none focus:ring-4 focus:ring-pink-200 sm:inline-flex"
                >
                    Solicita una valoración
                </a>

                <details data-mobile-menu class="group relative lg:hidden">
                    <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-cyan-200 focus:outline-none focus:ring-4 focus:ring-cyan-100 [&::-webkit-details-marker]:hidden" aria-label="Abrir menú">
                        <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                        <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                        </svg>
                    </summary>
                    <nav class="absolute right-0 top-14 max-h-[calc(100vh-5.5rem)] w-[min(18rem,calc(100vw-1.5rem))] overflow-y-auto rounded-3xl border border-slate-100 bg-white p-3 shadow-2xl shadow-slate-300/40" aria-label="Navegación móvil">
                        <a href="{{ route('home') }}" class="block rounded-2xl bg-pink-50 px-4 py-3 font-bold text-pink-600">Inicio</a>
                        <a href="#nosotros" class="block rounded-2xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">Nosotros</a>
                        <a href="#terapias" class="block rounded-2xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">Terapias</a>
                        <a href="#equipo" class="block rounded-2xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">Equipo</a>
                        <a href="#contacto" class="block rounded-2xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">Contacto</a>
                        <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="mt-2 flex min-h-12 items-center justify-center rounded-2xl bg-pink-600 px-4 text-center text-sm font-bold text-white">Solicita una valoración</a>
                    </nav>
                </details>
            </div>
            <div aria-hidden="true" class="urpe-nav-accent pointer-events-none absolute inset-x-12 -bottom-px h-[3px] overflow-hidden rounded-full bg-gradient-to-r from-cyan-400 via-pink-500 via-50% to-lime-400"></div>
        </div>
    </header>

    <main class="relative z-10">
        <section class="mx-auto grid min-h-[calc(100vh-96px)] max-w-7xl items-center gap-10 px-5 pb-14 pt-7 sm:px-8 sm:pt-10 lg:grid-cols-[0.92fr_1.08fr] lg:gap-14 lg:px-10 lg:pb-16 lg:pt-2">
            <div data-reveal="left" class="max-w-2xl">
                <div class="mb-6 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-700 sm:text-sm">
                    <span class="h-1 w-8 rounded-full bg-yellow-400"></span>Rehabilitación infantil
                </div>
                <h1 class="text-[2.8rem] font-black leading-[0.98] tracking-[-0.045em] text-slate-800 sm:text-6xl lg:text-[4.35rem]">
                    Acompañamos cada paso de su
                    <span class="relative inline-block text-pink-600">desarrollo.<span aria-hidden="true" class="absolute -bottom-3 left-0 h-1.5 w-full rounded-full bg-gradient-to-r from-cyan-400 via-pink-500 to-lime-400"></span></span>
                </h1>
                <p class="mt-9 max-w-xl text-lg leading-8 text-slate-600">
                    Atención especializada en <strong class="font-bold text-slate-800">rehabilitación neurológica infantil</strong> para potenciar las capacidades de cada niño, favorecer su autonomía y acompañar de cerca a su familia.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="inline-flex min-h-14 items-center justify-center gap-2 rounded-full bg-pink-600 px-7 font-bold text-white shadow-xl shadow-pink-200/70 transition hover:-translate-y-0.5 hover:bg-pink-700 focus:outline-none focus:ring-4 focus:ring-pink-200">
                        Solicita una valoración <span aria-hidden="true">→</span>
                    </a>
                    <a href="#nosotros" class="inline-flex min-h-14 items-center justify-center rounded-full border border-slate-200 bg-white px-7 font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-cyan-100">Conoce URPE</a>
                </div>

                <div class="mt-9 flex max-w-xl items-start gap-4 text-sm leading-6 text-slate-500">
                    <div class="mt-1 flex shrink-0 -space-x-1.5" aria-hidden="true">
                        <span class="h-3.5 w-3.5 rounded-full border-2 border-white bg-cyan-400"></span><span class="h-3.5 w-3.5 rounded-full border-2 border-white bg-pink-500"></span><span class="h-3.5 w-3.5 rounded-full border-2 border-white bg-lime-500"></span><span class="h-3.5 w-3.5 rounded-full border-2 border-white bg-yellow-400"></span>
                    </div>
                    <p>Acompañamos con amor, profesionalismo y conocimiento el neurodesarrollo de tu hijo.</p>
                </div>
            </div>

            <div data-reveal="right" class="relative mx-auto flex min-h-[390px] w-full max-w-2xl items-center justify-center sm:min-h-[500px] lg:min-h-[560px]">
                <div aria-hidden="true" data-float="slow" class="absolute right-[2%] top-[5%] h-24 w-24 rounded-full bg-cyan-200/60"></div>
                <div aria-hidden="true" data-float="reverse" class="absolute bottom-[7%] right-[1%] h-20 w-20 rounded-full border-[14px] border-yellow-300/70"></div>
                <div aria-hidden="true" data-float="fast" class="absolute bottom-[22%] left-[1%] h-12 w-12 rounded-full bg-pink-200/70"></div>
                <div aria-hidden="true" class="absolute left-[8%] top-[10%] flex gap-1.5 -rotate-12">
                    <span class="h-2.5 w-2.5 rounded-full bg-pink-400/75"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-300/90"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-lime-400/75"></span>
                </div>
                <svg aria-hidden="true" class="absolute right-[8%] top-[28%] h-10 w-10 rotate-12 text-pink-300/80" viewBox="0 0 48 48" fill="none">
                    <path d="M24 5c2.4 10.2 8.8 16.6 19 19-10.2 2.4-16.6 8.8-19 19-2.4-10.2-8.8-16.6-19-19C15.2 21.6 21.6 15.2 24 5Z" fill="currentColor"/>
                </svg>
                <svg aria-hidden="true" class="absolute bottom-[8%] left-[12%] h-12 w-12 -rotate-12 text-cyan-300/80" viewBox="0 0 52 52" fill="none">
                    <path d="M7 34c8-1 12-6 13-15 3 8 8 12 16 12-7 3-11 8-11 16-4-7-9-11-18-13Z" fill="currentColor"/>
                </svg>
                <div class="relative h-[380px] w-full max-w-[540px] overflow-hidden rounded-[44%_56%_48%_52%/55%_43%_57%_45%] bg-cyan-50 shadow-2xl shadow-slate-200/70 ring-1 ring-cyan-100 sm:h-[480px] lg:h-[510px]">
                    <img
                        src="{{ asset('images/website/hero-urpe.jpg') }}"
                        alt="Profesional de URPE acompañando a un niño durante su atención"
                        class="h-full w-full object-cover object-[47%_center] sm:object-[46%_center] lg:object-[45%_center]"
                        fetchpriority="high"
                        decoding="async"
                        width="1600"
                        height="1067"
                    >
                    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900/10 via-transparent to-white/5"></div>
                </div>
                <div class="absolute bottom-2 left-0 rounded-2xl bg-white/95 px-5 py-4 shadow-xl shadow-slate-200/70 ring-1 ring-slate-100 backdrop-blur sm:bottom-4 sm:left-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-lime-600">Atención infantil</p>
                    <p class="mt-1 font-bold text-slate-800">Desde el nacimiento hasta los 10 años</p>
                </div>
            </div>
        </section>

        <section id="nosotros" class="scroll-mt-28 relative overflow-hidden border-t border-slate-100 bg-slate-50/70 py-20 sm:py-24 lg:py-28">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                <div class="absolute -left-24 top-16 h-64 w-64 rounded-full bg-cyan-100/60 blur-3xl"></div>
                <div class="absolute -right-20 bottom-0 h-64 w-64 rounded-full bg-yellow-100/70 blur-3xl"></div>
            </div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-[0.78fr_1.22fr] lg:gap-20 lg:px-10">
                <div data-reveal="left" class="relative mx-auto w-full max-w-md">
                    <div class="rounded-[2.5rem] bg-white p-8 shadow-xl shadow-slate-200/60 ring-1 ring-slate-100 sm:p-10">
                        <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-cyan-700">Nuestra historia</p>
                        <p class="mt-5 text-7xl font-black tracking-[-0.07em] text-pink-600 sm:text-8xl">2015</p>
                        <p class="mt-3 text-lg font-bold leading-7 text-slate-800">El año en que comenzó el camino de URPE.</p>
                        <div class="mt-8 flex items-center gap-2" aria-hidden="true">
                            <span class="h-3 w-10 rounded-full bg-cyan-400"></span>
                            <span class="h-3 w-3 rounded-full bg-pink-500"></span>
                            <span class="h-3 w-7 rounded-full bg-lime-500"></span>
                            <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                        </div>
                    </div>
                    <div aria-hidden="true" class="absolute -right-5 -top-6 h-16 w-16 rounded-full border-[10px] border-yellow-300/80"></div>
                    <div aria-hidden="true" class="absolute -bottom-5 -left-5 h-14 w-14 rounded-full bg-cyan-200/80"></div>
                </div>

                <div data-reveal="right" class="max-w-2xl">
                    <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-pink-600 sm:text-sm">
                        <span class="h-1 w-8 rounded-full bg-yellow-400"></span>Desde 2015
                    </div>
                    <h2 class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">
                        Creciendo junto a <span class="text-cyan-600">ellos.</span>
                    </h2>
                    <p class="mt-7 text-lg leading-8 text-slate-600">
                        URPE nació a partir de la inquietud y el compromiso de ofrecer una atención más especializada y humana a niños con problemas neurológicos que requieren rehabilitación física.
                    </p>
                    <p class="mt-5 text-lg leading-8 text-slate-600">
                        Desde el inicio, su propósito ha sido <strong class="font-bold text-slate-800">potenciar al máximo las capacidades de cada niño</strong>, favorecer su desarrollo y autonomía, y contribuir a mejorar su bienestar y la calidad de vida de sus familias.
                    </p>

                    <div class="mt-9 rounded-3xl border border-white bg-white/90 p-6 shadow-lg shadow-slate-200/40 sm:p-7">
                        <p class="text-base leading-7 text-slate-600">
                            Hoy, URPE es un espacio de acompañamiento donde el trabajo va más allá de la rehabilitación física: se orienta y trabaja de manera cercana con cada familia para avanzar juntos.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
            <div aria-hidden="true" class="pointer-events-none absolute -left-20 top-12 h-64 w-64 rounded-full bg-cyan-100/65 blur-2xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute right-8 top-12 h-20 w-20 rounded-full bg-pink-100"></div>
            <div aria-hidden="true" class="pointer-events-none absolute right-[8%] top-28 flex gap-2"><span class="h-3 w-3 rounded-full bg-cyan-400"></span><span class="h-3 w-3 rounded-full bg-pink-500"></span><span class="h-3 w-3 rounded-full bg-yellow-400"></span><span class="h-3 w-3 rounded-full bg-lime-400"></span></div>

            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div data-reveal="left" class="mx-auto max-w-3xl text-center">
                    <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-700 sm:text-sm"><span class="h-1 w-8 rounded-full bg-yellow-400"></span>A quiénes acompañamos</div>
                    <h2 class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">Cada niño tiene <span class="text-pink-600">su propio camino.</span></h2>
                    <div aria-hidden="true" class="mx-auto mt-3 flex w-44 overflow-hidden rounded-full"><span class="h-1 flex-1 bg-cyan-400"></span><span class="h-1 flex-1 bg-pink-500"></span><span class="h-1 flex-1 bg-yellow-400"></span><span class="h-1 flex-1 bg-lime-400"></span></div>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">Brindamos atención especializada a niñas y niños <strong class="font-bold text-slate-800">desde el nacimiento hasta los 10 años</strong> con alteraciones que afectan el desarrollo motor.</p>
                </div>

                @php
                    $conditions = [
                        ['Parálisis cerebral','Acompañamiento para potenciar su autonomía y habilidades.','https://images.pexels.com/photos/8653970/pexels-photo-8653970.jpeg?auto=compress&cs=tinysrgb&w=1200','cyan'],
                        ['Alto riesgo de daño neurológico','Seguimiento temprano para favorecer su desarrollo.','https://images.pexels.com/photos/7141030/pexels-photo-7141030.jpeg?auto=compress&cs=tinysrgb&w=900','pink'],
                        ['Prematurez','Estimulación y acompañamiento en sus primeras etapas.','https://images.pexels.com/photos/12365687/pexels-photo-12365687.jpeg?auto=compress&cs=tinysrgb&w=900','lime'],
                        ['Síndrome de Down','Apoyo en su desarrollo integral y habilidades para la vida diaria.','https://images.pexels.com/photos/7944379/pexels-photo-7944379.jpeg?auto=compress&cs=tinysrgb&w=900','yellow'],
                        ['Espina bífida','Acompañamiento para favorecer su movilidad, independencia y calidad de vida.','https://images.pexels.com/photos/6191916/pexels-photo-6191916.jpeg?auto=compress&cs=tinysrgb&w=1200','violet'],
                        ['Alteraciones del desarrollo motor','Estimulación para fortalecer su movimiento y coordinación.','https://images.pexels.com/photos/8504439/pexels-photo-8504439.jpeg?auto=compress&cs=tinysrgb&w=900','cyan'],
                        ['Plagiocefalia','Orientación y acompañamiento durante su desarrollo.','https://images.pexels.com/photos/14997907/pexels-photo-14997907.jpeg?auto=compress&cs=tinysrgb&w=900','pink'],
                        ['Pie equino varo congénito','Acompañamiento en su tratamiento y desarrollo funcional.','https://images.pexels.com/photos/30483062/pexels-photo-30483062.jpeg?auto=compress&cs=tinysrgb&w=900','lime'],
                    ];
                    $visuals=['cyan'=>['soft'=>'bg-cyan-100','ink'=>'text-cyan-600','bar'=>'bg-cyan-400'],'pink'=>['soft'=>'bg-pink-100','ink'=>'text-pink-600','bar'=>'bg-pink-500'],'lime'=>['soft'=>'bg-lime-100','ink'=>'text-lime-600','bar'=>'bg-lime-500'],'yellow'=>['soft'=>'bg-yellow-100','ink'=>'text-amber-500','bar'=>'bg-yellow-400'],'violet'=>['soft'=>'bg-violet-100','ink'=>'text-violet-500','bar'=>'bg-violet-400']];
                @endphp

                <div class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($conditions as [$condition,$description,$image,$color])
                        <article data-reveal="up" class="group relative min-h-[260px] overflow-hidden rounded-[1.75rem] border border-slate-100 bg-white shadow-[0_12px_38px_rgba(15,23,42,.08)] transition duration-500 hover:-translate-y-2 hover:shadow-[0_22px_55px_rgba(15,23,42,.14)]">
                            <img src="{{ $image }}" alt="" loading="lazy" decoding="async" width="1200" height="800" class="absolute inset-y-0 right-0 h-full w-[62%] object-cover object-center transition duration-700 group-hover:scale-105">
                            <div aria-hidden="true" class="absolute inset-y-0 left-[34%] w-[34%] bg-gradient-to-r from-white via-white/95 to-transparent"></div>
                            <div class="relative z-10 flex min-h-[260px] w-[58%] flex-col p-6">
                                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-full {{ $visuals[$color]['soft'] }} {{ $visuals[$color]['ink'] }}">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 0-4 4v1a3 3 0 0 0-2 3v2a3 3 0 0 0 3 3h1v4h4v-4h1a3 3 0 0 0 3-3v-2a3 3 0 0 0-2-3V7a4 4 0 0 0-4-4Z"/><path d="M9 9h6M9 13h6"/></svg>
                                </div>
                                <h3 class="text-lg font-extrabold leading-6 text-slate-800">{{ $condition }}</h3>
                                <p class="mt-3 text-sm leading-5 text-slate-600">{{ $description }}</p>
                                <span aria-hidden="true" class="mt-auto block h-1 w-9 rounded-full {{ $visuals[$color]['bar'] }}"></span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div data-reveal="right" class="relative mt-8 overflow-hidden rounded-[2rem] bg-gradient-to-r from-cyan-50 via-white to-pink-50 shadow-[0_16px_50px_rgba(15,23,42,.08)] ring-1 ring-slate-100">
                    <div aria-hidden="true" class="absolute -right-8 -bottom-12 h-40 w-40 rounded-full bg-cyan-100/80"></div>
                    <div class="grid items-stretch lg:grid-cols-[.78fr_1.22fr]">
                        <div class="min-h-48 overflow-hidden lg:min-h-56">
                            <img src="https://images.pexels.com/photos/7141030/pexels-photo-7141030.jpeg?auto=compress&cs=tinysrgb&w=1200" alt="" loading="lazy" decoding="async" width="1200" height="800" class="h-full w-full object-cover">
                        </div>
                        <div class="relative z-10 grid items-center gap-6 px-7 py-8 sm:px-9 lg:grid-cols-[1fr_auto]">
                            <div><p class="text-sm font-extrabold uppercase tracking-[0.16em] text-cyan-700">Estamos aquí para orientarte</p><h3 class="mt-2 text-2xl font-black tracking-[-0.03em] text-slate-800 sm:text-3xl">No es necesario contar con un <span class="text-pink-600">diagnóstico previo.</span></h3><p class="mt-3 max-w-xl text-base leading-7 text-slate-600">Si tienes dudas sobre el desarrollo de tu hijo, puedes solicitar una valoración con nuestro equipo.</p></div>
                            <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-full bg-pink-600 px-7 font-bold text-white shadow-lg shadow-pink-200/60 transition hover:-translate-y-1 hover:bg-pink-700 focus:outline-none focus:ring-4 focus:ring-pink-300/40">Solicita una valoración <span aria-hidden="true" class="ml-2">→</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="terapias" class="scroll-mt-28 relative overflow-hidden bg-slate-50 py-20 sm:py-24 lg:py-28">
            <div aria-hidden="true" class="pointer-events-none absolute -right-28 top-10 h-80 w-80 rounded-full bg-yellow-100/70 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -left-24 bottom-12 h-72 w-72 rounded-full bg-cyan-100/60 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                    <div data-reveal="left">
                        <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-pink-600 sm:text-sm">
                            <span class="h-1 w-8 rounded-full bg-lime-400"></span>Terapias y métodos
                        </div>
                        <h2 class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">
                            Un abordaje pensado para <span class="text-cyan-600">cada niño.</span>
                        </h2>
                    </div>
                    <div data-reveal="right" class="lg:pb-1">
                        <p class="max-w-2xl text-lg leading-8 text-slate-600">
                            La selección de terapias se basa en los <strong class="font-bold text-slate-800">objetivos individuales de cada paciente</strong>. Después de la valoración se establecen los objetivos terapéuticos y las terapias que se llevarán a cabo.
                        </p>
                    </div>
                </div>

                @php
                    $therapies = [
                        ['Método Vojta','Favorece la aparición de movimientos coordinados y organizados mediante posturas y la estimulación de zonas determinadas del cuerpo.','cyan','V'],
                        ['PediaSuit / PediaSuit Therapy','Método de rehabilitación neuromotora intensiva que utiliza un traje ortopédico dinámico y propioceptivo durante el trabajo activo.','pink','P'],
                        ['Programa de bipedestación','Estrategia terapéutica utilizada principalmente para prevenir alteraciones músculo-esqueléticas en pacientes con alteraciones del desarrollo motor.','lime','B'],
                        ['Estimulación temprana',null,'yellow','E'],
                        ['Terapia orofacial',null,'violet','O'],
                        ['Entrenamiento de marcha',null,'cyan','M'],
                        ['Terapia tridimensional del pie',null,'pink','T'],
                    ];
                    $therapyStyles = [
                        'cyan'=>['soft'=>'bg-cyan-50','ink'=>'text-cyan-600','line'=>'bg-cyan-400'],
                        'pink'=>['soft'=>'bg-pink-50','ink'=>'text-pink-600','line'=>'bg-pink-500'],
                        'lime'=>['soft'=>'bg-lime-50','ink'=>'text-lime-600','line'=>'bg-lime-500'],
                        'yellow'=>['soft'=>'bg-yellow-50','ink'=>'text-amber-500','line'=>'bg-yellow-400'],
                        'violet'=>['soft'=>'bg-violet-50','ink'=>'text-violet-500','line'=>'bg-violet-400'],
                    ];
                @endphp

                <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($therapies as [$therapy,$description,$color,$initial])
                        <article data-reveal="up" class="group relative overflow-hidden rounded-[2rem] border border-white bg-white p-7 shadow-[0_14px_45px_rgba(15,23,42,.06)] transition duration-500 hover:-translate-y-2 hover:shadow-[0_22px_55px_rgba(15,23,42,.11)]">
                            <div aria-hidden="true" class="absolute -right-10 -top-10 h-32 w-32 rounded-full {{ $therapyStyles[$color]['soft'] }} transition duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-xl font-black {{ $therapyStyles[$color]['soft'] }} {{ $therapyStyles[$color]['ink'] }}">{{ $initial }}</div>
                                <h3 class="mt-6 text-xl font-extrabold leading-7 text-slate-800">{{ $therapy }}</h3>
                                @if ($description)
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                                @else
                                    <p class="mt-3 text-sm leading-6 text-slate-500">Disponible dentro del abordaje terapéutico de URPE según los objetivos definidos en la valoración.</p>
                                @endif
                                <span aria-hidden="true" class="mt-6 block h-1 w-10 rounded-full {{ $therapyStyles[$color]['line'] }}"></span>
                            </div>
                        </article>
                    @endforeach

                    <article data-reveal="up" class="relative flex min-h-64 flex-col justify-between overflow-hidden rounded-[2rem] bg-slate-800 p-7 text-white shadow-xl shadow-slate-200/60">
                        <div aria-hidden="true" class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-cyan-400/20"></div>
                        <div aria-hidden="true" class="absolute right-10 top-10 h-5 w-5 rounded-full bg-yellow-400"></div>
                        <div class="relative">
                            <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-cyan-300">¿Por dónde empezar?</p>
                            <h3 class="mt-4 text-2xl font-black leading-tight">Primero conocemos las necesidades de tu hijo.</h3>
                            <p class="mt-4 text-sm leading-6 text-slate-300">La valoración inicial permite establecer los objetivos terapéuticos y definir el abordaje.</p>
                        </div>
                        <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="relative mt-7 inline-flex w-fit items-center gap-2 font-bold text-white transition hover:gap-3">
                            Solicita una valoración <span aria-hidden="true">→</span>
                        </a>
                    </article>
                </div>
            </div>
        </section>


        <section class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28" aria-labelledby="proceso-title">
            <div aria-hidden="true" class="pointer-events-none absolute left-1/2 top-0 h-64 w-64 -translate-x-1/2 rounded-full bg-cyan-100/50 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div data-reveal="up" class="mx-auto max-w-3xl text-center">
                    <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-700 sm:text-sm">
                        <span class="h-1 w-8 rounded-full bg-yellow-400"></span>Nuestra forma de acompañar
                    </div>
                    <h2 id="proceso-title" class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">Así acompañamos a <span class="text-pink-600">tu hijo.</span></h2>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">Cada proceso parte de una valoración y de objetivos definidos de manera individual para cada paciente.</p>
                </div>

                @php
                    $steps = [
                        ['01','Valoramos','Conocemos las necesidades de tu hijo mediante una valoración inicial.','cyan'],
                        ['02','Definimos objetivos','Establecemos los objetivos terapéuticos de acuerdo con sus necesidades.','pink'],
                        ['03','Diseñamos el abordaje','Seleccionamos las terapias que se llevarán a cabo según los objetivos individuales.','lime'],
                        ['04','Acompañamos la evolución','Trabajamos de manera cercana con el niño y su familia durante su proceso terapéutico.','yellow'],
                    ];
                    $stepStyles = [
                        'cyan'=>['number'=>'text-cyan-600','dot'=>'bg-cyan-400','soft'=>'bg-cyan-50'],
                        'pink'=>['number'=>'text-pink-600','dot'=>'bg-pink-500','soft'=>'bg-pink-50'],
                        'lime'=>['number'=>'text-lime-600','dot'=>'bg-lime-500','soft'=>'bg-lime-50'],
                        'yellow'=>['number'=>'text-amber-500','dot'=>'bg-yellow-400','soft'=>'bg-yellow-50'],
                    ];
                @endphp

                <div class="relative mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <div aria-hidden="true" class="absolute left-[12.5%] right-[12.5%] top-10 hidden h-px bg-gradient-to-r from-cyan-200 via-pink-200 to-yellow-200 lg:block"></div>
                    @foreach ($steps as [$number,$title,$description,$color])
                        <article data-reveal="up" class="group relative rounded-[2rem] border border-slate-100 bg-white p-6 shadow-[0_14px_45px_rgba(15,23,42,.06)] transition duration-500 hover:-translate-y-2 hover:shadow-[0_22px_55px_rgba(15,23,42,.11)]">
                            <div class="relative z-10 flex items-center justify-between">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full {{ $stepStyles[$color]['soft'] }} ring-8 ring-white">
                                    <span class="text-2xl font-black {{ $stepStyles[$color]['number'] }}">{{ $number }}</span>
                                </div>
                                <span aria-hidden="true" class="h-3 w-3 rounded-full {{ $stepStyles[$color]['dot'] }}"></span>
                            </div>
                            <h3 class="mt-7 text-xl font-extrabold text-slate-800">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
                            <span aria-hidden="true" class="mt-6 block h-1 w-10 rounded-full {{ $stepStyles[$color]['dot'] }}"></span>
                        </article>
                    @endforeach
                </div>

                <div data-reveal="up" class="mx-auto mt-10 flex max-w-3xl flex-col items-center justify-between gap-5 rounded-[2rem] bg-gradient-to-r from-cyan-50 via-white to-pink-50 px-7 py-6 text-center shadow-[0_12px_40px_rgba(15,23,42,.05)] sm:flex-row sm:text-left">
                    <p class="font-semibold leading-7 text-slate-700">¿Tienes dudas sobre el desarrollo de tu hijo? <span class="font-extrabold text-slate-900">No necesitas contar con un diagnóstico previo.</span></p>
                    <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="shrink-0 font-extrabold text-pink-600 transition hover:text-pink-700">Solicita una valoración →</a>
                </div>
            </div>
        </section>

        <section id="equipo" class="scroll-mt-28 relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28">
            <div aria-hidden="true" class="pointer-events-none absolute -left-20 top-24 h-64 w-64 rounded-full bg-pink-100/50 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-20 bottom-10 h-72 w-72 rounded-full bg-cyan-100/60 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div data-reveal="left" class="mx-auto max-w-3xl text-center">
                    <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-700 sm:text-sm"><span class="h-1 w-8 rounded-full bg-pink-500"></span>Nuestro equipo</div>
                    <h2 class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">Profesionales que <span class="text-pink-600">acompañan de cerca.</span></h2>
                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600">En URPE el acompañamiento cercano forma parte del trabajo con cada niño y su familia.</p>
                </div>

                @php
                    $team = [
                        ['Jonathan Escobar Zambrano','Director de URPE','J','cyan'],
                        ['Fredi Carpio Jiménez','Encargado del área de PediaSuit','F','pink'],
                        ['Mauricio Ballinas Ramírez','Encargado del área de marcha y programa de bipedestación','M','lime'],
                    ];
                    $teamStyles = [
                        'cyan'=>['soft'=>'from-cyan-100 to-cyan-50','ink'=>'text-cyan-600','dot'=>'bg-cyan-400'],
                        'pink'=>['soft'=>'from-pink-100 to-pink-50','ink'=>'text-pink-600','dot'=>'bg-pink-500'],
                        'lime'=>['soft'=>'from-lime-100 to-lime-50','ink'=>'text-lime-600','dot'=>'bg-lime-500'],
                    ];
                @endphp

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @foreach ($team as [$name,$role,$initial,$color])
                        <article data-reveal="up" class="group overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-[0_14px_45px_rgba(15,23,42,.07)] transition duration-500 hover:-translate-y-2 hover:shadow-[0_22px_55px_rgba(15,23,42,.12)]">
                            <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-gradient-to-br {{ $teamStyles[$color]['soft'] }}">
                                <div aria-hidden="true" class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/50"></div>
                                <div aria-hidden="true" class="absolute bottom-8 left-8 h-4 w-4 rounded-full {{ $teamStyles[$color]['dot'] }}"></div>
                                <div class="relative flex h-28 w-28 items-center justify-center rounded-full bg-white/90 text-5xl font-black shadow-sm {{ $teamStyles[$color]['ink'] }}">{{ $initial }}</div>
                                <span class="absolute bottom-4 right-5 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-500 shadow-sm">Foto próximamente</span>
                            </div>
                            <div class="p-7">
                                <h3 class="text-xl font-extrabold leading-7 text-slate-800">{{ $name }}</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 {{ $teamStyles[$color]['ink'] }}">{{ $role }}</p>
                                <span aria-hidden="true" class="mt-6 block h-1 w-10 rounded-full {{ $teamStyles[$color]['dot'] }}"></span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div data-reveal="right" class="mt-10 grid gap-5 rounded-[2rem] bg-slate-800 px-7 py-8 text-white shadow-xl shadow-slate-200/60 sm:px-9 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-lime-300">Atención especializada</p>
                        <h3 class="mt-2 text-2xl font-black tracking-[-0.03em] sm:text-3xl">Un equipo enfocado en el desarrollo de cada niño.</h3>
                        <p class="mt-3 max-w-2xl text-base leading-7 text-slate-300">URPE trabaja con atención especializada y un acompañamiento cercano a las familias durante el proceso terapéutico.</p>
                    </div>
                    <a href="#contacto" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-full bg-white px-7 font-bold text-slate-800 transition hover:-translate-y-1 hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-white/30">Contacta con URPE <span aria-hidden="true" class="ml-2">→</span></a>
                </div>
            </div>
        </section>


        <section class="relative overflow-hidden bg-white py-20 sm:py-24 lg:py-28" aria-labelledby="faq-title">
            <div aria-hidden="true" class="pointer-events-none absolute -right-24 top-20 h-72 w-72 rounded-full bg-lime-100/55 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div class="grid gap-10 lg:grid-cols-[.72fr_1.28fr] lg:gap-16">
                    <div data-reveal="left" class="lg:sticky lg:top-32 lg:self-start">
                        <div class="mb-5 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-pink-600 sm:text-sm"><span class="h-1 w-8 rounded-full bg-cyan-400"></span>Preguntas frecuentes</div>
                        <h2 id="faq-title" class="text-4xl font-black leading-tight tracking-[-0.04em] text-slate-800 sm:text-5xl">Antes de comenzar, es normal tener <span class="text-cyan-600">preguntas.</span></h2>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">Aquí reunimos información útil para dar el primer paso. Si necesitas orientación sobre un caso particular, puedes escribir directamente a URPE.</p>
                        <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20sobre%20URPE." target="_blank" rel="noopener noreferrer" class="mt-7 inline-flex items-center font-extrabold text-pink-600 transition hover:text-pink-700">Tengo otra pregunta <span aria-hidden="true" class="ml-2">→</span></a>
                    </div>

                    <div data-reveal="right" class="space-y-4">
                        @php
                            $faqs = [
                                ['¿Necesito contar con un diagnóstico previo?','No. Puedes solicitar una valoración aunque todavía no cuentes con un diagnóstico previo.'],
                                ['¿Qué edades atiende URPE?','URPE brinda atención desde el nacimiento hasta los 10 años.'],
                                ['¿Cómo inicio el proceso?','Puedes enviar un mensaje por WhatsApp para solicitar información y agendar una valoración inicial.'],
                                ['¿Qué debo llevar a la primera valoración?','Todos los estudios que hayan realizado, como resonancias magnéticas, electroencefalograma, radiografías u otros estudios relacionados.'],
                                ['¿Cómo se eligen las terapias?','Después de la valoración se establecen los objetivos terapéuticos. La selección de terapias se basa en los objetivos individuales de cada paciente.'],
                                ['¿Dónde se encuentra URPE?','En 15 norte poniente esquina Río Chancalá #105, Fraccionamiento Miramar.'],
                                ['¿Cuál es el horario de atención?','Lunes a viernes de 9:00 a 18:00 y sábados de 8:00 a 14:00.'],
                            ];
                        @endphp
                        @foreach ($faqs as $index => [$question,$answer])
                            <details class="group overflow-hidden rounded-[1.6rem] border border-slate-100 bg-slate-50/70 transition open:bg-white open:shadow-[0_14px_45px_rgba(15,23,42,.07)]">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-5 px-6 py-5 font-extrabold text-slate-800 [&::-webkit-details-marker]:hidden">
                                    <span>{{ $question }}</span>
                                    <span aria-hidden="true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-xl text-cyan-600 shadow-sm transition duration-300 group-open:rotate-45">+</span>
                                </summary>
                                <div class="px-6 pb-6 pr-16 text-sm leading-7 text-slate-600">{{ $answer }}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-white px-5 py-8 sm:px-8 lg:px-10 lg:py-12" aria-label="Invitación a solicitar una valoración">
            <div data-reveal="up" class="relative mx-auto max-w-7xl overflow-hidden rounded-[2.5rem] bg-slate-800 px-7 py-12 text-white shadow-[0_24px_70px_rgba(15,23,42,.18)] sm:px-10 sm:py-14 lg:px-16 lg:py-16">
                <div aria-hidden="true" class="absolute -left-20 -top-24 h-72 w-72 rounded-full bg-cyan-400/20 blur-2xl"></div>
                <div aria-hidden="true" class="absolute -bottom-28 right-8 h-80 w-80 rounded-full bg-pink-500/20 blur-2xl"></div>
                <div aria-hidden="true" class="absolute right-[8%] top-10 h-6 w-6 rounded-full bg-yellow-400"></div>
                <div aria-hidden="true" class="absolute bottom-12 left-[8%] h-4 w-4 rounded-full bg-lime-400"></div>
                <div aria-hidden="true" class="absolute left-[4%] top-1/2 hidden h-3 w-3 rounded-full bg-pink-400 sm:block"></div>

                <div class="relative mx-auto max-w-4xl text-center">
                    <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="" class="mx-auto mb-7 h-auto w-28 rounded-xl bg-white/95 p-2 shadow-lg shadow-slate-950/20 sm:w-32">
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-cyan-300 sm:text-sm">URPE · Unidad de Rehabilitación Pediátrica Evolutiva</p>
                    <h2 class="mt-5 text-3xl font-black leading-tight tracking-[-0.04em] sm:text-4xl lg:text-5xl">
                        “Acompañamos con <span class="text-pink-400">amor</span>, profesionalismo y conocimiento el <span class="text-cyan-300">neurodesarrollo de tu hijo.</span>”
                    </h2>
                    <p class="mx-auto mt-6 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">Si tienes dudas sobre su desarrollo, el primer paso puede ser una valoración con el equipo de URPE.</p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="inline-flex min-h-13 items-center justify-center rounded-full bg-pink-600 px-8 font-extrabold text-white shadow-lg shadow-pink-950/25 transition hover:-translate-y-1 hover:bg-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-300/30">Solicita una valoración <span aria-hidden="true" class="ml-2">→</span></a>
                        <a href="tel:+529616508708" class="inline-flex min-h-13 items-center justify-center rounded-full border border-white/20 bg-white/10 px-7 font-bold text-white backdrop-blur transition hover:-translate-y-1 hover:bg-white/15">961 650 8708</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto" class="scroll-mt-28 relative overflow-hidden bg-slate-50 py-20 sm:py-24 lg:py-28">
            <div aria-hidden="true" class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-yellow-100/60 blur-3xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-24 top-12 h-80 w-80 rounded-full bg-pink-100/50 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
                <div class="grid gap-8 lg:grid-cols-[.92fr_1.08fr] lg:items-stretch">
                    <div data-reveal="left" class="rounded-[2.25rem] bg-slate-800 p-8 text-white shadow-xl shadow-slate-200/70 sm:p-10">
                        <div class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-300 sm:text-sm"><span class="h-1 w-8 rounded-full bg-yellow-400"></span>Contacto</div>
                        <h2 class="mt-5 text-4xl font-black leading-tight tracking-[-0.04em] sm:text-5xl">Estamos para <span class="text-pink-400">acompañarte.</span></h2>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Si tienes dudas o deseas solicitar una valoración, puedes comunicarte directamente con URPE.</p>

                        <div class="mt-10 space-y-5">
                            <div class="flex gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-cyan-400/15 text-cyan-300" aria-hidden="true">⌖</span>
                                <div><p class="text-xs font-extrabold uppercase tracking-[.14em] text-slate-400">Dirección</p><p class="mt-1 leading-6 text-white">15 norte poniente esquina Río Chancalá #105, Fraccionamiento Miramar</p></div>
                            </div>
                            <div class="flex gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-pink-400/15 text-pink-300" aria-hidden="true">☎</span>
                                <div><p class="text-xs font-extrabold uppercase tracking-[.14em] text-slate-400">Teléfono y WhatsApp</p><a href="tel:+529616508708" class="mt-1 inline-block font-bold text-white transition hover:text-cyan-300">+52 961 650 8708</a></div>
                            </div>
                            <div class="flex gap-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-lime-400/15 text-lime-300" aria-hidden="true">✉</span>
                                <div><p class="text-xs font-extrabold uppercase tracking-[.14em] text-slate-400">Correo</p><a href="mailto:joez_sk@hotmail.com" class="mt-1 inline-block break-all font-bold text-white transition hover:text-cyan-300">joez_sk@hotmail.com</a></div>
                            </div>
                        </div>

                        <a href="https://wa.me/529616508708?text=Hola%2C%20me%20gustar%C3%ADa%20solicitar%20informaci%C3%B3n%20para%20una%20valoraci%C3%B3n%20en%20URPE." target="_blank" rel="noopener noreferrer" class="mt-10 inline-flex min-h-13 items-center justify-center rounded-full bg-pink-600 px-7 font-bold text-white shadow-lg shadow-pink-950/20 transition hover:-translate-y-1 hover:bg-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-300/30">Escríbenos por WhatsApp <span aria-hidden="true" class="ml-2">→</span></a>
                    </div>

                    <div data-reveal="right" class="grid gap-5 sm:grid-cols-2">
                        <article class="rounded-[2rem] border border-white bg-white p-7 shadow-[0_14px_45px_rgba(15,23,42,.06)]">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-50 text-2xl" aria-hidden="true">☀</div>
                            <p class="mt-6 text-xs font-extrabold uppercase tracking-[.16em] text-amber-500">Lunes a viernes</p>
                            <p class="mt-2 text-3xl font-black tracking-[-0.03em] text-slate-800">9:00–18:00</p>
                            <p class="mt-3 text-sm leading-6 text-slate-500">Horario de atención.</p>
                        </article>
                        <article class="rounded-[2rem] border border-white bg-white p-7 shadow-[0_14px_45px_rgba(15,23,42,.06)]">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-50 text-2xl" aria-hidden="true">◷</div>
                            <p class="mt-6 text-xs font-extrabold uppercase tracking-[.16em] text-cyan-600">Sábados</p>
                            <p class="mt-2 text-3xl font-black tracking-[-0.03em] text-slate-800">8:00–14:00</p>
                            <p class="mt-3 text-sm leading-6 text-slate-500">Horario de atención.</p>
                        </article>

                        <article class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-cyan-50 via-white to-lime-50 p-7 shadow-[0_14px_45px_rgba(15,23,42,.06)] sm:col-span-2">
                            <div aria-hidden="true" class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-pink-100/70"></div>
                            <div class="relative">
                                <p class="text-xs font-extrabold uppercase tracking-[.16em] text-pink-600">Tu primera visita</p>
                                <h3 class="mt-3 text-2xl font-black tracking-[-0.03em] text-slate-800">¿Qué debo llevar a la valoración?</h3>
                                <p class="mt-4 max-w-2xl leading-7 text-slate-600">Todos los estudios que hayan realizado, como resonancias magnéticas, electroencefalograma, radiografías u otros estudios relacionados.</p>
                                <p class="mt-5 text-sm font-semibold text-slate-700">Después de la valoración se establecen los objetivos terapéuticos y las terapias que se llevarán a cabo.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-8 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE - Unidad de Rehabilitación Pediátrica Evolutiva" class="h-12 w-auto object-contain">
                <p class="text-sm text-slate-500">Unidad de Rehabilitación Pediátrica Evolutiva · Tuxtla Gutiérrez, Chiapas</p>
                <a href="#inicio" class="text-sm font-bold text-cyan-700 transition hover:text-pink-600">Volver arriba ↑</a>
            </div>
        </footer>

        <button id="urpe-back-to-top" type="button" aria-label="Volver arriba" title="Volver arriba" class="pointer-events-none fixed bottom-5 right-5 z-40 flex h-12 w-12 translate-y-4 items-center justify-center rounded-full bg-slate-800 text-white opacity-0 shadow-[0_12px_35px_rgba(15,23,42,.22)] transition duration-300 hover:-translate-y-1 hover:bg-pink-600 focus:outline-none focus:ring-4 focus:ring-pink-200 sm:bottom-7 sm:right-7 sm:h-14 sm:w-14"><svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 14l6-6 6 6"/></svg></button>
    </main>
</div>
</body>
</html>
