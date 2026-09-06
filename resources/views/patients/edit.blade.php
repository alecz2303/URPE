<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar paciente — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Editar paciente</h1>
            </div>
        </div>
        <a href="{{ route('patients.show', $patient) }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver a ficha</a>
    </div>
</header>

<main class="mx-auto max-w-5xl px-6 py-10 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">{{ $patient->folio }}</p>
        <h2 class="mt-1 text-3xl font-bold">{{ $patient->full_name }}</h2>
    </div>

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        @csrf
        @method('PUT')
        @include('patients._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Guardar cambios</button>
        </div>
    </form>
</main>

<x-sweet-alerts />
</body>
</html>
