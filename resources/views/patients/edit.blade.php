<x-app-shell title="Editar paciente" eyebrow="{{ $patient->folio }}">
    <x-slot:actions><a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Ver ficha</a></x-slot:actions>
    <div class="mx-auto max-w-4xl">
        <div class="mb-5"><h2 class="text-xl font-bold">{{ $patient->full_name }}</h2><p class="mt-1 text-sm text-slate-500">Modifica únicamente los datos administrativos necesarios. El expediente clínico se gestiona por separado.</p></div>
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')
            @include('patients._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6"><a href="{{ route('patients.show', $patient) }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancelar</a><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Guardar cambios</button></div>
        </form>
    </div>
</x-app-shell>
