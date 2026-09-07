<x-app-shell title="Editar cita" eyebrow="Agenda clínica">
    <x-slot:actions><a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a agenda</a></x-slot:actions>

    <section class="max-w-5xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div><p class="font-mono text-xs font-bold uppercase tracking-[0.15em] text-cyan-700">Cita #{{ $appointment->id }}</p><h2 class="mt-2 text-xl font-bold">{{ $appointment->patient->full_name }}</h2><p class="mt-1 text-sm text-slate-500">Reprograma terapia, terapeutas u horario. El paciente permanece vinculado a esta cita.</p></div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Programada</span>
        </div>
        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
            @csrf
            @method('PUT')
            @include('appointments._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a>
                <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Guardar cambios</button>
            </div>
        </form>
    </section>
</x-app-shell>
