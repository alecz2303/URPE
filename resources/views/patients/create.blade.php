<x-app-shell title="Nuevo paciente" eyebrow="Pacientes">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 overflow-hidden rounded-3xl border border-cyan-200 bg-gradient-to-r from-cyan-50 via-sky-50 to-fuchsia-50 shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-cyan-500 via-sky-400 to-fuchsia-400"></div>
            <div class="px-6 py-5 sm:px-7">
                <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-cyan-700 ring-1 ring-cyan-200">Nueva ficha</span>
                <h2 class="mt-3 text-xl font-black text-slate-900">Comencemos con sus datos administrativos</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Después podrás agregar responsables, consultar su expediente clínico y programar citas desde la ficha del paciente.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('patients.store') }}" class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            @csrf
            <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50/80 to-white px-6 py-4 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-cyan-700">Información del paciente</p>
            </div>
            <div class="p-6 sm:p-8">
                @include('patients._form')
                <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-cyan-100 pt-6">
                    <a href="{{ route('patients.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 hover:border-cyan-200 hover:bg-cyan-50">Cancelar</a>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-cyan-700 hover:to-sky-700">Guardar paciente</button>
                </div>
            </div>
        </form>
    </div>
</x-app-shell>
