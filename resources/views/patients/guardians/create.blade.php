<x-app-shell title="Agregar responsable" eyebrow="Pacientes">
    <x-slot:actions><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Volver a la ficha</a></x-slot:actions>

    <div class="mb-6 rounded-2xl border border-cyan-100 bg-cyan-50 px-5 py-4">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Paciente</p>
        <p class="mt-1 font-bold text-slate-900">{{ $patient->full_name }}</p>
        <p class="mt-1 font-mono text-xs text-slate-500">{{ $patient->folio }}</p>
    </div>

    <form method="POST" action="{{ route('patients.guardians.store', $patient) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @include('patients.guardians._form', ['guardian' => null, 'linkedGuardian' => null])
        <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-5"><a href="{{ route('patients.show', $patient) }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-50">Cancelar</a><button class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Guardar responsable</button></div>
    </form>
</x-app-shell>
