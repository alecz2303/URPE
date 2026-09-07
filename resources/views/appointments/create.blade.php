<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva cita — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Agenda clínica</p><h1 class="text-xl font-bold">Nueva cita</h1></div>
        </div>
        <a href="{{ route('appointments.index') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver a agenda</a>
    </div>
</header>
<main class="mx-auto max-w-5xl px-6 py-10">
    <form method="POST" action="{{ route('appointments.store') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        @csrf
        @include('appointments._form')
        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('appointments.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancelar</a>
            <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Guardar cita</button>
        </div>
    </form>
</main>
<x-sweet-alerts />
</body>
</html>
