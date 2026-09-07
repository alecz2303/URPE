<x-app-shell title="Editar paciente" eyebrow="{{ $patient->folio }}">
    <x-slot:actions>
        <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Ver ficha</a>
    </x-slot:actions>

    <div class="mx-auto max-w-4xl">
        <div class="mb-6 overflow-hidden rounded-3xl border border-fuchsia-200 bg-gradient-to-r from-fuchsia-50 via-rose-50 to-amber-50 shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-fuchsia-500 via-rose-400 to-amber-400"></div>
            <div class="px-6 py-5 sm:px-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700 ring-1 ring-fuchsia-200">Datos administrativos</span>
                        <h2 class="mt-3 text-xl font-black text-slate-900">{{ $patient->full_name }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Actualiza aquí únicamente su información administrativa. El expediente clínico permanece separado y protegido.</p>
                    </div>
                    <span class="rounded-full bg-white/80 px-3 py-1.5 font-mono text-xs font-bold text-slate-600 ring-1 ring-white">{{ $patient->folio }}</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('patients.update', $patient) }}" class="overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
            @csrf
            @method('PUT')
            <div class="border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50/80 to-white px-6 py-4 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700">Información del paciente</p>
            </div>
            <div class="p-6 sm:p-8">
                @include('patients._form')
                <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-fuchsia-100 pt-6">
                    <a href="{{ route('patients.show', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 hover:border-fuchsia-200 hover:bg-fuchsia-50">Cancelar</a>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-fuchsia-600 to-rose-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-fuchsia-700 hover:to-rose-600">Guardar cambios</button>
                </div>
            </div>
        </form>
    </div>
</x-app-shell>
