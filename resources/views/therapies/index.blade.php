<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terapias — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Terapias</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Dashboard</a>
            <a href="{{ route('therapies.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Nueva terapia</a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Catálogo clínico</p>
        <h2 class="mt-1 text-3xl font-bold">Terapias configurables</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Define duración, cantidad de terapeutas requeridos, color de agenda y disponibilidad para nuevas citas. La futura agenda consultará estos valores sin depender del nombre de la terapia.</p>
    </div>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terapia</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Duración</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terapeutas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($therapies as $therapy)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="h-4 w-4 rounded-full ring-1 ring-black/10" style="background-color: {{ $therapy->color }}"></span>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $therapy->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $therapy->color }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $therapy->duration_minutes }} min</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $therapy->required_therapists }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $therapy->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $therapy->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('therapies.edit', $therapy) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Editar</a>
                                <form method="POST" action="{{ route('therapies.toggle-active', $therapy) }}" class="js-confirm-status" data-therapy="{{ $therapy->name }}" data-active="{{ $therapy->is_active ? '1' : '0' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-sm font-semibold {{ $therapy->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-700 hover:text-emerald-900' }}">
                                        {{ $therapy->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Aún no hay terapias registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<x-sweet-alerts />
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.js-confirm-status').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                if (form.dataset.confirmed === '1') return;
                event.preventDefault();

                const active = form.dataset.active === '1';
                const therapy = form.dataset.therapy;
                const result = await Swal.fire({
                    title: active ? '¿Desactivar terapia?' : '¿Activar terapia?',
                    text: active
                        ? `${therapy} dejará de estar disponible para nuevas citas.`
                        : `${therapy} volverá a estar disponible para nuevas citas.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: active ? 'Sí, desactivar' : 'Sí, activar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                });

                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    });
</script>
</body>
</html>
