<x-app-shell :title="'Editar · '.$therapy->name" eyebrow="Catálogo clínico">
    <x-slot:actions>
        <a href="{{ route('therapies.show', $therapy) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-50">Volver a la ficha</a>
    </x-slot:actions>

    <div class="mx-auto max-w-4xl">
        <section class="overflow-hidden rounded-3xl border border-fuchsia-200 bg-white shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-fuchsia-500 via-violet-500 to-cyan-400"></div>
            <div class="border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50 via-violet-50/80 to-cyan-50 px-6 py-6 sm:px-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700 ring-1 ring-fuchsia-200">Configuración operativa</span>
                        <h2 class="mt-3 text-xl font-black text-slate-900">{{ $therapy->name }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Actualiza los parámetros que usa la agenda sin mezclar la ficha de consulta con la edición.</p>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('therapies.update', $therapy) }}">
                    @csrf
                    @method('PUT')
                    @include('therapies._form')
                </form>
            </div>
        </section>
    </div>
</x-app-shell>
