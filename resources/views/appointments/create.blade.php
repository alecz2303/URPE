<x-app-shell title="Nueva cita" eyebrow="Agenda clínica">
    <x-slot:actions><a href="{{ route('appointments.index') }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a agenda</a></x-slot:actions>

    <section class="max-w-5xl overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
        <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50 via-fuchsia-50 to-amber-50 px-6 py-6 sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Programación clínica</p>
            <h2 class="mt-2 text-xl font-bold text-slate-900">Agenda una nueva sesión</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Selecciona paciente, terapia, terapeutas y horario. La duración y la cantidad de terapeutas se validan con la configuración clínica existente.</p>
        </div>
        <form method="POST" action="{{ route('appointments.store') }}" class="p-6 sm:p-8">
            @csrf
            @include('appointments._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('appointments.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-cyan-700 hover:to-teal-700">Guardar cita</button>
            </div>
        </form>
    </section>
</x-app-shell>
