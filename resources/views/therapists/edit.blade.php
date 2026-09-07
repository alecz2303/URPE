<x-app-shell title="Editar terapeuta" eyebrow="Equipo clínico">
    <x-slot:actions><a href="{{ route('therapists.show', $therapist) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Ver perfil</a></x-slot:actions>

    <form method="POST" action="{{ route('therapists.update', $therapist) }}" data-swal-confirm data-swal-title="¿Guardar cambios?" data-swal-text="Se actualizará el perfil y la disponibilidad semanal del terapeuta." data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')
        @include('therapists._form')
        <div class="mt-8 flex justify-end"><button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-cyan-800">Guardar cambios</button></div>
    </form>

    <section class="mt-8 grid gap-6 lg:grid-cols-[1fr_1.4fr]">
        <form method="POST" action="{{ route('therapists.blocks.store', $therapist) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" data-swal-confirm data-swal-title="¿Registrar bloqueo?" data-swal-text="El terapeuta quedará no disponible durante el intervalo indicado." data-swal-confirm-text="Sí, registrar">
            @csrf
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Ausencias y bloqueos</p><h2 class="mt-1 text-xl font-bold">Nuevo bloqueo</h2><p class="mt-2 text-sm text-slate-500">Vacaciones, permisos, reuniones u otros periodos no disponibles.</p>
            <div class="mt-6 space-y-4">
                <label class="block"><span class="text-sm font-semibold text-slate-700">Inicio</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"></label>
                <label class="block"><span class="text-sm font-semibold text-slate-700">Fin</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm"></label>
                <label class="block"><span class="text-sm font-semibold text-slate-700">Motivo</span><textarea name="reason" rows="3" maxlength="500" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm">{{ old('reason') }}</textarea></label>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white hover:bg-slate-800">Registrar bloqueo</button>
            </div>
        </form>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Historial operativo</p><h2 class="mt-1 text-xl font-bold">Bloqueos registrados</h2>
            <div class="mt-6 space-y-3">@forelse($therapist->blocks as $block)<article class="rounded-xl border border-slate-200 p-4"><p class="font-semibold">{{ $block->starts_at->format('d/m/Y H:i') }} — {{ $block->ends_at->format('d/m/Y H:i') }}</p><p class="mt-1 text-sm text-slate-500">{{ $block->reason ?: 'Sin motivo registrado.' }}</p></article>@empty<p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No hay bloqueos registrados para este terapeuta.</p>@endforelse</div>
        </section>
    </section>
</x-app-shell>
