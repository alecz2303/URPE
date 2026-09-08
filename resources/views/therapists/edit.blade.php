<x-app-shell title="Editar terapeuta" eyebrow="Equipo clínico">
    <x-slot:actions>
        <a href="{{ route('therapists.blocks.index', $therapist) }}" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-800 shadow-sm hover:bg-amber-100">Ausencias y bloqueos</a>
        <a href="{{ route('therapists.show', $therapist) }}" class="rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-emerald-800 shadow-sm hover:bg-emerald-50">Ver perfil</a>
    </x-slot:actions>

    @if(session('therapist_credentials'))
        <div class="mb-6 overflow-hidden rounded-3xl border border-amber-300 bg-gradient-to-r from-amber-50 via-yellow-50 to-white shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-amber-400 to-yellow-400"></div>
            <div class="px-6 py-5 sm:px-7">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-amber-800">Credenciales temporales · mostrar una sola vez</p>
                <h2 class="mt-2 text-lg font-black text-slate-900">Entrega estos datos directamente al terapeuta</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-amber-200 bg-white px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Usuario</p>
                        <p class="mt-1 break-all font-mono text-sm font-bold text-slate-900">{{ session('therapist_credentials.email') }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-white px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contraseña temporal</p>
                        <p class="mt-1 break-all font-mono text-sm font-bold text-slate-900">{{ session('therapist_credentials.password') }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs leading-5 text-amber-900">La contraseña no se volverá a mostrar desde esta pantalla. No la copies en notas clínicas ni en campos de auditoría.</p>
            </div>
        </div>
    @endif

    <div class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="px-6 py-5 sm:px-7">
            <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-violet-700 ring-1 ring-violet-200">Perfil clínico</span>
            <h2 class="mt-3 text-xl font-black text-slate-900">{{ $therapist->name }}</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Actualiza sus datos profesionales, acceso y disponibilidad semanal. Las ausencias y bloqueos se administran desde su acceso independiente.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('therapists.update', $therapist) }}" class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm" data-swal-confirm data-swal-title="¿Guardar cambios?" data-swal-text="Se actualizarán el perfil, el acceso y la disponibilidad semanal del terapeuta." data-swal-confirm-text="Sí, guardar">
        @csrf
        @method('PUT')
        <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50/80 to-white px-6 py-4 sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-violet-700">Perfil, acceso y disponibilidad</p>
        </div>
        <div class="p-6 sm:p-8">
            @include('therapists._form')
            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-violet-100 pt-6">
                <a href="{{ route('therapists.blocks.index', $therapist) }}" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800 hover:bg-amber-100">Gestionar ausencias</a>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Guardar cambios</button>
            </div>
        </div>
    </form>
</x-app-shell>
