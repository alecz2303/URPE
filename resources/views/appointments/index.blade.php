<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Agenda clínica</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Dashboard</a>
            @can('appointments.manage')
                <a href="{{ route('appointments.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Nueva cita</a>
            @endcan
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Operación clínica</p>
            <h2 class="mt-1 text-3xl font-bold">Agenda</h2>
            <p class="mt-2 text-sm text-slate-500">{{ $rangeStart->format('d/m/Y') }} — {{ $rangeEnd->format('d/m/Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach(['day' => 'Día', 'week' => 'Semana', 'month' => 'Mes'] as $key => $label)
                <a href="{{ route('appointments.index', ['view' => $key, 'date' => $date->toDateString()]) }}" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $mode === $key ? 'bg-cyan-700 text-white' : 'bg-white text-slate-700 ring-1 ring-slate-200' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha / hora</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Paciente</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terapia</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terapeuta(s)</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($appointments as $appointment)
                    <tr class="{{ $appointment->isCancelled() ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <p class="font-semibold">{{ $appointment->starts_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-slate-500">{{ $appointment->ends_at->format('H:i') }} · {{ $appointment->duration_minutes }} min</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $appointment->patient->full_name }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $appointment->patient->folio }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $appointment->therapy->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $appointment->therapists->pluck('name')->implode(', ') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $appointment->isCancelled() ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $appointment->isCancelled() ? 'Cancelada' : 'Programada' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @can('appointments.manage')
                                @if(! $appointment->isCancelled())
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Editar</a>
                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" data-confirm="¿Cancelar esta cita?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">Cancelar</button>
                                        </form>
                                    </div>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">No hay citas en este rango.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<x-sweet-alerts />
</body>
</html>
