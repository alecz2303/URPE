<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="hidden bg-gradient-to-br from-sky-700 via-cyan-700 to-teal-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <div class="mb-10 max-w-md rounded-3xl bg-white p-5 shadow-lg shadow-cyan-950/10 ring-1 ring-white/30">
                    <img
                        src="{{ asset('images/brand/urpe-logo.png') }}"
                        alt="URPE - Unidad de Rehabilitación Pediátrica Evolutiva y Fisioterapia Infantil"
                        class="h-auto w-full object-contain"
                    >
                </div>
                <h1 class="max-w-xl text-5xl font-bold leading-tight">Gestión Clínica</h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-cyan-50/90">Plataforma clínica para organizar la operación, la agenda y el seguimiento de pacientes de forma segura.</p>
            </div>
            <p class="text-sm text-cyan-100/80">Acceso exclusivo para personal autorizado.</p>
        </section>

        <section class="flex items-center justify-center px-6 py-12 sm:px-10 lg:px-16">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <img
                        src="{{ asset('images/brand/urpe-logo.png') }}"
                        alt="URPE - Unidad de Rehabilitación Pediátrica Evolutiva y Fisioterapia Infantil"
                        class="mb-5 h-auto w-full max-w-xs object-contain"
                    >
                    <h1 class="text-3xl font-bold">Gestión Clínica</h1>
                </div>

                <div class="rounded-3xl bg-white p-7 shadow-xl shadow-slate-200/70 ring-1 ring-slate-200 sm:p-9">
                    <div class="mb-8">
                        <p class="text-sm font-medium text-cyan-700">Bienvenido</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight">Iniciar sesión</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Ingresa tus credenciales para acceder al sistema.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                                placeholder="usuario@urpe.mx"
                            >
                            @error('email')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Contraseña</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100"
                                placeholder="••••••••"
                            >
                            @error('password')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                            Entrar al sistema
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs leading-5 text-slate-500">URPE Gestión Clínica · La información del sistema es de uso restringido.</p>
            </div>
        </section>
    </main>
</body>
</html>
