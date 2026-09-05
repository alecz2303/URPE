<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roles y permisos — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">URPE</p>
            <h1 class="text-xl font-bold">Roles y permisos</h1>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver al dashboard</a>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div>
        <h2 class="text-3xl font-bold tracking-tight">Roles del sistema</h2>
        <p class="mt-2 text-sm text-slate-500">Consulta qué roles existen y cuántos permisos tiene cada uno.</p>
    </div>

    @if (session('status'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($roles as $role)
            <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Rol</p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ $role->name }}</h3>
                    </div>
                    @if($role->is_system)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Sistema</span>
                    @endif
                </div>

                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $role->description }}</p>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Usuarios</p>
                        <p class="mt-1 text-lg font-bold">{{ $role->users_count }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Permisos</p>
                        <p class="mt-1 text-lg font-bold">{{ $role->permissions_count }}</p>
                    </div>
                </div>

                @can('roles.manage')
                    <a href="{{ route('roles.edit', $role) }}" class="mt-5 inline-flex text-sm font-semibold text-cyan-700 hover:text-cyan-900">Administrar permisos →</a>
                @endcan
            </article>
        @endforeach
    </div>
</main>
</body>
</html>
