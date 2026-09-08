<x-app-shell title="Bitácora clínica" eyebrow="Sesión terapéutica">
    <x-slot:actions>
        @if($canManage && ! $sessionLog?->isCompleted() && ! $appointment->isCancelled())
            <a href="{{ route('session-logs.edit', $appointment) }}" class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">{{ $sessionLog ? 'Continuar captura' : 'Capturar bitácora' }}</a>
        @endif
        <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a agenda</a>
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="grid gap-4 px-6 py-6 md:grid-cols-4">
            <div><p class="text-xs font-bold uppercase tracking-wide text-violet-700">Paciente</p><p class="mt-1 font-black text-slate-900">{{ $appointment->patient->full_name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-fuchsia-700">Terapia</p><p class="mt-1 font-black text-slate-900">{{ $appointment->therapy->name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Fecha y hora</p><p class="mt-1 font-black text-slate-900">{{ $appointment->starts_at->translatedFormat('d M Y · H:i') }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Estado</p><p class="mt-1 font-black text-slate-900">{{ $sessionLog?->isCompleted() ? 'Completada' : ($sessionLog ? 'Borrador' : 'Pendiente') }}</p></div>
        </div>
    </section>

    @if($sessionLog)
        <section class="mb-6 rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Terapeutas que participaron</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($sessionLog->participatingTherapists as $therapist)
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-bold text-emerald-800 ring-1 ring-emerald-100">{{ $therapist->name }}</span>
                @endforeach
            </div>
        </section>

        @php
            $sections = [
                'treatment_activities' => ['Tratamiento / actividades realizadas', 'text-cyan-700'],
                'patient_response' => ['Respuesta y evolución del paciente', 'text-violet-700'],
                'observations_incidents' => ['Observaciones e incidencias', 'text-rose-700'],
                'home_recommendations' => ['Recomendaciones para casa', 'text-emerald-700'],
                'next_session_objectives' => ['Objetivos para la siguiente sesión', 'text-amber-700'],
            ];
        @endphp
        <section class="grid gap-4 lg:grid-cols-2">
            @foreach($sections as $field => [$label, $labelClass])
                <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm {{ $field === 'treatment_activities' ? 'lg:col-span-2' : '' }}">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] {{ $labelClass }}">{{ $label }}</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $sessionLog->{$field} ?: 'Sin información registrada.' }}</p>
                </article>
            @endforeach
        </section>
    @else
        <section class="rounded-3xl border border-dashed border-violet-200 bg-violet-50/60 p-8 text-center">
            <p class="text-lg font-black text-violet-900">Esta sesión todavía no tiene bitácora clínica.</p>
            <p class="mt-2 text-sm text-violet-700">La captura queda disponible únicamente para profesionales autorizados y asignados a la cita.</p>
        </section>
    @endif

    @if($appointment->therapistChanges->isNotEmpty())
        <section class="mt-6 rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">Historial de sustituciones</p>
            <div class="mt-4 space-y-3">
                @foreach($appointment->therapistChanges as $change)
                    <div class="rounded-2xl bg-amber-50/60 p-4 text-sm text-slate-700">
                        <span class="font-bold">{{ $change->removedTherapist?->name ?: '—' }}</span>
                        <span class="mx-2 text-slate-400">→</span>
                        <span class="font-bold">{{ $change->addedTherapist?->name ?: '—' }}</span>
                        <span class="ml-2 text-xs text-slate-500">{{ $change->created_at->format('d/m/Y H:i') }}</span>
                        @if($change->reason)<p class="mt-1 text-xs text-slate-500">{{ $change->reason }}</p>@endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-app-shell>
