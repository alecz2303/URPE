<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo terapeuta — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Nuevo terapeuta</h1>
            </div>
        </div>
        <a href="{{ route('therapists.index') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver a terapeutas</a>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <form method="POST" action="{{ route('therapists.store') }}"
          data-swal-confirm
          data-swal-title="¿Crear terapeuta?"
          data-swal-text="Se guardará el perfil y su disponibilidad semanal."
          data-swal-confirm-text="Sí, crear">
        @csrf
        @include('therapists._form')

        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">Crear terapeuta</button>
        </div>
    </form>
</main>

<x-sweet-alerts />
</body>
</html>
