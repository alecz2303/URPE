<x-app-shell title="Editar cita" eyebrow="Agenda clínica">
    <x-slot:actions><a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a agenda</a></x-slot:actions>

    <section class="max-w-5xl overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50 via-violet-50 to-cyan-50 px-6 py-6 sm:px-8">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-[0.15em] text-fuchsia-700">Cita #{{ $appointment->id }}</p>
                <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $appointment->patient->full_name }}</h2>
                <p class="mt-1 text-sm text-slate-600">Reprograma terapia, terapeutas u horario. El paciente permanece vinculado a esta cita.</p>
                @if($appointment->isRecurring())
                    <p class="mt-2 inline-flex rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700 ring-1 ring-violet-200">Serie recurrente · sesión {{ $appointment->series_occurrence }}</p>
                @endif
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 ring-1 ring-emerald-200">Programada</span>
        </div>

        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="p-6 sm:p-8">
            @csrf
            @method('PUT')
            @include('appointments._form')

            @if($appointment->isRecurring())
                <div class="mt-6 rounded-2xl border border-violet-100 bg-violet-50/70 p-5">
                    <p class="text-sm font-bold text-violet-900">¿A qué citas quieres aplicar estos cambios?</p>
                    <p class="mt-1 text-xs text-violet-700/70">URPE validará todas las sesiones afectadas antes de modificar la serie. Si alguna presenta conflicto, no se cambiará ninguna.</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach([
                            'single' => ['Solo esta cita', 'Modifica únicamente esta sesión.'],
                            'following' => ['Esta y las siguientes', 'Aplica el cambio desde esta sesión hacia adelante.'],
                            'series' => ['Toda la serie', 'Aplica el cambio a todas las sesiones programadas de la serie.'],
                        ] as $scope => [$label, $description])
                            <label class="cursor-pointer rounded-xl border border-violet-100 bg-white p-4 transition has-[:checked]:border-violet-500 has-[:checked]:ring-2 has-[:checked]:ring-violet-100">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="scope" value="{{ $scope }}" @checked(old('scope', 'single') === $scope) class="mt-1 border-violet-300 text-violet-600 focus:ring-violet-500">
                                    <span><span class="block text-sm font-bold text-slate-800">{{ $label }}</span><span class="mt-1 block text-xs leading-5 text-slate-500">{{ $description }}</span></span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-fuchsia-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-fuchsia-700 hover:to-violet-700">Guardar cambios</button>
            </div>
        </form>

        <div class="border-t border-rose-100 bg-rose-50/50 p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-rose-900">Cancelar cita</p>
                    <p class="mt-1 text-xs leading-5 text-rose-700/70">La cancelación es lógica y queda registrada en auditoría.</p>
                </div>
                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="grid w-full gap-3 lg:max-w-3xl lg:grid-cols-[minmax(0,1fr)_220px_auto]" data-confirm="¿Confirmas la cancelación seleccionada?">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="cancellation_reason" placeholder="Motivo opcional" class="w-full rounded-xl border-rose-200 bg-white text-sm focus:border-rose-500 focus:ring-rose-500">
                    @if($appointment->isRecurring())
                        <select name="scope" class="w-full rounded-xl border-rose-200 bg-white text-sm font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500">
                            <option value="single">Solo esta cita</option>
                            <option value="following">Esta y las siguientes</option>
                            <option value="series">Toda la serie</option>
                        </select>
                    @else
                        <input type="hidden" name="scope" value="single">
                    @endif
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Cancelar cita</button>
                </form>
            </div>
        </div>
    </section>
</x-app-shell>
