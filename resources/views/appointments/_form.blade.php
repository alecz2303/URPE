@php
    $editing = isset($appointment);
    $selectedTherapists = collect(old('therapist_ids', $editing ? $appointment->therapists->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $startsAtValue = old('starts_at', $editing ? $appointment->starts_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    @unless($editing)
        <div>
            <label for="patient_id" class="mb-2 block text-sm font-semibold text-slate-700">Paciente</label>
            <select id="patient_id" name="patient_id" required class="w-full rounded-xl border-slate-300 focus:border-cyan-600 focus:ring-cyan-600">
                <option value="">Selecciona un paciente</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" @selected((int) old('patient_id') === $patient->id)>{{ $patient->full_name }} · {{ $patient->folio }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div>
            <p class="mb-2 block text-sm font-semibold text-slate-700">Paciente</p>
            <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-200">{{ $appointment->patient->full_name }} · {{ $appointment->patient->folio }}</div>
        </div>
    @endunless

    <div>
        <label for="therapy_id" class="mb-2 block text-sm font-semibold text-slate-700">Terapia</label>
        <select id="therapy_id" name="therapy_id" required class="w-full rounded-xl border-slate-300 focus:border-cyan-600 focus:ring-cyan-600">
            <option value="">Selecciona una terapia</option>
            @foreach($therapies as $therapy)
                <option value="{{ $therapy->id }}" @selected((int) old('therapy_id', $editing ? $appointment->therapy_id : 0) === $therapy->id)>
                    {{ $therapy->name }} · {{ $therapy->duration_minutes }} min · {{ $therapy->required_therapists }} terapeuta(s)
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="starts_at" class="mb-2 block text-sm font-semibold text-slate-700">Fecha y hora de inicio</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ $startsAtValue }}" required class="w-full rounded-xl border-slate-300 focus:border-cyan-600 focus:ring-cyan-600">
    </div>

    <div class="lg:col-span-2">
        <p class="mb-2 block text-sm font-semibold text-slate-700">Terapeutas</p>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($therapists as $therapist)
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    <input type="checkbox" name="therapist_ids[]" value="{{ $therapist->id }}" @checked(in_array($therapist->id, $selectedTherapists, true)) class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                    <span>{{ $therapist->name }}</span>
                </label>
            @endforeach
        </div>
        <p class="mt-2 text-xs text-slate-500">La cantidad seleccionada debe coincidir exactamente con el requisito de la terapia.</p>
    </div>
</div>

@if($errors->any())
    <div class="mt-6 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
