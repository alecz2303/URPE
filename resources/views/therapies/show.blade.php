<x-app-shell :title="$therapy->name" eyebrow="Terapia">
    <x-slot:actions>
        <a href="{{ route('therapies.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-violet-200 hover:bg-violet-50">Volver</a>
        <a href="{{ route('therapies.edit', $therapy) }}" class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-600">Editar terapia</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <section class="overflow-hidden rounded-3xl border border-violet-200 bg-white shadow-sm">
            <div class="h-1.5" style="background: linear-gradient(90deg, {{ $therapy->color }}, #8b5cf6, #ec4899);"></div>
            <div class="bg-gradient-to-br from-violet-50 via-white to-fuchsia-50 p-6 sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="h-14 w-14 rounded-2xl shadow-sm ring-4 ring-white" style="background-color: {{ $therapy->color }}"></span>
                        <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Configuración clínica</p><h2 class="mt-1 text-2xl font-black text-slate-900">{{ $therapy->name }}</h2></div>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $therapy->is_active ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">{{ $therapy->is_active ? 'Activa' : 'Inactiva' }}</span>
                </div>
            </div>

            <div class="p-6 sm:p-7">
                <dl class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-cyan-700">Duración</dt><dd class="mt-2 text-2xl font-black text-cyan-950">{{ $therapy->duration_minutes }} min</dd></div>
                    <div class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-fuchsia-700">Terapeutas requeridos</dt><dd class="mt-2 text-2xl font-black text-fuchsia-950">{{ $therapy->required_therapists }}</dd></div>
                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-amber-700">Citas registradas</dt><dd class="mt-2 text-2xl font-black text-amber-950">{{ $therapy->appointments_count }}</dd></div>
                </dl>

                <div class="mt-7 rounded-2xl border border-violet-100 bg-violet-50/70 p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-violet-700">Color en agenda</p>
                    <div class="mt-3 flex items-center gap-3"><span class="h-8 w-8 rounded-full shadow-sm ring-2 ring-white" style="background-color: {{ $therapy->color }}"></span><span class="font-mono text-sm font-bold text-slate-700">{{ $therapy->color }}</span></div>
                </div>
            </div>
        </section>

        <aside class="overflow-hidden rounded-3xl border border-cyan-200 bg-white shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-cyan-500 to-emerald-400"></div>
            <div class="bg-gradient-to-br from-cyan-50 to-emerald-50 p-6">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Uso operativo</p>
                <h3 class="mt-2 text-lg font-black text-slate-900">Cómo se utiliza</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">La agenda toma la duración y el número de terapeutas directamente de esta configuración. Los cambios futuros no alteran automáticamente citas históricas ya persistidas.</p>
                @if(! $therapy->is_active)<div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">Esta terapia está inactiva y no puede seleccionarse para nuevas citas.</div>@endif
            </div>
        </aside>
    </div>
</x-app-shell>
