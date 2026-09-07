<x-app-shell title="{{ $clinicalRecord ? 'Editar expediente clínico' : 'Iniciar expediente clínico' }}" eyebrow="Paciente">
    <x-slot:actions><a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver al expediente</a></x-slot:actions>

    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="font-mono text-xs font-bold uppercase tracking-[0.15em] text-cyan-700">{{ $patient->folio }}</p>
        <h2 class="mt-2 text-2xl font-bold">{{ $patient->full_name }}</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Registra información clínica basal. Las evoluciones por sesión permanecen fuera de este expediente base.</p>
    </section>

    <form method="POST" action="{{ route('clinical-records.update', $patient) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6"><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Antecedentes</p><h3 class="mt-1 text-xl font-bold">Historia clínica basal</h3></div>
            <div class="grid gap-6 lg:grid-cols-2">
                <div><label for="medical_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes médicos</label><textarea id="medical_history" name="medical_history" rows="7" maxlength="10000" class="w-full">{{ old('medical_history', $clinicalRecord?->medical_history) }}</textarea>@error('medical_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="prenatal_perinatal_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes prenatales y perinatales</label><textarea id="prenatal_perinatal_history" name="prenatal_perinatal_history" rows="7" maxlength="10000" class="w-full">{{ old('prenatal_perinatal_history', $clinicalRecord?->prenatal_perinatal_history) }}</textarea>@error('prenatal_perinatal_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="developmental_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes del desarrollo</label><textarea id="developmental_history" name="developmental_history" rows="7" maxlength="10000" class="w-full">{{ old('developmental_history', $clinicalRecord?->developmental_history) }}</textarea>@error('developmental_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="family_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes familiares</label><textarea id="family_history" name="family_history" rows="7" maxlength="10000" class="w-full">{{ old('family_history', $clinicalRecord?->family_history) }}</textarea>@error('family_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6"><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Plan clínico</p><h3 class="mt-1 text-xl font-bold">Diagnóstico y objetivos</h3></div>
            <div class="grid gap-6 lg:grid-cols-2">
                <div><label for="diagnoses" class="mb-2 block text-sm font-semibold text-slate-700">Diagnósticos</label><textarea id="diagnoses" name="diagnoses" rows="8" maxlength="10000" class="w-full">{{ old('diagnoses', $clinicalRecord?->diagnoses) }}</textarea>@error('diagnoses')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="therapeutic_objectives" class="mb-2 block text-sm font-semibold text-slate-700">Objetivos terapéuticos</label><textarea id="therapeutic_objectives" name="therapeutic_objectives" rows="8" maxlength="10000" class="w-full">{{ old('therapeutic_objectives', $clinicalRecord?->therapeutic_objectives) }}</textarea>@error('therapeutic_objectives')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="lg:col-span-2"><label for="general_observations" class="mb-2 block text-sm font-semibold text-slate-700">Observaciones clínicas generales</label><textarea id="general_observations" name="general_observations" rows="7" maxlength="10000" class="w-full">{{ old('general_observations', $clinicalRecord?->general_observations) }}</textarea>@error('general_observations')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <div class="flex justify-end gap-3"><a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">{{ $clinicalRecord ? 'Guardar cambios' : 'Iniciar expediente' }}</button></div>
    </form>
</x-app-shell>
