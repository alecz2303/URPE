<x-app-shell title="Usuarios" eyebrow="Administración">
    <x-slot:actions>
        @can('users.create')
            <a href="{{ route('users.create') }}" class="rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-sky-600 hover:to-cyan-600">+ Nuevo usuario</a>
        @endcan
    </x-slot:actions>

    <p class="mb-6 text-sm font-medium text-slate-500">Cuentas internas, roles y acceso a URPE Gestión Clínica.</p>

    <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm">
        <div class="divide-y divide-sky-50">
            @forelse($users as $managedUser)
                <article class="grid gap-4 px-5 py-5 transition hover:bg-gradient-to-r hover:from-sky-50/60 hover:to-violet-50/30 md:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_auto] md:items-center lg:px-6">
                    <div class="flex items-center gap-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-sky-100 text-sm font-extrabold text-sky-700">{{ strtoupper(substr($managedUser->name, 0, 1)) }}</div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-extrabold">{{ $managedUser->name }}</h2>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $managedUser->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                            <p class="mt-1 text-sm font-medium text-slate-500">{{ $managedUser->email }}</p>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-violet-50 px-4 py-3">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-violet-500">Rol</p>
                        <p class="mt-1 text-sm font-bold text-slate-700">{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</p>
                    </div>
                    <div class="flex gap-3 md:justify-end">
                        @can('users.update')
                            <a href="{{ route('users.edit', $managedUser) }}" class="rounded-xl bg-sky-100 px-3 py-2 text-sm font-bold text-sky-700 hover:bg-sky-600 hover:text-white">Editar</a>
                        @endcan

                        @can('users.deactivate')
                            @if(! auth()->user()->is($managedUser))
                                <form method="POST" action="{{ route('users.toggle-active', $managedUser) }}" data-swal-confirm data-swal-title="{{ $managedUser->is_active ? '¿Desactivar usuario?' : '¿Activar usuario?' }}" data-swal-text="{{ $managedUser->is_active ? 'El usuario perderá acceso al sistema.' : 'El usuario recuperará el acceso al sistema.' }}" data-swal-confirm-text="{{ $managedUser->is_active ? 'Sí, desactivar' : 'Sí, activar' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-xl px-3 py-2 text-sm font-bold {{ $managedUser->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $managedUser->is_active ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </article>
            @empty
                <div class="bg-gradient-to-br from-white to-sky-50 px-6 py-14 text-center text-sm text-slate-500">No hay usuarios registrados.</div>
            @endforelse
        </div>
    </section>
</x-app-shell>
