<x-app-shell title="Evolución clínica" eyebrow="Línea longitudinal del paciente">
    <x-slot:actions>
        <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver al paciente</a>
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="px-6 py-6">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Paciente</p>
            <h2 class="mt-2 text-2xl font-black text-slate-900">{{ $patient->full_name }}</h2>
            <p class="mt-2 text-sm text-slate-600">Evolución clínica longitudinal construida a partir de las bitácoras de sesión existentes, sin duplicar la información del expediente.</p>
        </div>
    </section>

    <div class="relative space-y-5 before:absolute before:bottom-3 before:left-[11px] before:top-3 before:w-px before:bg-violet-200">
        @forelse($sessionLogs as $sessionLog)
            <article class="relative pl-9">
                <span class="absolute left-0 top-6 h-6 w-6 rounded-full border-4 border-white bg-violet-500 shadow ring-1 ring-violet-200"></span>
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-black text-slate-900">{{ $sessionLog->appointment->starts_at->translatedFormat('d M Y · H:i') }}</p>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $sessionLog->isCompleted() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $sessionLog->isCompleted() ? 'Completada' : 'Borrador' }}</span>
                                @if($sessionLog->amendments->isNotEmpty())
                                    <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-bold text-violet-800">{{ $sessionLog->amendments->count() }} {{ $sessionLog->amendments->count() === 1 ? 'enmienda' : 'enmiendas' }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm font-bold text-violet-700">{{ $sessionLog->therapy->name }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ $sessionLog->participatingTherapists->pluck('name')->implode(' · ') }}</p>
                        </div>
                        <a href="{{ route('session-logs.show', $sessionLog->appointment) }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-900">Abrir bitácora →</a>
                    </div>
                    <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Resumen clínico</p>
                        <p class="mt-2 line-clamp-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $sessionLog->patient_response ?: $sessionLog->treatment_activities }}</p>
                    </div>
                </div>
            </article>
        @empty
            <section class="rounded-3xl border border-dashed border-violet-200 bg-violet-50/60 p-10 text-center">
                <p class="font-black text-violet-900">Aún no hay sesiones clínicas documentadas para este paciente.</p>
            </section>
        @endforelse
    </div>
</x-app-shell>
