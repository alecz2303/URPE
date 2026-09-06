<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <div class="flex min-w-0 items-center gap-4">
                <img
                    src="{{ asset('images/brand/urpe-logo.png') }}"
                    alt="URPE - Unidad de Rehabilitación Pediátrica Evolutiva y Fisioterapia Infantil"
                    class="h-14 w-auto max-w-[190px] object-contain sm:h-16 sm:max-w-[240px]"
                >
                <div class="hidden border-l border-slate-200 pl-4 md:block">
                    <h1 class="text-lg font-bold text-slate-900">Gestión Clínica</h1>
                    <p class="text-xs text-slate-500">Sistema de gestión clínica</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">Sesión activa</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        <section class="rounded-3xl bg-gradient-to-r from-cyan-700 to-teal-700 p-8 text-white shadow-lg shadow-cyan-900/10 sm:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-100">Acceso confirmado</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Bienvenido, {{ auth()->user()->name }}.</h2>
            <p class="mt-4 max-w-2xl text-cyan-50/90">URPE Gestión Clínica ya cuenta con autenticación y autorización granular. Los módulos clínicos se incorporarán de forma progresiva según el Roadmap.</p>
        </section>

        @if(auth()->user()->can('users.view') || auth()->user()->can('roles.view') || auth()->user()->can('center.manage') || auth()->user()->can('therapists.manage'))
            <section class="mt-8">
                <div class="mb-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Administración</p>
                    <h3 class="mt-1 text-xl font-bold">Seguridad y configuración</h3>
                </div>
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @can('users.view')
                        <a href="{{ route('users.index') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-cyan-300">
                            <p class="text-sm font-semibold text-cyan-700">Usuarios</p>
                            <h4 class="mt-2 text-lg font-bold">Administrar cuentas</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Consulta usuarios, roles asignados y estado de acceso.</p>
                        </a>
                    @endcan

                    @can('roles.view')
                        <a href="{{ route('roles.index') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-cyan-300">
                            <p class="text-sm font-semibold text-cyan-700">Roles y permisos</p>
                            <h4 class="mt-2 text-lg font-bold">Configurar autorización</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Revisa los roles del sistema y los permisos efectivos de cada uno.</p>
                        </a>
                    @endcan

                    @can('center.manage')
                        <a href="{{ route('center.edit') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-cyan-300">
                            <p class="text-sm font-semibold text-cyan-700">Centro</p>
                            <h4 class="mt-2 text-lg font-bold">Configuración y horarios</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Administra datos operativos y el horario semanal que usará la futura agenda.</p>
                        </a>
                    @endcan

                    @can('therapists.manage')
                        <a href="{{ route('therapists.index') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:ring-cyan-300">
                            <p class="text-sm font-semibold text-cyan-700">Terapeutas</p>
                            <h4 class="mt-2 text-lg font-bold">Perfiles y disponibilidad</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Gestiona terapeutas, horarios semanales, ausencias y bloqueos operativos.</p>
                        </a>
                    @endcan
                </div>
            </section>
        @endif

        <section class="mt-8 grid gap-5 md:grid-cols-3">
            <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-cyan-700">Agenda</p>
                <h3 class="mt-2 text-lg font-bold">Próximamente</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Calendario clínico, disponibilidad y citas.</p>
            </article>

            <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-cyan-700">Pacientes</p>
                <h3 class="mt-2 text-lg font-bold">Próximamente</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Expediente clínico, responsables y seguimiento.</p>
            </article>

            <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-cyan-700">Seguridad</p>
                <h3 class="mt-2 text-lg font-bold">Sesión protegida</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">El acceso depende de autenticación activa y permisos granulares.</p>
            </article>
        </section>
    </main>
</body>
</html>
