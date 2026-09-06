<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terapeutas — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Terapeutas</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Dashboard</a>
            <a href="{{ route('therapists.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Nuevo terapeuta</a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Equipo clínico</p>
        <h2 class="mt-1 text-3xl font-bold">Perfiles y disponibilidad</h2>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Administra los perfiles operativos de terapeutas, sus horarios semanales y los bloqueos que afectarán la futura agenda.</p>
    </div>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terapeuta</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario vinculado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Disponibilidad</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($therapists as $therapist)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $therapist->name }}</p>
                            <p class="text-sm text-slate-500">{{ $therapist->professional_title ?: 'Sin título registrado' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $therapist->user?->email ?: 'Sin vínculo' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $therapist->availabilityWindows->count() }} ventana(s)</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $therapist->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $therapist->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('therapists.edit', $therapist) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Administrar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Aún no hay terapeutas registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<x-sweet-alerts />
</body>
</html>
