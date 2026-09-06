<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración del centro — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">URPE</p>
            <h1 class="text-xl font-bold">Configuración del centro</h1>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver al dashboard</a>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <form method="POST" action="{{ route('center.update') }}"
          data-swal-confirm
          data-swal-title="¿Guardar configuración?"
          data-swal-text="Se actualizarán los datos generales y los horarios operativos del centro."
          data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Datos generales</p>
                <h2 class="mt-1 text-2xl font-bold">Centro</h2>
                <p class="mt-2 text-sm text-slate-500">Información operativa utilizada por URPE Gestión Clínica.</p>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Nombre del centro</span>
                    <input name="name" value="{{ old('name', $center->name) }}" required maxlength="120"
                           class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Zona horaria</span>
                    <select name="timezone" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4">
                        <option value="America/Mexico_City" @selected(old('timezone', $center->timezone) === 'America/Mexico_City')>America/Mexico_City</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Teléfono</span>
                    <input name="phone" value="{{ old('phone', $center->phone) }}" maxlength="40"
                           class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Correo electrónico</span>
                    <input type="email" name="email" value="{{ old('email', $center->email) }}" maxlength="190"
                           class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4">
                </label>

                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-slate-700">Dirección</span>
                    <textarea name="address" rows="3" maxlength="500"
                              class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4">{{ old('address', $center->address) }}</textarea>
                </label>
            </div>
        </section>

        <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Operación</p>
                <h2 class="mt-1 text-2xl font-bold">Horario semanal</h2>
                <p class="mt-2 text-sm text-slate-500">Cada día puede estar cerrado o tener una o más ventanas de atención. Los horarios no pueden traslaparse.</p>
            </div>

            <div class="mt-6 space-y-5">
                @foreach($days as $dayNumber => $dayLabel)
                    @php
                        $storedWindows = old("hours.$dayNumber", collect($weeklyHours[$dayNumber] ?? [])->map(fn ($window) => [
                            'is_enabled' => $window->is_enabled,
                            'opens_at' => $window->opens_at ? substr($window->opens_at, 0, 5) : null,
                            'closes_at' => $window->closes_at ? substr($window->closes_at, 0, 5) : null,
                        ])->values()->all());

                        if (empty($storedWindows)) {
                            $storedWindows = [['is_enabled' => false, 'opens_at' => null, 'closes_at' => null]];
                        }
                    @endphp

                    <div class="rounded-2xl border border-slate-200 p-5" data-day-card="{{ $dayNumber }}">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $dayLabel }}</h3>
                                <p class="text-xs text-slate-500">Activa las ventanas que correspondan.</p>
                            </div>
                            <button type="button" data-add-window="{{ $dayNumber }}" class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-800 hover:bg-cyan-100">+ Agregar ventana</button>
                        </div>

                        <div class="mt-4 space-y-3" data-windows="{{ $dayNumber }}">
                            @foreach($storedWindows as $index => $window)
                                <div class="grid gap-3 rounded-xl bg-slate-50 p-4 md:grid-cols-[auto_1fr_1fr_auto] md:items-end" data-window-row>
                                    <label class="flex items-center gap-2 pb-3 md:pb-0">
                                        <input type="hidden" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="0">
                                        <input type="checkbox" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="1" @checked((bool) ($window['is_enabled'] ?? false)) class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                        <span class="text-sm font-semibold text-slate-700">Activo</span>
                                    </label>

                                    <label class="block">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Apertura</span>
                                        <input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][opens_at]" value="{{ $window['opens_at'] ?? '' }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                                    </label>

                                    <label class="block">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cierre</span>
                                        <input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][closes_at]" value="{{ $window['closes_at'] ?? '' }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                                    </label>

                                    <button type="button" data-remove-window class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Quitar</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Guardar configuración</button>
        </div>
    </form>
</main>

<template id="operating-window-template">
    <div class="grid gap-3 rounded-xl bg-slate-50 p-4 md:grid-cols-[auto_1fr_1fr_auto] md:items-end" data-window-row>
        <label class="flex items-center gap-2 pb-3 md:pb-0">
            <input type="hidden" data-field="is_enabled_hidden" value="0">
            <input type="checkbox" data-field="is_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
            <span class="text-sm font-semibold text-slate-700">Activo</span>
        </label>
        <label class="block">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Apertura</span>
            <input type="time" data-field="opens_at" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        </label>
        <label class="block">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cierre</span>
            <input type="time" data-field="closes_at" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        </label>
        <button type="button" data-remove-window class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Quitar</button>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const template = document.getElementById('operating-window-template');

        const bindRemoveButtons = (scope = document) => {
            scope.querySelectorAll('[data-remove-window]').forEach((button) => {
                if (button.dataset.bound === '1') return;
                button.dataset.bound = '1';
                button.addEventListener('click', () => {
                    const container = button.closest('[data-windows]');
                    const rows = container.querySelectorAll('[data-window-row]');
                    if (rows.length === 1) {
                        const row = button.closest('[data-window-row]');
                        row.querySelector('[data-field="is_enabled"], input[type="checkbox"]')?.removeAttribute('checked');
                        const checkbox = row.querySelector('input[type="checkbox"]');
                        if (checkbox) checkbox.checked = false;
                        row.querySelectorAll('input[type="time"]').forEach((input) => input.value = '');
                        return;
                    }
                    button.closest('[data-window-row]').remove();
                    renumber(container);
                });
            });
        };

        const renumber = (container) => {
            const day = container.dataset.windows;
            container.querySelectorAll('[data-window-row]').forEach((row, index) => {
                const hidden = row.querySelector('input[type="hidden"]');
                const checkbox = row.querySelector('input[type="checkbox"]');
                const times = row.querySelectorAll('input[type="time"]');
                if (hidden) hidden.name = `hours[${day}][${index}][is_enabled]`;
                if (checkbox) checkbox.name = `hours[${day}][${index}][is_enabled]`;
                if (times[0]) times[0].name = `hours[${day}][${index}][opens_at]`;
                if (times[1]) times[1].name = `hours[${day}][${index}][closes_at]`;
            });
        };

        document.querySelectorAll('[data-add-window]').forEach((button) => {
            button.addEventListener('click', () => {
                const day = button.dataset.addWindow;
                const container = document.querySelector(`[data-windows="${day}"]`);
                const fragment = template.content.cloneNode(true);
                container.appendChild(fragment);
                renumber(container);
                bindRemoveButtons(container);
            });
        });

        document.querySelectorAll('[data-windows]').forEach(renumber);
        bindRemoveButtons();
    });
</script>

<x-sweet-alerts />
</body>
</html>
