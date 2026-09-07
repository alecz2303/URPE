<x-app-shell title="Nueva terapia" eyebrow="Catálogo clínico">
    <x-slot:actions><a href="{{ route('therapies.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-slate-300">Cancelar</a></x-slot:actions>

    <div class="mx-auto max-w-4xl">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-7"><h2 class="text-xl font-bold">Configuración de la terapia</h2><p class="mt-2 text-sm leading-6 text-slate-500">Estos valores alimentan directamente la agenda: duración, recursos humanos requeridos, color y disponibilidad.</p></div>
            <form method="POST" action="{{ route('therapies.store') }}">@csrf @include('therapies._form')</form>
        </section>
    </div>
</x-app-shell>
