@props(['title', 'eyebrow' => null])
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-cyan-50/70 via-white to-pink-50/70 text-slate-900 antialiased">
<div class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
    <div id="mobile-nav-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    <aside id="primary-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-[min(86vw,300px)] -translate-x-full flex-col overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-cyan-950 text-slate-200 shadow-2xl transition-transform duration-200 lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-auto lg:translate-x-0 lg:border-r lg:border-cyan-900/40 lg:shadow-none" aria-label="Navegación principal">
        <div class="pointer-events-none absolute -left-20 bottom-16 h-40 w-40 rounded-full bg-pink-500/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -right-16 top-16 h-36 w-36 rounded-full bg-cyan-400/10 blur-2xl"></div>
        <div class="relative flex h-full flex-col">
            <div class="flex items-center justify-between gap-4 px-5 py-5">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white p-1.5 shadow-lg shadow-cyan-950/30 ring-1 ring-white/20"><img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="max-h-full max-w-full object-contain"></span>
                    <span><strong class="block text-sm text-white">URPE</strong><span class="text-xs text-cyan-100/70">Gestión Clínica</span></span>
                </a>
                <button type="button" data-close-mobile-nav class="grid h-10 w-10 place-items-center rounded-xl text-xl text-slate-300 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Cerrar menú">×</button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 pb-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-pink-500 to-fuchsia-500 text-white shadow-lg shadow-pink-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>⌂</span> Inicio</a>

                @can('appointments.view')
                    <a href="{{ route('appointments.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('appointments.*') ? 'bg-gradient-to-r from-cyan-500 to-sky-500 text-white shadow-lg shadow-cyan-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>◷</span> Agenda</a>
                @endcan

                @can('patients.view')
                    <a href="{{ route('patients.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('patients.*') || request()->routeIs('clinical-records.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>♙</span> Pacientes</a>
                @endcan

                @can('session_logs.view')
                    <a href="{{ route('session-logs.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('session-logs.*') ? 'bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white shadow-lg shadow-violet-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>✎</span> Sesiones clínicas</a>
                @endcan

                <p class="mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-200/50">Operación clínica</p>

                @can('therapists.manage')
                    <a href="{{ route('therapists.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('therapists.*') ? 'bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>◉</span> Terapeutas</a>
                @endcan

                @can('therapies.manage')
                    <a href="{{ route('therapies.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('therapies.*') ? 'bg-gradient-to-r from-amber-400 to-orange-400 text-slate-950 shadow-lg shadow-amber-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>✦</span> Terapias</a>
                @endcan

                <p class="mt-5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-pink-200/50">Administración</p>

                @can('center.manage')
                    <a href="{{ route('center.edit') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('center.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>⚙</span> Centro y horarios</a>
                @endcan

                @can('users.view')
                    <a href="{{ route('users.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('users.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>♟</span> Usuarios</a>
                @endcan

                @can('roles.view')
                    <a href="{{ route('roles.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('roles.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"><span>☷</span> Roles y permisos</a>
                @endcan
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="rounded-2xl bg-white/5 p-3 ring-1 ring-white/10">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="w-full rounded-xl border border-white/10 px-3 py-2 text-left text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="min-w-0">
        <div class="sticky top-0 z-30 flex items-center justify-between border-b border-cyan-100 bg-white/90 px-4 py-3 backdrop-blur lg:hidden">
            <button type="button" data-open-mobile-nav class="inline-flex items-center gap-2 rounded-xl border border-cyan-100 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm" aria-controls="primary-sidebar" aria-expanded="false"><span class="text-lg leading-none text-cyan-600">☰</span> Menú</button>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-900">URPE <span class="font-medium text-cyan-500">· Gestión Clínica</span></a>
        </div>

        <header class="relative overflow-hidden border-b border-cyan-100 bg-white/85 px-5 py-5 backdrop-blur lg:px-8">
            <div class="pointer-events-none absolute -right-10 -top-16 h-36 w-36 rounded-full bg-pink-100/80"></div>
            <div class="pointer-events-none absolute right-20 -bottom-20 h-32 w-32 rounded-full bg-cyan-100/70"></div>
            <div class="relative mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-end sm:justify-between sm:gap-5">
                <div>
                    @if($eyebrow)
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-cyan-700">{{ $eyebrow }}</p>
                    @endif
                    <h1 class="{{ $eyebrow ? 'mt-1' : '' }} text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">{{ $title }}</h1>
                </div>
                @isset($actions)
                    <div class="flex flex-wrap items-center gap-2 sm:justify-end">{{ $actions }}</div>
                @endisset
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-5 lg:px-8 lg:py-8">{{ $slot }}</main>
    </div>
</div>
<x-sweet-alerts />
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('primary-sidebar');
        const backdrop = document.getElementById('mobile-nav-backdrop');
        const openButton = document.querySelector('[data-open-mobile-nav]');
        const closeButton = document.querySelector('[data-close-mobile-nav]');
        if (! sidebar || ! backdrop || ! openButton) return;

        const openNav = () => {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            openButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');
        };
        const closeNav = () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            openButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        };

        openButton.addEventListener('click', openNav);
        closeButton?.addEventListener('click', closeNav);
        backdrop.addEventListener('click', closeNav);
        document.addEventListener('keydown', event => { if (event.key === 'Escape') closeNav(); });
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) { backdrop.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); openButton.setAttribute('aria-expanded', 'false'); } });
    });
</script>
</body>
</html>
