<x-app-shell title="Terapeutas" eyebrow="Equipo clínico">
    <x-slot:actions><a href="{{ route('therapists.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nuevo terapeuta</a></x-slot:actions>

    <div class="mb-6"><p class="max-w-3xl text-sm leading-6 text-slate-500">Consulta perfiles clínicos, disponibilidad y próximos compromisos. La edición queda separada de la consulta.</p></div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($therapists as $therapist)
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0"><h2 class="truncate text-lg font-bold">{{ $therapist->name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $therapist->professional_title ?: 'Sin título registrado' }}</p></div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $therapist->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapist->is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Disponibilidad</p><p class="mt-1 text-lg font-bold">{{ $therapist->availabilityWindows->count() }}</p><p class="text-xs text-slate-500">ventanas</p></div>
                    <div class="rounded-2xl bg-slate-50 p-3"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Usuario</p><p class="mt-1 truncate text-sm font-bold text-slate-700">{{ $therapist->user?->name ?: 'Sin vínculo' }}</p></div>
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="truncate text-xs text-slate-400">{{ $therapist->email ?: $therapist->phone ?: 'Sin contacto' }}</span><a href="{{ route('therapists.show', $therapist) }}" class="text-sm font-bold text-cyan-700">Ver perfil →</a></div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"><h2 class="font-bold">Aún no hay terapeutas registrados</h2><p class="mt-1 text-sm text-slate-500">Crea el primer perfil clínico para comenzar.</p></div>
        @endforelse
    </section>
</x-app-shell>
