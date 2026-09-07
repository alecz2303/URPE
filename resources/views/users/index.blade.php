<x-app-shell title="Usuarios" eyebrow="Administración">
    <x-slot:actions>
        @can('users.create')
            <a href="{{ route('users.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nuevo usuario</a>
        @endcan
    </x-slot:actions>

    <p class="mb-6 text-sm text-slate-500">Cuentas internas, roles y acceso a URPE Gestión Clínica.</p>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="divide-y divide-slate-100">
            @forelse($users as $managedUser)
                <article class="grid gap-4 px-5 py-5 md:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_auto] md:items-center lg:px-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-bold">{{ $managedUser->name }}</h2>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $managedUser->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Rol</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</p>
                    </div>
                    <div class="flex gap-3 md:justify-end">
                        @can('users.update')
                            <a href="{{ route('users.edit', $managedUser) }}" class="text-sm font-bold text-cyan-700">Editar</a>
                        @endcan

                        @can('users.deactivate')
                            @if(! auth()->user()->is($managedUser))
                                <form method="POST" action="{{ route('users.toggle-active', $managedUser) }}" data-swal-confirm data-swal-title="{{ $managedUser->is_active ? '¿Desactivar usuario?' : '¿Activar usuario?' }}" data-swal-text="{{ $managedUser->is_active ? 'El usuario perderá acceso al sistema.' : 'El usuario recuperará el acceso al sistema.' }}" data-swal-confirm-text="{{ $managedUser->is_active ? 'Sí, desactivar' : 'Sí, activar' }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-sm font-bold {{ $managedUser->is_active ? 'text-rose-600' : 'text-emerald-700' }}">{{ $managedUser->is_active ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </article>
            @empty
                <div class="px-6 py-14 text-center text-sm text-slate-500">No hay usuarios registrados.</div>
            @endforelse
        </div>
    </section>
</x-app-shell>
