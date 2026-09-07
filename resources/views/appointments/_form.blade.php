@php
    $editing = isset($appointment);
    $selectedTherapists = collect(old('therapist_ids', $editing ? $appointment->therapists->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $startsAtValue = old('starts_at', $editing ? $appointment->starts_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    @unless($editing)
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">
            <label for="patient_id" class="mb-2 block text-sm font-bold text-cyan-900">Paciente</label>
            <select id="patient_id" name="patient_id" required class="w-full rounded-xl border-cyan-200 bg-white focus:border-cyan-600 focus:ring-cyan-600">
                <option value="">Selecciona un paciente</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" @selected((int) old('patient_id') === $patient->id)>{{ $patient->full_name }} · {{ $patient->folio }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">
            <p class="mb-2 block text-sm font-bold text-cyan-900">Paciente</p>
            <div class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-slate-700 ring-1 ring-cyan-100">{{ $appointment->patient->full_name }} · {{ $appointment->patient->folio }}</div>
        </div>
    @endunless

    <div class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50/60 p-4">
        <label for="therapy_id" class="mb-2 block text-sm font-bold text-fuchsia-900">Terapia</label>
        <select id="therapy_id" name="therapy_id" required class="w-full rounded-xl border-fuchsia-200 bg-white focus:border-fuchsia-500 focus:ring-fuchsia-500">
            <option value="">Selecciona una terapia</option>
            @foreach($therapies as $therapy)
                <option value="{{ $therapy->id }}" @selected((int) old('therapy_id', $editing ? $appointment->therapy_id : 0) === $therapy->id)>
                    {{ $therapy->name }} · {{ $therapy->duration_minutes }} min · {{ $therapy->required_therapists }} terapeuta(s)
                </option>
            @endforeach
        </select>
    </div>

    <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4">
        <label for="starts_at" class="mb-2 block text-sm font-bold text-amber-900">Fecha y hora de inicio</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ $startsAtValue }}" required class="w-full rounded-xl border-amber-200 bg-white focus:border-amber-500 focus:ring-amber-500">
    </div>

    <div class="rounded-2xl border border-violet-100 bg-violet-50/60 p-4 lg:col-span-2">
        <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
            <div><p class="text-sm font-bold text-violet-900">Terapeutas</p><p class="mt-1 text-xs text-violet-700/70">Selecciona exactamente la cantidad requerida por la terapia.</p></div>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($therapists as $therapist)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-violet-100 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-violet-300 hover:bg-violet-50">
                    <input type="checkbox" name="therapist_ids[]" value="{{ $therapist->id }}" @checked(in_array($therapist->id, $selectedTherapists, true)) class="rounded border-violet-300 text-violet-600 focus:ring-violet-500">
                    <span>{{ $therapist->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>

@if($errors->any())
    <div class="mt-6 rounded-2xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
