<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar terapeuta — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">{{ $therapist->name }}</h1>
            </div>
        </div>
        <a href="{{ route('therapists.index') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver a terapeutas</a>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <form method="POST" action="{{ route('therapists.update', $therapist) }}"
          data-swal-confirm
          data-swal-title="¿Guardar cambios?"
          data-swal-text="Se actualizará el perfil y la disponibilidad semanal del terapeuta."
          data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')
        @include('therapists._form')

        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">Guardar cambios</button>
        </div>
    </form>

    <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_1.4fr]">
        <form method="POST" action="{{ route('therapists.blocks.store', $therapist) }}"
              class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
              data-swal-confirm
              data-swal-title="¿Registrar bloqueo?"
              data-swal-text="El terapeuta quedará no disponible durante el intervalo indicado."
              data-swal-confirm-text="Sí, registrar">
            @csrf
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Ausencias y bloqueos</p>
                <h2 class="mt-1 text-2xl font-bold">Nuevo bloqueo</h2>
                <p class="mt-2 text-sm text-slate-500">Úsalo para vacaciones, permisos, reuniones u otros periodos no disponibles.</p>
            </div>

            <div class="mt-6 space-y-4">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Inicio</span>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Fin</span>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Motivo</span>
                    <textarea name="reason" rows="3" maxlength="500" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">{{ old('reason') }}</textarea>
                </label>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">Registrar bloqueo</button>
            </div>
        </form>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Historial operativo</p>
                <h2 class="mt-1 text-2xl font-bold">Bloqueos registrados</h2>
            </div>

            <div class="mt-6 space-y-3">
                @forelse($therapist->blocks as $block)
                    <article class="rounded-xl border border-slate-200 p-4">
                        <p class="font-semibold text-slate-900">{{ $block->starts_at->format('d/m/Y H:i') }} — {{ $block->ends_at->format('d/m/Y H:i') }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $block->reason ?: 'Sin motivo registrado.' }}</p>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No hay bloqueos registrados para este terapeuta.</p>
                @endforelse
            </div>
        </section>
    </section>
</main>

<x-sweet-alerts />
</body>
</html>
