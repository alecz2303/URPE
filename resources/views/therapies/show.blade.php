<x-app-shell :title="$therapy->name" eyebrow="Terapia">
    <x-slot:actions>
        <a href="{{ route('therapies.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-slate-300">Volver</a>
        <a href="{{ route('therapies.edit', $therapy) }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Editar terapia</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4"><span class="h-12 w-12 rounded-2xl ring-1 ring-black/10" style="background-color: {{ $therapy->color }}"></span><div><p class="text-sm font-semibold text-slate-500">Configuración clínica</p><h2 class="mt-1 text-2xl font-bold">{{ $therapy->name }}</h2></div></div>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
            </div>

            <dl class="mt-8 grid gap-5 sm:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Duración</dt><dd class="mt-2 text-2xl font-bold">{{ $therapy->duration_minutes }} min</dd></div>
                <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Terapeutas requeridos</dt><dd class="mt-2 text-2xl font-bold">{{ $therapy->required_therapists }}</dd></div>
                <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Citas registradas</dt><dd class="mt-2 text-2xl font-bold">{{ $therapy->appointments_count }}</dd></div>
            </dl>

            <div class="mt-8 border-t border-slate-100 pt-6"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Color en agenda</p><div class="mt-3 flex items-center gap-3"><span class="h-7 w-7 rounded-full ring-1 ring-black/10" style="background-color: {{ $therapy->color }}"></span><span class="font-mono text-sm font-semibold text-slate-700">{{ $therapy->color }}</span></div></div>
        </section>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Uso operativo</p>
            <h3 class="mt-2 text-lg font-bold">Cómo se utiliza</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">La agenda toma la duración y el número de terapeutas directamente de esta configuración. Los cambios futuros no alteran automáticamente citas históricas ya persistidas.</p>
            @if(! $therapy->is_active)<div class="mt-5 rounded-2xl bg-amber-50 p-4 text-sm leading-6 text-amber-900">Esta terapia está inactiva y no puede seleccionarse para nuevas citas.</div>@endif
        </aside>
    </div>
</x-app-shell>
