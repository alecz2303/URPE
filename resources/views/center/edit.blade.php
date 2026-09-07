<x-app-shell title="Configuración del centro" eyebrow="Administración">
    <div class="mb-6 overflow-hidden rounded-3xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-sky-50 to-violet-50 p-6 shadow-sm sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-cyan-700">Centro y horarios</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900">La base de la operación clínica</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Mantén identificados los datos de URPE y define las ventanas en las que la agenda puede programar atención.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center text-xs font-bold">
                <span class="rounded-2xl border border-cyan-200 bg-white/80 px-4 py-3 text-cyan-800">Datos del centro</span>
                <span class="rounded-2xl border border-violet-200 bg-white/80 px-4 py-3 text-violet-800">Horario semanal</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('center.update') }}"
          data-swal-confirm data-swal-title="¿Guardar configuración?"
          data-swal-text="Se actualizarán los datos generales y los horarios operativos del centro."
          data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50 to-sky-50 p-6 lg:px-8">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">01 · Datos generales</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Información del centro</h2>
                <p class="mt-2 text-sm text-slate-600">Datos que identifican al centro dentro de URPE Gestión Clínica.</p>
            </div>
            <div class="grid gap-5 p-6 md:grid-cols-2 lg:p-8">
                <label class="block rounded-2xl border border-cyan-100 bg-cyan-50/40 p-4"><span class="text-sm font-bold text-cyan-900">Nombre del centro</span><input name="name" value="{{ old('name', $center->name) }}" required maxlength="120" class="mt-2 w-full rounded-xl border border-cyan-200 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4"></label>
                <label class="block rounded-2xl border border-violet-100 bg-violet-50/40 p-4"><span class="text-sm font-bold text-violet-900">Zona horaria</span><select name="timezone" required class="mt-2 w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm"><option value="America/Mexico_City" @selected(old('timezone', $center->timezone) === 'America/Mexico_City')>America/Mexico_City</option></select></label>
                <label class="block rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4"><span class="text-sm font-bold text-emerald-900">Teléfono</span><input name="phone" value="{{ old('phone', $center->phone) }}" maxlength="40" class="mt-2 w-full rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm"></label>
                <label class="block rounded-2xl border border-fuchsia-100 bg-fuchsia-50/40 p-4"><span class="text-sm font-bold text-fuchsia-900">Correo electrónico</span><input type="email" name="email" value="{{ old('email', $center->email) }}" maxlength="190" class="mt-2 w-full rounded-xl border border-fuchsia-200 bg-white px-4 py-3 text-sm"></label>
                <label class="block rounded-2xl border border-amber-100 bg-amber-50/40 p-4 md:col-span-2"><span class="text-sm font-bold text-amber-900">Dirección</span><textarea name="address" rows="3" maxlength="500" class="mt-2 w-full rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm">{{ old('address', $center->address) }}</textarea></label>
            </div>
        </section>

        <section class="mt-6 overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
            <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-rose-50 p-6 lg:px-8">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-violet-700">02 · Operación</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Horario semanal</h2>
                <p class="mt-2 text-sm text-slate-600">Define cuándo puede operar la agenda. Cada día admite una o más ventanas sin traslapes.</p>
            </div>
            <div class="grid gap-4 p-6 xl:grid-cols-2 lg:p-8">
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
                        $dayStyles = [
                            1 => ['border-cyan-100', 'bg-cyan-50/35', 'text-cyan-800', 'bg-cyan-100'],
                            2 => ['border-violet-100', 'bg-violet-50/35', 'text-violet-800', 'bg-violet-100'],
                            3 => ['border-fuchsia-100', 'bg-fuchsia-50/35', 'text-fuchsia-800', 'bg-fuchsia-100'],
                            4 => ['border-emerald-100', 'bg-emerald-50/35', 'text-emerald-800', 'bg-emerald-100'],
                            5 => ['border-amber-100', 'bg-amber-50/35', 'text-amber-800', 'bg-amber-100'],
                            6 => ['border-sky-100', 'bg-sky-50/35', 'text-sky-800', 'bg-sky-100'],
                            7 => ['border-rose-100', 'bg-rose-50/35', 'text-rose-800', 'bg-rose-100'],
                        ][$dayNumber] ?? ['border-slate-100', 'bg-slate-50', 'text-slate-800', 'bg-slate-100'];
                    @endphp
                    <article class="rounded-2xl border {{ $dayStyles[0] }} {{ $dayStyles[1] }} p-5" data-day-card="{{ $dayNumber }}">
                        <div class="flex items-center justify-between gap-3"><div><h3 class="font-black {{ $dayStyles[2] }}">{{ $dayLabel }}</h3><p class="mt-1 text-xs font-medium text-slate-500">Ventanas de atención</p></div><button type="button" data-add-window="{{ $dayNumber }}" class="rounded-xl {{ $dayStyles[3] }} px-3 py-2 text-xs font-black {{ $dayStyles[2] }} transition hover:scale-[1.02]">+ Ventana</button></div>
                        <div class="mt-4 space-y-3" data-windows="{{ $dayNumber }}">
                            @foreach($storedWindows as $index => $window)
                                <div class="grid gap-3 rounded-xl border border-white bg-white/90 p-4 shadow-sm sm:grid-cols-[auto_1fr_1fr_auto] sm:items-end" data-window-row>
                                    <label class="flex items-center gap-2 pb-2 sm:pb-0"><input type="hidden" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="0"><input type="checkbox" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="1" @checked((bool) ($window['is_enabled'] ?? false)) class="h-4 w-4 rounded border-slate-300 text-cyan-700"><span class="text-xs font-black text-slate-700">Abierto</span></label>
                                    <label><span class="text-[10px] font-black uppercase tracking-wide text-slate-400">Abre</span><input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][opens_at]" value="{{ $window['opens_at'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm"></label>
                                    <label><span class="text-[10px] font-black uppercase tracking-wide text-slate-400">Cierra</span><input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][closes_at]" value="{{ $window['closes_at'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm"></label>
                                    <button type="button" data-remove-window class="rounded-lg px-2 py-2 text-xs font-black text-rose-600 hover:bg-rose-50">Quitar</button>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        <div class="sticky bottom-4 z-10 mt-6 flex justify-end"><button type="submit" class="rounded-2xl bg-gradient-to-r from-cyan-600 via-sky-600 to-violet-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-cyan-900/15 transition hover:scale-[1.02]">Guardar configuración</button></div>
    </form>

    <template id="operating-window-template"><div class="grid gap-3 rounded-xl border border-white bg-white/90 p-4 shadow-sm sm:grid-cols-[auto_1fr_1fr_auto] sm:items-end" data-window-row><label class="flex items-center gap-2 pb-2 sm:pb-0"><input type="hidden" data-field="is_enabled_hidden" value="0"><input type="checkbox" data-field="is_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-700"><span class="text-xs font-black text-slate-700">Abierto</span></label><label><span class="text-[10px] font-black uppercase tracking-wide text-slate-400">Abre</span><input type="time" data-field="opens_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm"></label><label><span class="text-[10px] font-black uppercase tracking-wide text-slate-400">Cierra</span><input type="time" data-field="closes_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2 py-2 text-sm"></label><button type="button" data-remove-window class="rounded-lg px-2 py-2 text-xs font-black text-rose-600 hover:bg-rose-50">Quitar</button></div></template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const template = document.getElementById('operating-window-template');
            const renumber = (container) => {
                const day = container.dataset.windows;
                container.querySelectorAll('[data-window-row]').forEach((row, index) => {
                    const hidden = row.querySelector('input[type="hidden"]'), checkbox = row.querySelector('input[type="checkbox"]'), times = row.querySelectorAll('input[type="time"]');
                    if (hidden) hidden.name = `hours[${day}][${index}][is_enabled]`;
                    if (checkbox) checkbox.name = `hours[${day}][${index}][is_enabled]`;
                    if (times[0]) times[0].name = `hours[${day}][${index}][opens_at]`;
                    if (times[1]) times[1].name = `hours[${day}][${index}][closes_at]`;
                });
            };
            const bindRemoveButtons = (scope = document) => scope.querySelectorAll('[data-remove-window]').forEach(button => {
                if (button.dataset.bound === '1') return; button.dataset.bound = '1';
                button.addEventListener('click', () => { const container = button.closest('[data-windows]'), rows = container.querySelectorAll('[data-window-row]'); if (rows.length === 1) { const row = button.closest('[data-window-row]'); const checkbox = row.querySelector('input[type="checkbox"]'); if (checkbox) checkbox.checked = false; row.querySelectorAll('input[type="time"]').forEach(input => input.value = ''); return; } button.closest('[data-window-row]').remove(); renumber(container); });
            });
            document.querySelectorAll('[data-add-window]').forEach(button => button.addEventListener('click', () => { const container = document.querySelector(`[data-windows="${button.dataset.addWindow}"]`); container.appendChild(template.content.cloneNode(true)); renumber(container); bindRemoveButtons(container); }));
            document.querySelectorAll('[data-windows]').forEach(renumber); bindRemoveButtons();
        });
    </script>
</x-app-shell>
