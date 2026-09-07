<x-app-shell title="Terapeutas" eyebrow="Equipo clínico">
    <x-slot:actions>
        <a href="{{ route('therapists.create') }}" class="rounded-xl bg-gradient-to-r from-violet-500 to-purple-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-violet-600 hover:to-purple-600">+ Nuevo terapeuta</a>
    </x-slot:actions>

    <div class="mb-6"><p class="max-w-3xl text-sm font-medium leading-6 text-slate-500">Consulta perfiles clínicos, disponibilidad y próximos compromisos. Las ausencias y bloqueos tienen acceso directo desde cada terapeuta.</p></div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($therapists as $therapist)
            <article class="relative overflow-hidden rounded-3xl border border-violet-100 bg-gradient-to-br from-white to-violet-50/50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-pink-100/70"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-violet-100 text-lg text-violet-600">✦</div>
                        <div class="min-w-0"><h2 class="truncate text-lg font-extrabold">{{ $therapist->name }}</h2><p class="mt-1 text-sm font-medium text-slate-500">{{ $therapist->professional_title ?: 'Sin título registrado' }}</p></div>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $therapist->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapist->is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <div class="relative mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-cyan-50 p-3"><p class="text-[11px] font-extrabold uppercase tracking-wide text-cyan-600">Disponibilidad</p><p class="mt-1 text-lg font-extrabold text-slate-950">{{ $therapist->availabilityWindows->count() }}</p><p class="text-xs font-medium text-slate-500">ventanas</p></div>
                    <div class="rounded-2xl bg-pink-50 p-3"><p class="text-[11px] font-extrabold uppercase tracking-wide text-pink-500">Usuario</p><p class="mt-1 truncate text-sm font-bold text-slate-700">{{ $therapist->user?->name ?: 'Sin vínculo' }}</p></div>
                </div>
                <div class="relative mt-5 border-t border-violet-100 pt-4">
                    <p class="mb-3 truncate text-xs font-medium text-slate-400">{{ $therapist->email ?: $therapist->phone ?: 'Sin contacto' }}</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('therapists.blocks.index', $therapist) }}" class="flex-1 rounded-xl bg-amber-50 px-3 py-2 text-center text-sm font-bold text-amber-700 ring-1 ring-amber-200 hover:bg-amber-500 hover:text-white">Ausencias</a>
                        <a href="{{ route('therapists.show', $therapist) }}" class="flex-1 rounded-xl bg-violet-100 px-3 py-2 text-center text-sm font-bold text-violet-700 hover:bg-violet-600 hover:text-white">Ver perfil →</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-violet-200 bg-gradient-to-br from-white to-violet-50 px-6 py-14 text-center"><div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-violet-100 text-xl">✦</div><h2 class="mt-4 font-bold">Aún no hay terapeutas registrados</h2><p class="mt-1 text-sm text-slate-500">Crea el primer perfil clínico para comenzar.</p></div>
        @endforelse
    </section>
</x-app-shell>
