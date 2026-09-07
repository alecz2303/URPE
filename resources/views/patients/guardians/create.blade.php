<x-app-shell title="Agregar responsable" eyebrow="Pacientes">
    <x-slot:actions><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-fuchsia-200 bg-white px-4 py-2.5 text-sm font-bold text-fuchsia-800 shadow-sm hover:bg-fuchsia-50">Volver a la ficha</a></x-slot:actions>

    <div class="mb-6 overflow-hidden rounded-3xl border border-fuchsia-200 bg-gradient-to-r from-fuchsia-50 via-rose-50 to-amber-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-fuchsia-500 via-rose-400 to-amber-400"></div>
        <div class="px-6 py-5">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-fuchsia-700">Paciente</p>
            <p class="mt-1 text-lg font-black text-slate-900">{{ $patient->full_name }}</p>
            <p class="mt-1 font-mono text-xs text-slate-500">{{ $patient->folio }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Agrega un responsable y define su relación y datos de contacto para mantener clara la red de apoyo del paciente.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('patients.guardians.store', $patient) }}" class="overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
        @csrf
        <div class="border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50/80 to-white px-6 py-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700">Datos del responsable</p>
        </div>
        <div class="p-6">
            @include('patients.guardians._form', ['guardian' => null, 'linkedGuardian' => null])
            <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-fuchsia-100 pt-5"><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500 hover:border-fuchsia-200 hover:bg-fuchsia-50">Cancelar</a><button class="rounded-xl bg-gradient-to-r from-fuchsia-600 to-rose-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-fuchsia-700 hover:to-rose-600">Guardar responsable</button></div>
        </div>
    </form>
</x-app-shell>
