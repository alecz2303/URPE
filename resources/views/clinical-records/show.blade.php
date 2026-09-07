<x-app-shell title="Expediente clínico base" eyebrow="Paciente">
    <x-slot:actions>
        <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Ficha administrativa</a>
        @can('clinical_records.manage')
            <a href="{{ route('clinical-records.edit', $patient) }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">
                {{ $clinicalRecord ? 'Editar expediente' : 'Iniciar expediente' }}
            </a>
        @endcan
    </x-slot:actions>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-[0.15em] text-cyan-700">{{ $patient->folio }}</p>
                <h2 class="mt-2 text-2xl font-bold">{{ $patient->full_name }}</h2>
                <p class="mt-2 text-sm text-slate-500">Historia clínica basal y plan terapéutico.</p>
            </div>
            <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $clinicalRecord ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $clinicalRecord ? 'Expediente iniciado' : 'Pendiente de iniciar' }}</span>
        </div>
        @if($clinicalRecord)
            <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                <span>Creado: {{ $clinicalRecord->created_at?->format('d/m/Y H:i') }}</span>
                <span>Actualizado: {{ $clinicalRecord->updated_at?->format('d/m/Y H:i') }}</span>
                @if($clinicalRecord->updater)
                    <span>Por: {{ $clinicalRecord->updater->name }}</span>
                @endif
            </div>
        @endif
    </section>

    @if(!$clinicalRecord)
        <section class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <h3 class="font-bold">Aún no hay información clínica registrada</h3>
            <p class="mt-2 text-sm text-slate-500">El expediente puede iniciarse cuando exista información clínica basal.</p>
            @can('clinical_records.manage')
                <a href="{{ route('clinical-records.edit', $patient) }}" class="mt-5 inline-flex text-sm font-bold text-cyan-700">Iniciar expediente →</a>
            @endcan
        </section>
    @else
        @php
            $sections = [
                'Antecedentes médicos' => $clinicalRecord->medical_history,
                'Antecedentes prenatales y perinatales' => $clinicalRecord->prenatal_perinatal_history,
                'Antecedentes del desarrollo' => $clinicalRecord->developmental_history,
                'Antecedentes familiares' => $clinicalRecord->family_history,
                'Diagnósticos' => $clinicalRecord->diagnoses,
                'Objetivos terapéuticos' => $clinicalRecord->therapeutic_objectives,
                'Observaciones clínicas generales' => $clinicalRecord->general_observations,
            ];
        @endphp
        <section class="mt-6 grid gap-5 lg:grid-cols-2">
            @foreach($sections as $label => $value)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm {{ $label === 'Observaciones clínicas generales' ? 'lg:col-span-2' : '' }}">
                    <h3 class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">{{ $label }}</h3>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $value ?: 'Sin información registrada.' }}</p>
                </article>
            @endforeach
        </section>
    @endif
</x-app-shell>
