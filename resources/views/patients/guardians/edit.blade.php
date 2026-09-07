<x-app-shell title="Editar responsable" eyebrow="Pacientes">
    <x-slot:actions><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-50">Volver a la ficha</a></x-slot:actions>

    <div class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-rose-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-rose-400"></div>
        <div class="px-6 py-5">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Paciente</p>
            <p class="mt-1 text-lg font-black text-slate-900">{{ $patient->full_name }}</p>
            <p class="mt-1 font-mono text-xs text-slate-500">{{ $patient->folio }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Actualiza la información del responsable manteniendo su vínculo con la ficha del paciente.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('patients.guardians.update', [$patient, $guardian]) }}" class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
        @csrf
        @method('PUT')
        <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50/80 to-white px-6 py-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-violet-700">Datos del responsable</p>
        </div>
        <div class="p-6">
            @include('patients.guardians._form', ['guardian' => $guardian, 'linkedGuardian' => $linkedGuardian])
            <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-violet-100 pt-5"><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500 hover:border-violet-200 hover:bg-violet-50">Cancelar</a><button class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Guardar cambios</button></div>
        </div>
    </form>
</x-app-shell>
