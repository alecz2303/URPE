<x-app-shell title="Bitácora clínica" eyebrow="Sesión terapéutica">
    <x-slot:actions>
        <a href="{{ route('session-logs.show', $appointment) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-50">Ver sesión</a>
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="grid gap-4 px-6 py-6 md:grid-cols-3">
            <div><p class="text-xs font-bold uppercase tracking-wide text-violet-700">Paciente</p><p class="mt-1 font-black text-slate-900">{{ $appointment->patient->full_name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-fuchsia-700">Terapia</p><p class="mt-1 font-black text-slate-900">{{ $appointment->therapy->name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Sesión</p><p class="mt-1 font-black text-slate-900">{{ $appointment->starts_at->translatedFormat('d M Y · H:i') }}</p></div>
        </div>
    </section>

    <form method="POST" action="{{ route('session-logs.update', $appointment) }}" class="space-y-6" data-swal-confirm data-swal-title="¿Guardar bitácora?" data-swal-text="Se registrará la evolución clínica de esta sesión." data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')

        <section class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Profesionales participantes</p>
            <p class="mt-2 text-sm text-slate-500">Se muestran los terapeutas actualmente asignados. Si hubo una sustitución, debe registrarse primero desde la agenda.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach($participants as $therapist)
                    <label class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/50 px-4 py-3">
                        <input type="checkbox" name="participant_ids[]" value="{{ $therapist->id }}" @checked(in_array($therapist->id, old('participant_ids', $sessionLog?->participatingTherapists?->pluck('id')->all() ?: $participants->pluck('id')->all()))) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-bold text-slate-700">{{ $therapist->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('participant_ids')<p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
        </section>

        <section class="grid gap-5 rounded-3xl border border-cyan-100 bg-white p-6 shadow-sm">
            @foreach([
                'treatment_activities' => ['Tratamiento / actividades realizadas', 'Describe las técnicas, ejercicios y actividades efectuadas durante la sesión.'],
                'patient_response' => ['Respuesta y evolución del paciente', 'Registra tolerancia, respuesta, avances o dificultades observadas.'],
                'observations_incidents' => ['Observaciones e incidencias', 'Anota hallazgos relevantes, incidencias o situaciones clínicas de la sesión.'],
                'home_recommendations' => ['Recomendaciones para casa', 'Indicaciones o recomendaciones compartidas con la familia.'],
                'next_session_objectives' => ['Objetivos para la siguiente sesión', 'Define puntos de seguimiento para la próxima atención.'],
            ] as $field => [$label, $help])
                <label class="block">
                    <span class="text-sm font-black text-slate-800">{{ $label }}</span>
                    <span class="mt-1 block text-xs text-slate-500">{{ $help }}</span>
                    <textarea name="{{ $field }}" rows="{{ $field === 'treatment_activities' ? 5 : 4 }}" {{ $field === 'treatment_activities' ? 'required' : '' }} maxlength="10000" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">{{ old($field, $sessionLog?->{$field}) }}</textarea>
                    @error($field)<span class="mt-1 block text-sm font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            @endforeach
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <button type="submit" name="complete" value="0" class="rounded-xl border border-cyan-200 bg-white px-5 py-3 text-sm font-bold text-cyan-800 hover:bg-cyan-50">Guardar borrador</button>
            <button type="submit" name="complete" value="1" class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Completar y cerrar bitácora</button>
        </div>
    </form>
</x-app-shell>
