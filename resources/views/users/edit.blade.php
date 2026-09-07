<x-app-shell title="Editar usuario" eyebrow="Administración">
    <x-slot:actions><a href="{{ route('users.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a usuarios</a></x-slot:actions>

    <section class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-xl font-bold">{{ $managedUser->name }}</h2><p class="mt-1 text-sm text-slate-500">Actualiza datos de acceso y rol.</p></div><span class="rounded-full px-3 py-1 text-xs font-bold {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Activo' : 'Inactivo' }}</span></div>
        <form method="POST" action="{{ route('users.update', $managedUser) }}" class="mt-7 space-y-5">
            @csrf
            @method('PUT')
            <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name', $managedUser->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email', $managedUser->email) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            @if($canManageRoles)
                <div><label class="text-sm font-semibold">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3">@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', optional($managedUser->roles->first())->id) == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Rol actual: <strong>{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</strong>. No tienes permiso para modificar roles.</div>
            @endif
            <div class="grid gap-5 sm:grid-cols-2"><div><label class="text-sm font-semibold">Nueva contraseña <span class="font-normal text-slate-400">(opcional)</span></label><input type="password" name="password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div><div><label class="text-sm font-semibold">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div></div>
            <div class="flex justify-end border-t border-slate-100 pt-5"><button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white hover:bg-cyan-800">Guardar cambios</button></div>
        </form>
    </section>
</x-app-shell>
