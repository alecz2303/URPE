<x-app-shell title="Expediente clínico base" eyebrow="Paciente">
    <x-slot:actions>
        <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-cyan-100 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:border-cyan-300 hover:text-cyan-800">Ficha administrativa</a>
        @can('clinical_records.manage')
            <a href="{{ route('clinical-records.edit', $patient) }}" class="rounded-xl bg-gradient-to-r from-cyan-600 to-teal-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-cyan-700 hover:to-teal-600">
                {{ $clinicalRecord ? 'Editar expediente' : 'Iniciar expediente' }}
            </a>
        @endcan
    </x-slot:actions>

    <section class="relative overflow-hidden rounded-3xl border border-cyan-100 bg-gradient-to-r from-white via-cyan-50/80 to-emerald-50/70 p-6 shadow-sm sm:p-7">
        <div class="pointer-events-none absolute -right-8 -bottom-12 h-28 w-28 rounded-full border-[18px] border-pink-100/80"></div>
        <div class="pointer-events-none absolute right-16 -top-10 h-20 w-20 rounded-full bg-amber-100/70"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="inline-flex rounded-full bg-cyan-100 px-3 py-1 font-mono text-xs font-bold uppercase tracking-[0.15em] text-cyan-800">{{ $patient->folio }}</p>
                <h2 class="mt-3 text-2xl font-extrabold text-slate-950">{{ $patient->full_name }}</h2>
                <p class="mt-2 text-sm text-slate-600">Historia clínica basal y plan terapéutico.</p>
            </div>
            <span class="w-fit rounded-full px-3 py-1.5 text-xs font-bold shadow-sm {{ $clinicalRecord ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $clinicalRecord ? '✓ Expediente iniciado' : 'Pendiente de iniciar' }}</span>
        </div>
        @if($clinicalRecord)
            <div class="relative mt-5 flex flex-wrap gap-x-6 gap-y-2 border-t border-cyan-100/80 pt-4 text-xs font-medium text-slate-500">
                <span>Creado: {{ $clinicalRecord->created_at?->format('d/m/Y H:i') }}</span>
                <span>Actualizado: {{ $clinicalRecord->updated_at?->format('d/m/Y H:i') }}</span>
                @if($clinicalRecord->updater)
                    <span>Por: {{ $clinicalRecord->updater->name }}</span>
                @endif
            </div>
        @endif
    </section>

    @if(!$clinicalRecord)
        <section class="mt-6 rounded-3xl border border-dashed border-cyan-200 bg-gradient-to-br from-white to-cyan-50 px-6 py-14 text-center shadow-sm">
            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-cyan-100 text-2xl">✦</div>
            <h3 class="mt-4 font-bold">Aún no hay información clínica registrada</h3>
            <p class="mt-2 text-sm text-slate-500">El expediente puede iniciarse cuando exista información clínica basal.</p>
            @can('clinical_records.manage')
                <a href="{{ route('clinical-records.edit', $patient) }}" class="mt-5 inline-flex rounded-xl bg-cyan-600 px-4 py-2 text-sm font-bold text-white">Iniciar expediente →</a>
            @endcan
        </section>
    @else
        @php
            $sections = [
                ['label' => 'Antecedentes médicos', 'value' => $clinicalRecord->medical_history, 'icon' => '✚', 'border' => 'border-l-pink-500', 'iconBg' => 'bg-pink-100 text-pink-600', 'title' => 'text-pink-600'],
                ['label' => 'Antecedentes prenatales y perinatales', 'value' => $clinicalRecord->prenatal_perinatal_history, 'icon' => '♡', 'border' => 'border-l-emerald-500', 'iconBg' => 'bg-emerald-100 text-emerald-600', 'title' => 'text-emerald-600'],
                ['label' => 'Antecedentes del desarrollo', 'value' => $clinicalRecord->developmental_history, 'icon' => '⌁', 'border' => 'border-l-violet-500', 'iconBg' => 'bg-violet-100 text-violet-600', 'title' => 'text-violet-600'],
                ['label' => 'Antecedentes familiares', 'value' => $clinicalRecord->family_history, 'icon' => '♧', 'border' => 'border-l-amber-400', 'iconBg' => 'bg-amber-100 text-amber-600', 'title' => 'text-amber-600'],
                ['label' => 'Diagnósticos', 'value' => $clinicalRecord->diagnoses, 'icon' => '◉', 'border' => 'border-l-sky-500', 'iconBg' => 'bg-sky-100 text-sky-600', 'title' => 'text-sky-600'],
                ['label' => 'Objetivos terapéuticos', 'value' => $clinicalRecord->therapeutic_objectives, 'icon' => '◎', 'border' => 'border-l-fuchsia-500', 'iconBg' => 'bg-fuchsia-100 text-fuchsia-600', 'title' => 'text-fuchsia-600'],
                ['label' => 'Observaciones clínicas generales', 'value' => $clinicalRecord->general_observations, 'icon' => '✦', 'border' => 'border-l-cyan-500', 'iconBg' => 'bg-cyan-100 text-cyan-600', 'title' => 'text-cyan-700'],
            ];
        @endphp
        <section class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach($sections as $section)
                <article class="rounded-2xl border border-slate-200 border-l-4 {{ $section['border'] }} bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $section['label'] === 'Observaciones clínicas generales' ? 'lg:col-span-2' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-xl font-bold {{ $section['iconBg'] }}">{{ $section['icon'] }}</div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-extrabold uppercase tracking-[0.15em] {{ $section['title'] }}">{{ $section['label'] }}</h3>
                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $section['value'] ?: 'Sin información registrada.' }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</x-app-shell>
