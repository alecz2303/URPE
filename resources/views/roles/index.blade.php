<x-app-shell title="Roles y permisos" eyebrow="Administración">
    <p class="mb-6 text-sm font-medium text-slate-500">Consulta los roles del sistema y los permisos efectivos de cada uno.</p>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($roles as $role)
            <article class="relative overflow-hidden rounded-3xl border border-fuchsia-100 bg-gradient-to-br from-white to-fuchsia-50/40 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-20 w-20 rounded-full bg-cyan-100/60"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <div><p class="text-xs font-extrabold uppercase tracking-[0.18em] text-fuchsia-600">Rol</p><h2 class="mt-2 text-xl font-extrabold">{{ $role->name }}</h2></div>
                    @if($role->is_system)
                        <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700">Sistema</span>
                    @endif
                </div>
                <p class="relative mt-3 text-sm font-medium leading-6 text-slate-500">{{ $role->description }}</p>
                <div class="relative mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-cyan-50 p-3"><p class="text-xs font-bold text-cyan-600">Usuarios</p><p class="mt-1 text-lg font-extrabold text-slate-950">{{ $role->users_count }}</p></div>
                    <div class="rounded-xl bg-pink-50 p-3"><p class="text-xs font-bold text-pink-500">Permisos</p><p class="mt-1 text-lg font-extrabold text-slate-950">{{ $role->permissions_count }}</p></div>
                </div>
                @can('roles.manage')
                    <a href="{{ route('roles.edit', $role) }}" class="relative mt-5 inline-flex rounded-xl bg-fuchsia-100 px-3 py-2 text-sm font-bold text-fuchsia-700 hover:bg-fuchsia-600 hover:text-white">Administrar permisos →</a>
                @endcan
            </article>
        @endforeach
    </div>
</x-app-shell>
