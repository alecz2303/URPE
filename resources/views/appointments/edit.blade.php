<x-app-shell title="Editar cita" eyebrow="Agenda clínica">
    <x-slot:actions><a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a agenda</a></x-slot:actions>

    <section class="max-w-5xl overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50 via-violet-50 to-cyan-50 px-6 py-6 sm:px-8">
            <div><p class="font-mono text-xs font-bold uppercase tracking-[0.15em] text-fuchsia-700">Cita #{{ $appointment->id }}</p><h2 class="mt-2 text-xl font-bold text-slate-900">{{ $appointment->patient->full_name }}</h2><p class="mt-1 text-sm text-slate-600">Reprograma terapia, terapeutas u horario. El paciente permanece vinculado a esta cita.</p></div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 ring-1 ring-emerald-200">Programada</span>
        </div>
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="p-6 sm:p-8">
            @csrf
            @method('PUT')
            @include('appointments._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-fuchsia-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:from-fuchsia-700 hover:to-violet-700">Guardar cambios</button>
            </div>
        </form>
    </section>
</x-app-shell>
