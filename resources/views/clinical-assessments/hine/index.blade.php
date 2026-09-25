<x-app-shell title="Evaluaciones HINE" eyebrow="{{ $patient->full_name }}">
    <x-slot:actions>
        @can('clinical_assessments.manage')
            <a href="{{ route('patients.hine-assessments.create', $patient) }}" class="rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm">+ Nueva HINE</a>
        @endcan
        <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Volver al paciente</a>
    </x-slot:actions>

    <section class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
        <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50 to-white px-6 py-5">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Evaluaciones clínicas</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Hammersmith Infant Neurological Examination</h2>
            <p class="mt-2 text-sm text-slate-500">Historial del paciente. Cada evaluación se conserva como un registro independiente.</p>
        </div>
        <div class="space-y-3 p-6">
            @forelse($assessments as $assessment)
                <article class="flex flex-col gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-bold text-slate-800">{{ $assessment->examination_date->format('d/m/Y') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $assessment->instrument_version }} · {{ $assessment->author?->name ?: 'Autor no disponible' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $assessment->status === 'finalized' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $assessment->status === 'finalized' ? 'Finalizada' : 'Borrador' }}</span>
                        @if($assessment->hine?->global_score !== null)
                            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-800">{{ $assessment->hine->global_score }} / 78</span>
                        @endif
                        @if($assessment->status !== 'finalized')
                            <a href="{{ route('patients.hine-assessments.edit', [$patient, $assessment]) }}" class="rounded-xl bg-cyan-600 px-3 py-1.5 text-xs font-bold text-white">Continuar</a>
                        @else
                            <a href="{{ route('patients.hine-assessments.show', [$patient, $assessment]) }}" class="rounded-xl bg-violet-600 px-3 py-1.5 text-xs font-bold text-white">Ver resultado</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-cyan-50 p-6 text-sm leading-6 text-cyan-900">Este paciente todavía no tiene evaluaciones HINE registradas.</div>
            @endforelse
        </div>
    </section>
</x-app-shell>
