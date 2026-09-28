<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar contraseña — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="mx-auto mb-6 h-auto w-full max-w-xs object-contain">
                <p class="text-sm font-semibold text-cyan-700">Acceso protegido</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight">Define una nueva contraseña</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">La contraseña temporal solo sirve para recuperar el acceso. Antes de continuar en URPE debes reemplazarla.</p>
            </div>

            <div class="rounded-3xl bg-white p-7 shadow-xl shadow-slate-200/70 ring-1 ring-slate-200 sm:p-9">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Nueva contraseña</label>
                        <input id="password" name="password" type="password" required autofocus autocomplete="new-password" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                        @error('password')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirmar nueva contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Guardar nueva contraseña</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-5 text-center">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Cerrar sesión</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
