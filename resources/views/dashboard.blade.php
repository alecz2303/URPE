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
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">URPE</p>
                <h1 class="text-xl font-bold">Gestión Clínica</h1>
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
            <p class="mt-4 max-w-2xl text-cyan-50/90">La autenticación de URPE Gestión Clínica está funcionando correctamente. Los módulos operativos se incorporarán de forma progresiva según el Roadmap.</p>
        </section>

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
                <p class="mt-2 text-sm leading-6 text-slate-500">El acceso al dashboard requiere autenticación activa.</p>
            </article>
        </section>
    </main>
</body>
</html>
