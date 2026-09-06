<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar terapia — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Editar terapia</h1>
            </div>
        </div>
        <a href="{{ route('therapies.index') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver al catálogo</a>
    </div>
</header>

<main class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Catálogo clínico</p>
                <h2 class="mt-1 text-3xl font-bold">{{ $therapy->name }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Ajusta los valores que la futura agenda consultará para calcular duración y personal requerido.</p>
            </div>
            <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold {{ $therapy->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                {{ $therapy->is_active ? 'Activa' : 'Inactiva' }}
            </span>
        </div>

        <form method="POST" action="{{ route('therapies.update', $therapy) }}">
            @csrf
            @method('PUT')
            @include('therapies._form')
        </form>
    </section>
</main>

<x-sweet-alerts />
</body>
</html>
