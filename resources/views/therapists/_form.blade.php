@php
    $editing = isset($therapist);
    $scheduleSource = old('schedule');

    if ($scheduleSource === null && $editing) {
        $scheduleSource = collect($therapist->availabilityWindows)
            ->groupBy('day_of_week')
            ->map(fn ($windows) => $windows->map(fn ($window) => [
                'starts_at' => substr($window->starts_at, 0, 5),
                'ends_at' => substr($window->ends_at, 0, 5),
            ])->values()->all())
            ->all();
    }
@endphp

<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Perfil operativo</p>
        <h2 class="mt-1 text-2xl font-bold">Datos del terapeuta</h2>
        <p class="mt-2 text-sm text-slate-500">El vínculo con una cuenta interna es opcional y no duplica las credenciales del sistema.</p>
    </div>

    <div class="mt-6 grid gap-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Usuario vinculado</span>
            <select name="user_id" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
                <option value="">Sin vínculo</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $therapist->user_id ?? '') === (string) $user->id)>
                        {{ $user->name }} · {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="flex items-end gap-3 rounded-xl border border-slate-200 px-4 py-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $therapist->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
            <span class="text-sm font-semibold text-slate-700">Terapeuta activo para programación</span>
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Nombre</span>
            <input name="name" value="{{ old('name', $therapist->name ?? '') }}" required maxlength="255" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Título profesional</span>
            <input name="professional_title" value="{{ old('professional_title', $therapist->professional_title ?? '') }}" maxlength="255" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Cédula profesional</span>
            <input name="license_number" value="{{ old('license_number', $therapist->license_number ?? '') }}" maxlength="120" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Teléfono</span>
            <input name="phone" value="{{ old('phone', $therapist->phone ?? '') }}" maxlength="60" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
        </label>

        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-slate-700">Correo electrónico</span>
            <input type="email" name="email" value="{{ old('email', $therapist->email ?? '') }}" maxlength="255" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">
        </label>

        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-slate-700">Notas operativas</span>
            <textarea name="notes" rows="3" maxlength="2000" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">{{ old('notes', $therapist->notes ?? '') }}</textarea>
        </label>
    </div>
</section>

<section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Disponibilidad</p>
        <h2 class="mt-1 text-2xl font-bold">Horario semanal</h2>
        <p class="mt-2 text-sm text-slate-500">Cada ventana debe quedar completamente dentro de una ventana operativa habilitada del centro. Puedes usar varias ventanas por día para descansos o turnos partidos.</p>
    </div>

    <div class="mt-6 space-y-5">
        @foreach($days as $dayNumber => $dayLabel)
            @php
                $storedWindows = $scheduleSource[$dayNumber] ?? [];
                if (empty($storedWindows)) {
                    $storedWindows = [['starts_at' => '', 'ends_at' => '']];
                }
                $centerWindows = collect($centerHours[$dayNumber] ?? [])->filter(fn ($window) => $window->is_enabled);
            @endphp

            <div class="rounded-2xl border border-slate-200 p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $dayLabel }}</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Centro:
                            @forelse($centerWindows as $centerWindow)
                                {{ substr($centerWindow->opens_at, 0, 5) }}–{{ substr($centerWindow->closes_at, 0, 5) }}{{ ! $loop->last ? ' · ' : '' }}
                            @empty
                                cerrado
                            @endforelse
                        </p>
                    </div>
                    <button type="button" data-add-therapist-window="{{ $dayNumber }}" class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-800 hover:bg-cyan-100">+ Agregar ventana</button>
                </div>

                <div class="mt-4 space-y-3" data-therapist-windows="{{ $dayNumber }}">
                    @foreach($storedWindows as $index => $window)
                        <div class="grid gap-3 rounded-xl bg-slate-50 p-4 md:grid-cols-[1fr_1fr_auto] md:items-end" data-therapist-window-row>
                            <label class="block">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Inicio</span>
                                <input type="time" name="schedule[{{ $dayNumber }}][{{ $index }}][starts_at]" value="{{ $window['starts_at'] ?? '' }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                            </label>
                            <label class="block">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fin</span>
                                <input type="time" name="schedule[{{ $dayNumber }}][{{ $index }}][ends_at]" value="{{ $window['ends_at'] ?? '' }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                            </label>
                            <button type="button" data-remove-therapist-window class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Quitar</button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<template id="therapist-window-template">
    <div class="grid gap-3 rounded-xl bg-slate-50 p-4 md:grid-cols-[1fr_1fr_auto] md:items-end" data-therapist-window-row>
        <label class="block">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Inicio</span>
            <input type="time" data-field="starts_at" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        </label>
        <label class="block">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fin</span>
            <input type="time" data-field="ends_at" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        </label>
        <button type="button" data-remove-therapist-window class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Quitar</button>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const template = document.getElementById('therapist-window-template');

        const renumber = (container) => {
            const day = container.dataset.therapistWindows;
            container.querySelectorAll('[data-therapist-window-row]').forEach((row, index) => {
                const times = row.querySelectorAll('input[type="time"]');
                if (times[0]) times[0].name = `schedule[${day}][${index}][starts_at]`;
                if (times[1]) times[1].name = `schedule[${day}][${index}][ends_at]`;
            });
        };

        const bindRemoveButtons = (scope = document) => {
            scope.querySelectorAll('[data-remove-therapist-window]').forEach((button) => {
                if (button.dataset.bound === '1') return;
                button.dataset.bound = '1';
                button.addEventListener('click', () => {
                    const container = button.closest('[data-therapist-windows]');
                    const rows = container.querySelectorAll('[data-therapist-window-row]');
                    if (rows.length === 1) {
                        button.closest('[data-therapist-window-row]').querySelectorAll('input[type="time"]').forEach((input) => input.value = '');
                        return;
                    }
                    button.closest('[data-therapist-window-row]').remove();
                    renumber(container);
                });
            });
        };

        document.querySelectorAll('[data-add-therapist-window]').forEach((button) => {
            button.addEventListener('click', () => {
                const day = button.dataset.addTherapistWindow;
                const container = document.querySelector(`[data-therapist-windows="${day}"]`);
                container.appendChild(template.content.cloneNode(true));
                renumber(container);
                bindRemoveButtons(container);
            });
        });

        document.querySelectorAll('[data-therapist-windows]').forEach(renumber);
        bindRemoveButtons();
    });
</script>
