<x-app-shell title="Nueva cita" eyebrow="Agenda clínica">
    <x-slot:actions><a href="{{ route('appointments.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a agenda</a></x-slot:actions>

    <section class="max-w-5xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-7">
            <p class="text-sm text-slate-500">Programa una cita clínica seleccionando paciente, terapia, terapeutas y horario. La duración se calculará desde la terapia configurada.</p>
        </div>
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf
            @include('appointments._form')
            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('appointments.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Cancelar</a>
                <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Guardar cita</button>
            </div>
        </form>
    </section>
</x-app-shell>
