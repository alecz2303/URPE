<x-app-shell title="Resultado HINE" eyebrow="{{ $patient->full_name }}">
    <x-slot:actions>
        <a href="{{ route('patients.hine-assessments.index', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Historial HINE</a>
    </x-slot:actions>

    <div class="space-y-6">
        <section class="rounded-3xl border border-violet-100 bg-gradient-to-r from-violet-50 via-white to-cyan-50 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">HINE {{ $assessment->instrument_version }}</p>
                    <h2 class="mt-1 text-xl font-black text-slate-900">{{ $assessment->examination_date->format('d/m/Y') }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Evaluación {{ $assessment->status === 'finalized' ? 'finalizada' : 'en borrador' }} · {{ $assessment->author?->name ?: 'Autor no disponible' }}</p>
                </div>
                <div class="rounded-2xl bg-white px-5 py-4 text-center ring-1 ring-violet-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Puntuación global</p>
                    <p class="mt-1 text-4xl font-black text-violet-700">{{ $hine->global_score ?? '—' }}<span class="text-base text-slate-400"> / 78</span></p>
                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ $hine->asymmetry_count }} asimetrías</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach($sections as $sectionKey => $section)
                @php($field = match($sectionKey) {'cranial_nerves'=>'cranial_nerves_score','posture'=>'posture_score','movements'=>'movements_score','tone'=>'tone_score','reflexes_reactions'=>'reflexes_reactions_score'})
                <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $section['label'] }}</p>
                    <p class="mt-2 text-2xl font-black text-slate-800">{{ $hine->{$field} ?? '—' }} <span class="text-sm text-slate-400">/ {{ $section['maximum'] }}</span></p>
                </div>
            @endforeach
        </section>

        <section class="rounded-3xl border border-cyan-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Apoyo para la interpretación</p>
            <h3 class="mt-1 text-lg font-black text-slate-900">Referencia del material HINE proporcionado</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $interpretation['note'] }}</p>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">Puntuación global</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($interpretation['global_score_ranges'] as $range)
                            <span class="rounded-full bg-white px-3 py-1.5 text-sm font-bold text-slate-700 ring-1 ring-slate-200">{{ $range['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-2xl bg-amber-50 p-4">
                    <p class="text-xs font-bold uppercase text-amber-700">Asimetrías</p>
                    <p class="mt-3 text-2xl font-black text-amber-900">≥ {{ $interpretation['asymmetry_attention_threshold'] }}</p>
                    <p class="mt-1 text-xs text-amber-800">Referencia destacada en el apoyo de interpretación.</p>
                </div>
                <div class="rounded-2xl bg-violet-50 p-4">
                    <p class="text-xs font-bold uppercase text-violet-700">Alto riesgo en PC según edad</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        @foreach($interpretation['high_risk_cutoffs_by_age_months'] as $months => $cutoff)
                            <div class="rounded-xl bg-white px-3 py-2 font-semibold text-violet-900">{{ $months }} meses <strong class="float-right">{{ $cutoff }}</strong></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <p class="mt-4 text-xs font-semibold text-slate-500">{{ $interpretation['age_rule'] }}</p>
        </section>
    </div>
</x-app-shell>
