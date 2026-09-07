<x-app-shell title="Editar permisos" eyebrow="Administración">
    <x-slot:actions>
        <a href="{{ route('roles.index') }}" class="rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-50">Volver a roles</a>
    </x-slot:actions>

    <div class="mb-6 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
        <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-start sm:justify-between sm:px-7">
            <div>
                <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-violet-700 ring-1 ring-violet-200">Rol y permisos</span>
                <h2 class="mt-3 text-2xl font-black text-slate-900">{{ $managedRole->name }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Define qué áreas y acciones puede utilizar este rol. Los cambios afectan los permisos efectivos de las cuentas que lo tengan asignado.</p>
            </div>
            @if($managedRole->is_system)
                <span class="w-fit rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-800 ring-1 ring-amber-200">Rol de sistema</span>
            @endif
        </div>
    </div>

    <section class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
        <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50/80 via-white to-fuchsia-50/50 px-6 py-4 sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-violet-700">Matriz de acceso</p>
            <p class="mt-1 text-sm text-slate-500">Marca únicamente las capacidades que correspondan a las responsabilidades del rol.</p>
        </div>

        <form method="POST" action="{{ route('roles.update', $managedRole) }}" class="p-6 sm:p-8">
            @csrf
            @method('PUT')
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($permissions as $permission)
                    <label class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-violet-100 bg-gradient-to-br from-white to-violet-50/30 p-4 transition hover:-translate-y-0.5 hover:border-fuchsia-200 hover:shadow-sm">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', $managedRole->permissions->pluck('id')->all()))) class="mt-1 h-4 w-4 rounded border-violet-300 text-violet-600 focus:ring-violet-500">
                        <span>
                            <span class="block text-sm font-bold text-slate-800 group-hover:text-violet-800">{{ $permission->name }}</span>
                            <span class="mt-1 inline-flex rounded-full bg-cyan-50 px-2 py-0.5 font-mono text-[11px] font-semibold text-cyan-700">{{ $permission->slug }}</span>
                            @if($permission->description)
                                <span class="mt-2 block text-sm leading-5 text-slate-500">{{ $permission->description }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>

            @if($errors->any())
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-violet-100 pt-6">
                <a href="{{ route('roles.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 hover:border-violet-200 hover:bg-violet-50">Cancelar</a>
                <button class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Guardar permisos</button>
            </div>
        </form>
    </section>
</x-app-shell>
