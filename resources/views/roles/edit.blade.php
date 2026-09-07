<x-app-shell title="Editar permisos" eyebrow="Administración">
    <x-slot:actions><a href="{{ route('roles.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a roles</a></x-slot:actions>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">Rol</p><h2 class="mt-2 text-2xl font-bold">{{ $managedRole->name }}</h2><p class="mt-2 text-sm text-slate-500">Selecciona los permisos efectivos para este rol. El nombre y slug de los roles base permanecen estables.</p></div>
            @if($managedRole->is_system)<span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Rol de sistema</span>@endif
        </div>

        <form method="POST" action="{{ route('roles.update', $managedRole) }}" class="mt-8">
            @csrf
            @method('PUT')
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($permissions as $permission)
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', $managedRole->permissions->pluck('id')->all()))) class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
                        <span><span class="block text-sm font-semibold text-slate-800">{{ $permission->name }}</span><span class="mt-1 block text-xs font-medium text-slate-400">{{ $permission->slug }}</span>@if($permission->description)<span class="mt-2 block text-sm leading-5 text-slate-500">{{ $permission->description }}</span>@endif</span>
                    </label>
                @endforeach
            </div>
            @if($errors->any())<div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5"><a href="{{ route('roles.index') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancelar</a><button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white hover:bg-cyan-800">Guardar permisos</button></div>
        </form>
    </section>
</x-app-shell>
