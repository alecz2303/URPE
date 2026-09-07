@php
    $editing = isset($appointment);
    $selectedTherapists = collect(old('therapist_ids', $editing ? $appointment->therapists->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $startsAtValue = old('starts_at', $editing ? $appointment->starts_at->format('Y-m-d\TH:i') : '');
    $appointmentDateValue = ! $editing && $startsAtValue ? substr($startsAtValue, 0, 10) : '';
    $recurrenceEnabled = ! $editing && (bool) old('recurrence_enabled', false);
    $selectedWeekdays = collect(old('recurrence_weekdays', []))->map(fn ($day) => (int) $day)->all();
    $weekdayLabels = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    @unless($editing)
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">
            <label for="patient_id" class="mb-2 block text-sm font-bold text-cyan-900">Paciente</label>
            <select id="patient_id" name="patient_id" required class="w-full rounded-xl border-cyan-200 bg-white focus:border-cyan-600 focus:ring-cyan-600">
                <option value="">Selecciona un paciente</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" @selected((int) old('patient_id') === $patient->id)>{{ $patient->full_name }} · {{ $patient->folio }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">
            <p class="mb-2 block text-sm font-bold text-cyan-900">Paciente</p>
            <div class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-slate-700 ring-1 ring-cyan-100">{{ $appointment->patient->full_name }} · {{ $appointment->patient->folio }}</div>
        </div>
    @endunless

    <div class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50/60 p-4">
        <label for="therapy_id" class="mb-2 block text-sm font-bold text-fuchsia-900">Terapia</label>
        <select id="therapy_id" name="therapy_id" required class="w-full rounded-xl border-fuchsia-200 bg-white focus:border-fuchsia-500 focus:ring-fuchsia-500">
            <option value="">Selecciona una terapia</option>
            @foreach($therapies as $therapy)
                <option value="{{ $therapy->id }}" @selected((int) old('therapy_id', $editing ? $appointment->therapy_id : 0) === $therapy->id)>
                    {{ $therapy->name }} · {{ $therapy->duration_minutes }} min · {{ $therapy->required_therapists }} terapeuta(s)
                </option>
            @endforeach
        </select>
    </div>

    @if($editing)
        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4">
            <label for="starts_at" class="mb-2 block text-sm font-bold text-amber-900">Fecha y hora de inicio</label>
            <input id="starts_at" type="datetime-local" name="starts_at" value="{{ $startsAtValue }}" required class="w-full rounded-xl border-amber-200 bg-white focus:border-amber-500 focus:ring-amber-500">
        </div>
    @else
        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4">
            <label for="appointment_date" class="mb-2 block text-sm font-bold text-amber-900">Fecha</label>
            <input id="appointment_date" type="date" value="{{ $appointmentDateValue }}" required class="w-full rounded-xl border-amber-200 bg-white focus:border-amber-500 focus:ring-amber-500">
            <input id="starts_at" type="hidden" name="starts_at" value="{{ $startsAtValue }}">
            <p class="mt-2 text-xs leading-5 text-amber-800/75">Elige terapia y fecha para que URPE calcule los horarios que realmente pueden agendarse.</p>
        </div>

        <div id="availability_panel" data-url="{{ route('appointments.availability') }}" class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-sky-900">Disponibilidad del día</p>
                    <p class="mt-1 text-xs text-sky-800/70">URPE cruza horario del centro, disponibilidad, ausencias, bloqueos y citas existentes.</p>
                </div>
                <div id="center_hours" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-sky-800 ring-1 ring-sky-100">Selecciona terapia y fecha</div>
            </div>
            <div id="availability_status" class="mt-4 rounded-xl bg-white px-4 py-3 text-sm text-slate-600 ring-1 ring-sky-100">Aún no se ha consultado disponibilidad.</div>
            <div id="availability_slots" class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4"></div>
        </div>
    @endif

    @unless($editing)
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-emerald-900">Repetir cita</p>
                    <p class="mt-1 text-xs text-emerald-800/70">Crea una serie semanal manteniendo paciente, terapia, hora y terapeutas.</p>
                </div>
                <label class="inline-flex cursor-pointer items-center gap-3 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-emerald-800 ring-1 ring-emerald-200">
                    <input id="recurrence_enabled" type="checkbox" name="recurrence_enabled" value="1" @checked($recurrenceEnabled) class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Activar recurrencia</span>
                </label>
            </div>

            <div id="recurrence_options" class="mt-4 grid gap-4 rounded-2xl bg-white/80 p-4 ring-1 ring-emerald-100 {{ $recurrenceEnabled ? '' : 'hidden' }} lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <p class="mb-2 text-sm font-bold text-slate-700">Días de la semana</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($weekdayLabels as $day => $label)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="recurrence_weekdays[]" value="{{ $day }}" @checked(in_array($day, $selectedWeekdays, true)) class="peer sr-only">
                                <span class="inline-flex min-w-12 justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-600 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:text-white">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="recurrence_ends_on" class="mb-2 block text-sm font-bold text-slate-700">Repetir hasta</label>
                    <input id="recurrence_ends_on" type="date" name="recurrence_ends_on" value="{{ old('recurrence_ends_on') }}" class="w-full rounded-xl border-emerald-200 bg-white focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="rounded-xl bg-emerald-50 px-4 py-3 text-xs leading-5 text-emerald-900 ring-1 ring-emerald-100">
                    URPE validará cada fecha antes de crear la serie. Si alguna cita presenta conflicto de horario, disponibilidad, bloqueo o traslape, no se guardará ninguna y verás exactamente qué fechas requieren ajuste.
                </div>
            </div>
        </div>
    @endunless

    <div class="rounded-2xl border border-violet-100 bg-violet-50/60 p-4 lg:col-span-2">
        <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
            <div>
                <p class="text-sm font-bold text-violet-900">Terapeutas</p>
                <p id="therapist_help" class="mt-1 text-xs text-violet-700/70">{{ $editing ? 'Selecciona exactamente la cantidad requerida por la terapia.' : 'Selecciona primero un horario disponible para ver quién puede atenderlo.' }}</p>
            </div>
        </div>
        <div id="therapist_grid" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($therapists as $therapist)
                <label data-therapist-card="{{ $therapist->id }}" class="flex items-center gap-3 rounded-xl border border-violet-100 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition {{ $editing ? 'cursor-pointer hover:border-violet-300 hover:bg-violet-50' : 'cursor-not-allowed opacity-50' }}">
                    <input type="checkbox" name="therapist_ids[]" value="{{ $therapist->id }}" @checked(in_array($therapist->id, $selectedTherapists, true)) @disabled(! $editing) class="rounded border-violet-300 text-violet-600 focus:ring-violet-500">
                    <span class="min-w-0 flex-1">{{ $therapist->name }}</span>
                    @unless($editing)<span data-therapist-state class="text-[11px] font-medium text-slate-400">Pendiente</span>@endunless
                </label>
            @endforeach
        </div>
    </div>
</div>

@if($errors->any())
    <div class="mt-6 rounded-2xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@unless($editing)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const recurrenceToggle = document.getElementById('recurrence_enabled');
            const recurrenceOptions = document.getElementById('recurrence_options');
            const therapy = document.getElementById('therapy_id');
            const date = document.getElementById('appointment_date');
            const startsAt = document.getElementById('starts_at');
            const panel = document.getElementById('availability_panel');
            const slotsContainer = document.getElementById('availability_slots');
            const status = document.getElementById('availability_status');
            const centerHours = document.getElementById('center_hours');
            const therapistHelp = document.getElementById('therapist_help');
            const initialStartsAt = startsAt?.value || '';
            let requestToken = 0;

            const syncRecurrenceVisibility = () => recurrenceOptions?.classList.toggle('hidden', ! recurrenceToggle?.checked);
            recurrenceToggle?.addEventListener('change', syncRecurrenceVisibility);
            syncRecurrenceVisibility();

            const resetTherapists = () => {
                document.querySelectorAll('#therapist_grid input[name="therapist_ids[]"]').forEach((input) => {
                    input.checked = false;
                    input.disabled = true;
                    const card = input.closest('[data-therapist-card]');
                    card?.classList.add('cursor-not-allowed', 'opacity-50');
                    card?.classList.remove('cursor-pointer', 'hover:border-violet-300', 'hover:bg-violet-50');
                    const state = card?.querySelector('[data-therapist-state]');
                    if (state) {
                        state.textContent = 'Pendiente';
                        state.className = 'text-[11px] font-medium text-slate-400';
                    }
                });
            };

            const applyTherapists = (slot) => {
                const required = Number(slot.required_therapists || 0);
                const available = slot.therapists.filter((item) => item.available);

                slot.therapists.forEach((item) => {
                    const input = document.querySelector(`#therapist_grid input[value="${item.id}"]`);
                    if (!input) return;
                    const card = input.closest('[data-therapist-card]');
                    const state = card?.querySelector('[data-therapist-state]');
                    input.disabled = !item.available;
                    if (!item.available) input.checked = false;
                    card?.classList.toggle('opacity-50', !item.available);
                    card?.classList.toggle('cursor-not-allowed', !item.available);
                    card?.classList.toggle('cursor-pointer', item.available);
                    card?.classList.toggle('hover:border-violet-300', item.available);
                    card?.classList.toggle('hover:bg-violet-50', item.available);
                    if (state) {
                        state.textContent = item.available ? 'Disponible' : item.reason;
                        state.className = item.available
                            ? 'text-[11px] font-bold text-emerald-600'
                            : 'text-[11px] font-medium text-rose-500';
                    }
                });

                if (available.length === required) {
                    available.forEach((item) => {
                        const input = document.querySelector(`#therapist_grid input[value="${item.id}"]`);
                        if (input) input.checked = true;
                    });
                }

                if (therapistHelp) {
                    therapistHelp.textContent = available.length === required
                        ? `${available.length} terapeuta(s) disponible(s): selección automática.`
                        : `${available.length} terapeuta(s) disponible(s). Selecciona exactamente ${required}.`;
                }
            };

            const chooseSlot = (slot, button) => {
                startsAt.value = slot.starts_at;
                slotsContainer.querySelectorAll('button').forEach((item) => {
                    item.classList.remove('border-sky-500', 'bg-sky-600', 'text-white', 'ring-2', 'ring-sky-200');
                });
                button.classList.add('border-sky-500', 'bg-sky-600', 'text-white', 'ring-2', 'ring-sky-200');
                applyTherapists(slot);
                status.textContent = `Horario seleccionado: ${slot.label}. Ahora elige los terapeutas disponibles.`;
            };

            const renderAvailability = (payload) => {
                slotsContainer.innerHTML = '';
                const windows = payload.center_windows || [];
                centerHours.textContent = windows.length
                    ? `Centro: ${windows.map((window) => `${window.opens_at}–${window.closes_at}`).join(' · ')}`
                    : 'Centro cerrado';

                if (!payload.slots?.length) {
                    status.textContent = 'No hay horarios configurados para esta fecha.';
                    resetTherapists();
                    return;
                }

                const selectable = payload.slots.filter((slot) => slot.selectable).length;
                status.textContent = selectable
                    ? `${selectable} horario(s) con suficientes terapeutas disponibles.`
                    : 'No hay horarios con la cantidad de terapeutas requerida.';

                payload.slots.forEach((slot) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.disabled = !slot.selectable;
                    button.className = slot.selectable
                        ? 'rounded-xl border border-sky-200 bg-white px-3 py-3 text-left transition hover:border-sky-400 hover:bg-sky-50'
                        : 'cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-3 py-3 text-left opacity-60';
                    button.innerHTML = `<span class="block text-sm font-bold">${slot.label}</span><span class="mt-1 block text-xs">${slot.available_count}/${slot.required_therapists} necesarios · disponibles</span>`;
                    if (slot.selectable) button.addEventListener('click', () => chooseSlot(slot, button));
                    slotsContainer.appendChild(button);

                    if (slot.selectable && initialStartsAt && slot.starts_at === initialStartsAt) {
                        chooseSlot(slot, button);
                    }
                });
            };

            const loadAvailability = async () => {
                const therapyId = therapy?.value;
                const selectedDate = date?.value;
                startsAt.value = '';
                resetTherapists();
                slotsContainer.innerHTML = '';

                if (!therapyId || !selectedDate) {
                    centerHours.textContent = 'Selecciona terapia y fecha';
                    status.textContent = 'Aún no se ha consultado disponibilidad.';
                    return;
                }

                const token = ++requestToken;
                status.textContent = 'Consultando horarios y terapeutas disponibles…';
                centerHours.textContent = 'Calculando…';

                try {
                    const url = new URL(panel.dataset.url, window.location.origin);
                    url.searchParams.set('therapy_id', therapyId);
                    url.searchParams.set('date', selectedDate);
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) throw new Error('No fue posible consultar la disponibilidad.');
                    const payload = await response.json();
                    if (token !== requestToken) return;
                    renderAvailability(payload);
                } catch (error) {
                    if (token !== requestToken) return;
                    centerHours.textContent = 'Disponibilidad no disponible';
                    status.textContent = error.message || 'No fue posible consultar la disponibilidad.';
                }
            };

            therapy?.addEventListener('change', loadAvailability);
            date?.addEventListener('change', loadAvailability);
            loadAvailability();
        });
    </script>
@endunless
