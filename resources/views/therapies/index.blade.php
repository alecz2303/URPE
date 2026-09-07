<x-app-shell title="Terapias" eyebrow="Catálogo clínico">
    <x-slot:actions>
        <a href="{{ route('therapies.create') }}" class="rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 px-4 py-2.5 text-sm font-bold text-slate-950 shadow-sm hover:from-amber-500 hover:to-orange-500">+ Nueva terapia</a>
    </x-slot:actions>

    <div class="mb-6"><p class="max-w-3xl text-sm font-medium leading-6 text-slate-500">Consulta la configuración operativa de cada terapia. La edición y los cambios de estado permanecen como acciones explícitas.</p></div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($therapies as $therapy)
            <article class="relative overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-white to-amber-50/40 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-cyan-100/60"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3"><span class="h-11 w-11 shrink-0 rounded-2xl shadow-sm ring-4 ring-white" style="background-color: {{ $therapy->color }}"></span><div class="min-w-0"><h2 class="truncate text-lg font-extrabold">{{ $therapy->name }}</h2><p class="mt-1 font-mono text-xs font-bold text-slate-400">{{ $therapy->color }}</p></div></div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
                </div>
                <div class="relative mt-5 grid grid-cols-2 gap-3"><div class="rounded-2xl bg-pink-50 p-3"><p class="text-[11px] font-extrabold uppercase tracking-wide text-pink-500">Duración</p><p class="mt-1 text-xl font-extrabold text-slate-950">{{ $therapy->duration_minutes }}</p><p class="text-xs font-medium text-slate-500">minutos</p></div><div class="rounded-2xl bg-cyan-50 p-3"><p class="text-[11px] font-extrabold uppercase tracking-wide text-cyan-600">Terapeutas</p><p class="mt-1 text-xl font-extrabold text-slate-950">{{ $therapy->required_therapists }}</p><p class="text-xs font-medium text-slate-500">requeridos</p></div></div>
                <div class="relative mt-5 flex items-center justify-between border-t border-amber-100 pt-4"><span class="text-xs font-medium text-slate-400">Configuración de agenda</span><a href="{{ route('therapies.show', $therapy) }}" class="rounded-xl bg-amber-100 px-3 py-2 text-sm font-bold text-amber-700 hover:bg-amber-400 hover:text-slate-950">Ver terapia →</a></div>
            </article>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-3xl border border-dashed border-amber-200 bg-gradient-to-br from-white to-amber-50 px-6 py-14 text-center"><div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-amber-100 text-xl">✦</div><h2 class="mt-4 font-bold">Aún no hay terapias registradas</h2><p class="mt-1 text-sm text-slate-500">Crea la primera terapia del catálogo.</p></div>
        @endforelse
    </section>
</x-app-shell>
