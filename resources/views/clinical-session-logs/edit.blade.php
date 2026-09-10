<x-app-shell title="Sesión clínica" eyebrow="Atención terapéutica">
    <x-slot:actions>
        <a href="{{ route('session-logs.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">Sesiones clínicas</a>
        <a href="{{ route('session-logs.show', $appointment) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-50">Ver sesión</a>
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="grid gap-4 px-6 py-6 md:grid-cols-4">
            <div><p class="text-xs font-bold uppercase tracking-wide text-violet-700">Paciente</p><p class="mt-1 font-black text-slate-900">{{ $appointment->patient->full_name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-fuchsia-700">Terapia</p><p class="mt-1 font-black text-slate-900">{{ $appointment->therapy->name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Sesión</p><p class="mt-1 font-black text-slate-900">{{ $appointment->starts_at->translatedFormat('d M Y · H:i') }}</p></div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Guardado</p>
                <div id="autosave-status" class="mt-1 inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1.5 text-xs font-black text-emerald-700 ring-1 ring-emerald-100" aria-live="polite">
                    <span id="autosave-dot" class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span id="autosave-label">{{ $sessionLog ? 'Borrador cargado' : 'Listo para capturar' }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <form id="clinical-session-form" method="POST" action="{{ route('session-logs.update', $appointment) }}" data-autosave-url="{{ route('session-logs.autosave', $appointment) }}" class="space-y-6">
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
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Registro de la sesión</p>
                    <p class="mt-1 text-sm text-slate-500">Los cambios se guardan automáticamente mientras escribes. Completar la sesión la cierra para edición.</p>
                </div>

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

            <div class="sticky bottom-4 z-20 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-xl backdrop-blur">
                <p class="text-xs font-semibold text-slate-500">El borrador se autoguarda. Usa “Guardar y salir” cuando termines por ahora.</p>
                <div class="flex flex-wrap justify-end gap-3">
                    <button type="submit" name="save_and_exit" value="1" class="rounded-xl border border-cyan-200 bg-white px-5 py-3 text-sm font-bold text-cyan-800 hover:bg-cyan-50">Guardar y salir</button>
                    <button type="submit" name="complete" value="1" data-complete-session class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Completar sesión</button>
                </div>
            </div>
        </form>

        <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
            <section class="rounded-3xl border border-violet-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Contexto longitudinal</p>
                        <h2 class="mt-1 text-lg font-black text-slate-900">Sesiones recientes</h2>
                    </div>
                    <a href="{{ route('session-logs.patient-history', $appointment->patient) }}" class="text-xs font-bold text-violet-700 hover:text-violet-900">Ver evolución</a>
                </div>
                <p class="mt-2 text-sm text-slate-500">Referencia clínica del paciente. Las sesiones previas son de solo lectura desde este espacio.</p>

                <div class="mt-4 space-y-3">
                    @forelse($recentSessions as $recentSession)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">{{ $recentSession->appointment->starts_at->translatedFormat('d M Y · H:i') }}</p>
                                    <p class="mt-1 text-sm font-black text-slate-800">{{ $recentSession->therapy->name }}</p>
                                </div>
                                @if($recentSession->amendments->isNotEmpty())
                                    <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-black text-amber-700 ring-1 ring-amber-100">Enmendada</span>
                                @endif
                            </div>

                            @if($recentSession->patient_response)
                                <div class="mt-3">
                                    <p class="text-[10px] font-black uppercase tracking-wide text-cyan-700">Evolución</p>
                                    <p class="mt-1 line-clamp-3 text-xs leading-5 text-slate-600">{{ $recentSession->patient_response }}</p>
                                </div>
                            @endif

                            @if($recentSession->next_session_objectives)
                                <div class="mt-3 rounded-xl bg-violet-50 p-3">
                                    <p class="text-[10px] font-black uppercase tracking-wide text-violet-700">Objetivo previo</p>
                                    <p class="mt-1 line-clamp-3 text-xs leading-5 text-slate-700">{{ $recentSession->next_session_objectives }}</p>
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach($recentSession->participatingTherapists as $therapist)
                                    <span class="rounded-full bg-white px-2 py-1 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">{{ $therapist->name }}</span>
                                @endforeach
                            </div>

                            <a href="{{ route('session-logs.show', $recentSession->appointment) }}" class="mt-3 inline-flex text-xs font-black text-cyan-700 hover:text-cyan-900">Ver sesión completa →</a>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center">
                            <p class="text-sm font-bold text-slate-600">Aún no hay sesiones completadas previas.</p>
                            <p class="mt-1 text-xs text-slate-400">La evolución aparecerá aquí conforme avance la atención.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('clinical-session-form');
            const statusLabel = document.getElementById('autosave-label');
            const statusDot = document.getElementById('autosave-dot');
            if (! form || ! statusLabel || ! statusDot) return;

            const autosaveUrl = form.dataset.autosaveUrl;
            let timer = null;
            let requestController = null;
            let submitting = false;

            const setStatus = (state, text) => {
                statusLabel.textContent = text;
                statusDot.className = 'h-2 w-2 rounded-full';
                if (state === 'saving') statusDot.classList.add('bg-amber-400', 'animate-pulse');
                if (state === 'saved') statusDot.classList.add('bg-emerald-500');
                if (state === 'error') statusDot.classList.add('bg-rose-500');
            };

            const autosave = async () => {
                if (submitting || ! autosaveUrl) return;

                requestController?.abort();
                requestController = new AbortController();
                setStatus('saving', 'Guardando…');

                const payload = new FormData(form);
                payload.delete('_method');
                payload.delete('complete');
                payload.delete('save_and_exit');

                try {
                    const response = await fetch(autosaveUrl, {
                        method: 'POST',
                        body: payload,
                        headers: { 'Accept': 'application/json' },
                        signal: requestController.signal,
                    });

                    const data = await response.json().catch(() => ({}));
                    if (! response.ok) throw new Error(data.message || 'No se pudo guardar el borrador.');

                    setStatus('saved', `Guardado ${data.saved_at || ''}`.trim());
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    setStatus('error', 'Error al guardar');
                }
            };

            const scheduleAutosave = () => {
                if (submitting) return;
                clearTimeout(timer);
                setStatus('saving', 'Cambios pendientes…');
                timer = setTimeout(autosave, 900);
            };

            form.querySelectorAll('textarea, input[type="checkbox"]').forEach((field) => {
                field.addEventListener(field.type === 'checkbox' ? 'change' : 'input', scheduleAutosave);
            });

            form.addEventListener('submit', async (event) => {
                if (submitting) return;

                const submitter = event.submitter;
                const isCompleting = submitter?.matches('[data-complete-session]');

                if (isCompleting) {
                    event.preventDefault();
                    const confirmed = window.Swal
                        ? await Swal.fire({
                            icon: 'question',
                            title: '¿Completar sesión?',
                            text: 'La sesión quedará cerrada para edición. Cualquier corrección posterior deberá registrarse mediante una enmienda.',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, completar sesión',
                            cancelButtonText: 'Seguir capturando',
                        }).then(result => result.isConfirmed)
                        : window.confirm('¿Completar sesión? Después solo podrá corregirse mediante una enmienda.');

                    if (! confirmed) return;

                    submitting = true;
                    clearTimeout(timer);
                    form.requestSubmit(submitter);
                    return;
                }

                submitting = true;
                clearTimeout(timer);
            });
        });
    </script>
</x-app-shell>
