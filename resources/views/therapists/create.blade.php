<x-app-shell title="Nuevo terapeuta" eyebrow="Equipo clínico">
    <x-slot:actions><a href="{{ route('therapists.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a terapeutas</a></x-slot:actions>

    <form method="POST" action="{{ route('therapists.store') }}" data-swal-confirm data-swal-title="¿Crear terapeuta?" data-swal-text="Se guardará el perfil y su disponibilidad semanal." data-swal-confirm-text="Sí, crear">
        @csrf
        @include('therapists._form')
        <div class="mt-8 flex justify-end"><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-cyan-800">Crear terapeuta</button></div>
    </form>
</x-app-shell>
