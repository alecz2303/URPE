<x-app-shell :title="'Ausencias · '.$therapist->name" eyebrow="Equipo clínico">
    <x-slot:actions>
        <a href="{{ route('therapists.show', $therapist) }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-700 shadow-sm hover:bg-violet-50">Ver perfil</a>
        <a href="{{ route('therapists.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50">Terapeutas</a>
    </x-slot:actions>

    <div class="mb-6 overflow-hidden rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-rose-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-rose-400"></div>
        <div class="flex flex-wrap items-start justify-between gap-4 px-6 py-5 sm:px-7">
            <div>
                <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-amber-700 ring-1 ring-amber-200">Disponibilidad operativa</span>
                <h2 class="mt-3 text-xl font-black text-slate-900">{{ $therapist->name }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Registra vacaciones, permisos, reuniones u otros periodos en los que el terapeuta no podrá recibir citas. No necesitas entrar a editar su perfil.</p>
            </div>
            <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $therapist->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapist->is_active ? 'Activo' : 'Inactivo' }}</span>
        </div>
    </div>

    <section class="grid gap-6 lg:grid-cols-[minmax(320px,.8fr)_minmax(0,1.2fr)]">
        <form method="POST" action="{{ route('therapists.blocks.store', $therapist) }}" class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-sm" data-swal-confirm data-swal-title="¿Registrar bloqueo?" data-swal-text="El terapeuta quedará no disponible durante el intervalo indicado." data-swal-confirm-text="Sí, registrar">
            @csrf
            <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50 via-orange-50 to-white px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700">Ausencias y bloqueos</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Nuevo bloqueo</h2>
                <p class="mt-2 text-sm text-slate-600">Define el intervalo completo de indisponibilidad.</p>
            </div>
            <div class="space-y-4 p-6">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Inicio</span>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="mt-2 w-full rounded-xl border border-amber-200 bg-amber-50/30 px-4 py-3 text-sm focus:border-amber-400 focus:ring-amber-300">
                    @error('starts_at')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Fin</span>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" required class="mt-2 w-full rounded-xl border border-amber-200 bg-amber-50/30 px-4 py-3 text-sm focus:border-amber-400 focus:ring-amber-300">
                    @error('ends_at')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Motivo</span>
                    <textarea name="reason" rows="4" maxlength="500" class="mt-2 w-full rounded-xl border border-amber-200 bg-amber-50/30 px-4 py-3 text-sm focus:border-amber-400 focus:ring-amber-300">{{ old('reason') }}</textarea>
                    @error('reason')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                </label>
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3 text-sm font-bold text-white shadow-sm hover:from-amber-600 hover:to-orange-600">Registrar bloqueo</button>
            </div>
        </form>

        <section class="overflow-hidden rounded-3xl border border-rose-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-rose-100 bg-gradient-to-r from-rose-50 via-fuchsia-50 to-white px-6 py-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-rose-700">Historial operativo</p>
                    <h2 class="mt-1 text-xl font-black text-slate-900">Bloqueos registrados</h2>
                </div>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-100">{{ $therapist->blocks->count() }} registrados</span>
            </div>
            <div class="space-y-3 p-6">
                @forelse($therapist->blocks as $block)
                    <article class="rounded-2xl border border-rose-100 bg-gradient-to-r from-rose-50/60 to-white p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-800">{{ $block->starts_at->format('d/m/Y H:i') }}</p>
                                <p class="mt-1 text-sm font-semibold text-rose-600">hasta {{ $block->ends_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-bold text-rose-700">No disponible</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $block->reason ?: 'Sin motivo registrado.' }}</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-rose-200 bg-rose-50/50 px-5 py-10 text-center">
                        <p class="font-bold text-slate-700">Sin bloqueos registrados</p>
                        <p class="mt-1 text-sm text-slate-500">La disponibilidad semanal del terapeuta está libre de ausencias adicionales.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </section>
</x-app-shell>
