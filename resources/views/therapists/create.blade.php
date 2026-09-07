<x-app-shell title="Nuevo terapeuta" eyebrow="Equipo clínico">
    <x-slot:actions>
        <a href="{{ route('therapists.index') }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a terapeutas</a>
    </x-slot:actions>

    <div class="mb-6 overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-r from-emerald-50 via-cyan-50 to-sky-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-cyan-400 to-sky-400"></div>
        <div class="px-6 py-5 sm:px-7">
            <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 ring-1 ring-emerald-200">Nuevo integrante</span>
            <h2 class="mt-3 text-xl font-black text-slate-900">Agrega un terapeuta al equipo clínico</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Registra sus datos profesionales y disponibilidad semanal para que pueda participar en la agenda clínica.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('therapists.store') }}" class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm" data-swal-confirm data-swal-title="¿Crear terapeuta?" data-swal-text="Se guardará el perfil y su disponibilidad semanal." data-swal-confirm-text="Sí, crear">
        @csrf
        <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-50/80 to-white px-6 py-4 sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">Perfil y disponibilidad</p>
        </div>
        <div class="p-6 sm:p-8">
            @include('therapists._form')
            <div class="mt-8 flex justify-end border-t border-emerald-100 pt-6">
                <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-600 to-cyan-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-emerald-700 hover:to-cyan-700">Crear terapeuta</button>
            </div>
        </div>
    </form>
</x-app-shell>
