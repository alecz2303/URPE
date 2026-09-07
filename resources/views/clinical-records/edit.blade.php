<x-app-shell title="{{ $clinicalRecord ? 'Editar expediente clínico' : 'Iniciar expediente clínico' }}" eyebrow="Paciente">
    <x-slot:actions>
        <a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-700 transition hover:border-violet-300 hover:bg-violet-50">Volver al expediente</a>
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-100 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 p-6 shadow-sm sm:p-7">
        <p class="font-mono text-xs font-black uppercase tracking-[0.15em] text-violet-700">{{ $patient->folio }}</p>
        <h2 class="mt-2 text-2xl font-black text-slate-900">{{ $patient->full_name }}</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Registra la información clínica basal del paciente. Las evoluciones por sesión permanecen fuera de este expediente base.</p>
    </section>

    <form method="POST" action="{{ route('clinical-records.update', $patient) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50 to-sky-50 px-6 py-5">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">01 · Antecedentes</p>
                <h3 class="mt-1 text-xl font-black text-slate-900">Historia clínica basal</h3>
            </div>
            <div class="grid gap-5 p-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-cyan-100 bg-cyan-50/35 p-4"><label for="medical_history" class="mb-2 block text-sm font-bold text-cyan-900">Antecedentes médicos</label><textarea id="medical_history" name="medical_history" rows="7" maxlength="10000" class="w-full">{{ old('medical_history', $clinicalRecord?->medical_history) }}</textarea>@error('medical_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50/35 p-4"><label for="prenatal_perinatal_history" class="mb-2 block text-sm font-bold text-fuchsia-900">Antecedentes prenatales y perinatales</label><textarea id="prenatal_perinatal_history" name="prenatal_perinatal_history" rows="7" maxlength="10000" class="w-full">{{ old('prenatal_perinatal_history', $clinicalRecord?->prenatal_perinatal_history) }}</textarea>@error('prenatal_perinatal_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/35 p-4"><label for="developmental_history" class="mb-2 block text-sm font-bold text-emerald-900">Antecedentes del desarrollo</label><textarea id="developmental_history" name="developmental_history" rows="7" maxlength="10000" class="w-full">{{ old('developmental_history', $clinicalRecord?->developmental_history) }}</textarea>@error('developmental_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="rounded-2xl border border-amber-100 bg-amber-50/35 p-4"><label for="family_history" class="mb-2 block text-sm font-bold text-amber-900">Antecedentes familiares</label><textarea id="family_history" name="family_history" rows="7" maxlength="10000" class="w-full">{{ old('family_history', $clinicalRecord?->family_history) }}</textarea>@error('family_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
            <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-rose-50 px-6 py-5">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-violet-700">02 · Plan clínico</p>
                <h3 class="mt-1 text-xl font-black text-slate-900">Diagnóstico y objetivos</h3>
            </div>
            <div class="grid gap-5 p-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-violet-100 bg-violet-50/35 p-4"><label for="diagnoses" class="mb-2 block text-sm font-bold text-violet-900">Diagnósticos</label><textarea id="diagnoses" name="diagnoses" rows="8" maxlength="10000" class="w-full">{{ old('diagnoses', $clinicalRecord?->diagnoses) }}</textarea>@error('diagnoses')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50/35 p-4"><label for="therapeutic_objectives" class="mb-2 block text-sm font-bold text-fuchsia-900">Objetivos terapéuticos</label><textarea id="therapeutic_objectives" name="therapeutic_objectives" rows="8" maxlength="10000" class="w-full">{{ old('therapeutic_objectives', $clinicalRecord?->therapeutic_objectives) }}</textarea>@error('therapeutic_objectives')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="rounded-2xl border border-sky-100 bg-sky-50/35 p-4 lg:col-span-2"><label for="general_observations" class="mb-2 block text-sm font-bold text-sky-900">Observaciones clínicas generales</label><textarea id="general_observations" name="general_observations" rows="7" maxlength="10000" class="w-full">{{ old('general_observations', $clinicalRecord?->general_observations) }}</textarea>@error('general_observations')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700">Cancelar</a><button type="submit" class="rounded-xl bg-gradient-to-r from-violet-600 via-fuchsia-600 to-cyan-600 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-violet-900/15 transition hover:scale-[1.02]">{{ $clinicalRecord ? 'Guardar cambios' : 'Iniciar expediente' }}</button></div>
    </form>
</x-app-shell>
