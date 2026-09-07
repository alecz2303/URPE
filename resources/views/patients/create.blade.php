<x-app-shell title="Nuevo paciente" eyebrow="Pacientes">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 rounded-2xl border border-cyan-100 bg-cyan-50/60 px-5 py-4">
            <p class="text-sm font-semibold text-cyan-900">Primero registra los datos administrativos del paciente.</p>
            <p class="mt-1 text-sm text-cyan-800/80">Después podrás agregar responsables y consultar su expediente desde la ficha del paciente.</p>
        </div>
        <form method="POST" action="{{ route('patients.store') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @include('patients._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6"><a href="{{ route('patients.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancelar</a><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Guardar paciente</button></div>
        </form>
    </div>
</x-app-shell>
