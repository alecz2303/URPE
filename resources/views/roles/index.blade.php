<x-app-shell title="Roles y permisos" eyebrow="Administración">
    <p class="mb-6 text-sm text-slate-500">Consulta los roles del sistema y los permisos efectivos de cada uno.</p>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($roles as $role)
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Rol</p><h2 class="mt-2 text-xl font-bold">{{ $role->name }}</h2></div>
                    @if($role->is_system)<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Sistema</span>@endif
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-500">{{ $role->description }}</p>
                <div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Usuarios</p><p class="mt-1 text-lg font-bold">{{ $role->users_count }}</p></div><div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Permisos</p><p class="mt-1 text-lg font-bold">{{ $role->permissions_count }}</p></div></div>
                @can('roles.manage')<a href="{{ route('roles.edit', $role) }}" class="mt-5 inline-flex text-sm font-bold text-cyan-700">Administrar permisos →</a>@endcan
            </article>
        @endforeach
    </div>
</x-app-shell>
