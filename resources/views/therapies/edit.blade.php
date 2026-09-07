<x-app-shell :title="'Editar · '.$therapy->name" eyebrow="Catálogo clínico">
    <x-slot:actions>
        <a href="{{ route('therapies.show', $therapy) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-slate-300">Volver a la ficha</a>
    </x-slot:actions>

    <div class="mx-auto max-w-4xl">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
                <div><h2 class="text-xl font-bold">Editar configuración</h2><p class="mt-2 text-sm leading-6 text-slate-500">Modifica los parámetros operativos de esta terapia. La ficha de consulta permanece separada de la edición.</p></div>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
            </div>
            <form method="POST" action="{{ route('therapies.update', $therapy) }}">@csrf @method('PUT') @include('therapies._form')</form>
        </section>
    </div>
</x-app-shell>
