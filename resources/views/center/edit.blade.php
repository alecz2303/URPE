<x-app-shell title="Configuración del centro" eyebrow="Administración">
    <p class="-mt-3 mb-6 text-sm text-slate-500">Centro y horarios</p>

    <form method="POST" action="{{ route('center.update') }}"
          data-swal-confirm data-swal-title="¿Guardar configuración?"
          data-swal-text="Se actualizarán los datos generales y los horarios operativos del centro."
          data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Datos generales</p>
                <h2 class="mt-1 text-xl font-bold">Información operativa</h2>
                <p class="mt-2 text-sm text-slate-500">Datos que identifican al centro dentro de URPE Gestión Clínica.</p>
            </div>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <label class="block"><span class="text-sm font-semibold text-slate-700">Nombre del centro</span><input name="name" value="{{ old('name', $center->name) }}" required maxlength="120" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none ring-cyan-200 transition focus:border-cyan-600 focus:ring-4"></label>
                <label class="block"><span class="text-sm font-semibold text-slate-700">Zona horaria</span><select name="timezone" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"><option value="America/Mexico_City" @selected(old('timezone', $center->timezone) === 'America/Mexico_City')>America/Mexico_City</option></select></label>
                <label class="block"><span class="text-sm font-semibold text-slate-700">Teléfono</span><input name="phone" value="{{ old('phone', $center->phone) }}" maxlength="40" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"></label>
                <label class="block"><span class="text-sm font-semibold text-slate-700">Correo electrónico</span><input type="email" name="email" value="{{ old('email', $center->email) }}" maxlength="190" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"></label>
                <label class="block md:col-span-2"><span class="text-sm font-semibold text-slate-700">Dirección</span><textarea name="address" rows="3" maxlength="500" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">{{ old('address', $center->address) }}</textarea></label>
            </div>
        </section>

        <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Operación</p><h2 class="mt-1 text-xl font-bold">Horario semanal</h2><p class="mt-2 text-sm text-slate-500">Define cuándo puede operar la agenda. Cada día admite una o más ventanas sin traslapes.</p></div>
            <div class="mt-6 grid gap-4 xl:grid-cols-2">
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
                    <article class="rounded-2xl border border-slate-200 p-5" data-day-card="{{ $dayNumber }}">
                        <div class="flex items-center justify-between gap-3"><div><h3 class="font-bold">{{ $dayLabel }}</h3><p class="mt-1 text-xs text-slate-400">Ventanas de atención</p></div><button type="button" data-add-window="{{ $dayNumber }}" class="rounded-xl bg-cyan-50 px-3 py-2 text-xs font-bold text-cyan-800 hover:bg-cyan-100">+ Ventana</button></div>
                        <div class="mt-4 space-y-3" data-windows="{{ $dayNumber }}">
                            @foreach($storedWindows as $index => $window)
                                <div class="grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-[auto_1fr_1fr_auto] sm:items-end" data-window-row>
                                    <label class="flex items-center gap-2 pb-2 sm:pb-0"><input type="hidden" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="0"><input type="checkbox" name="hours[{{ $dayNumber }}][{{ $index }}][is_enabled]" value="1" @checked((bool) ($window['is_enabled'] ?? false)) class="h-4 w-4 rounded border-slate-300 text-cyan-700"><span class="text-xs font-bold text-slate-600">Abierto</span></label>
                                    <label><span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Abre</span><input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][opens_at]" value="{{ $window['opens_at'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm"></label>
                                    <label><span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Cierra</span><input type="time" name="hours[{{ $dayNumber }}][{{ $index }}][closes_at]" value="{{ $window['closes_at'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm"></label>
                                    <button type="button" data-remove-window class="rounded-lg px-2 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">Quitar</button>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        <div class="sticky bottom-4 mt-6 flex justify-end"><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-cyan-800">Guardar configuración</button></div>
    </form>

    <template id="operating-window-template"><div class="grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-[auto_1fr_1fr_auto] sm:items-end" data-window-row><label class="flex items-center gap-2 pb-2 sm:pb-0"><input type="hidden" data-field="is_enabled_hidden" value="0"><input type="checkbox" data-field="is_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-700"><span class="text-xs font-bold text-slate-600">Abierto</span></label><label><span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Abre</span><input type="time" data-field="opens_at" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm"></label><label><span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Cierra</span><input type="time" data-field="closes_at" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm"></label><button type="button" data-remove-window class="rounded-lg px-2 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">Quitar</button></div></template>

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
