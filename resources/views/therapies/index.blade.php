<x-app-shell title="Terapias" eyebrow="Catálogo clínico">
    <x-slot:actions><a href="{{ route('therapies.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nueva terapia</a></x-slot:actions>

    <div class="mb-6"><p class="max-w-3xl text-sm leading-6 text-slate-500">Consulta la configuración operativa de cada terapia. La edición y los cambios de estado permanecen como acciones explícitas.</p></div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($therapies as $therapy)
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3"><span class="h-10 w-10 shrink-0 rounded-2xl ring-1 ring-black/10" style="background-color: {{ $therapy->color }}"></span><div class="min-w-0"><h2 class="truncate text-lg font-bold">{{ $therapy->name }}</h2><p class="mt-1 font-mono text-xs text-slate-400">{{ $therapy->color }}</p></div></div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-2xl bg-slate-50 p-3"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Duración</p><p class="mt-1 text-xl font-bold">{{ $therapy->duration_minutes }}</p><p class="text-xs text-slate-500">minutos</p></div><div class="rounded-2xl bg-slate-50 p-3"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Terapeutas</p><p class="mt-1 text-xl font-bold">{{ $therapy->required_therapists }}</p><p class="text-xs text-slate-500">requeridos</p></div></div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="text-xs text-slate-400">Configuración de agenda</span><a href="{{ route('therapies.show', $therapy) }}" class="text-sm font-bold text-cyan-700">Ver terapia →</a></div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center"><h2 class="font-bold">Aún no hay terapias registradas</h2><p class="mt-1 text-sm text-slate-500">Crea la primera terapia del catálogo.</p></div>
        @endforelse
    </section>
</x-app-shell>
