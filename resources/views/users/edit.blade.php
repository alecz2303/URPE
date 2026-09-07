<x-app-shell title="Editar usuario" eyebrow="Administración">
    <x-slot:actions><a href="{{ route('users.index') }}" class="rounded-xl border border-fuchsia-200 bg-white px-4 py-2.5 text-sm font-bold text-fuchsia-800 shadow-sm hover:bg-fuchsia-50">Volver a usuarios</a></x-slot:actions>

    <div class="mx-auto max-w-3xl">
        <div class="mb-6 overflow-hidden rounded-3xl border border-fuchsia-200 bg-gradient-to-r from-fuchsia-50 via-rose-50 to-amber-50 shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-fuchsia-500 via-rose-400 to-amber-400"></div>
            <div class="px-6 py-5 sm:px-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700 ring-1 ring-fuchsia-200">Cuenta interna</span>
                        <h2 class="mt-3 text-xl font-black text-slate-900">{{ $managedUser->name }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Actualiza los datos de acceso, el rol y, si hace falta, restablece la contraseña.</p>
                    </div>
                    <span class="rounded-full px-3 py-1.5 text-xs font-bold {{ $managedUser->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>
            </div>
        </div>

        <section class="overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
            <div class="border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50/80 to-white px-6 py-4 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-fuchsia-700">Datos de la cuenta</p>
            </div>
            <form method="POST" action="{{ route('users.update', $managedUser) }}" class="space-y-5 p-6 sm:p-8">
                @csrf
                @method('PUT')
                <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name', $managedUser->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
                <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email', $managedUser->email) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
                @if($canManageRoles)
                    <div class="rounded-2xl border border-violet-100 bg-violet-50/50 p-4"><label class="text-sm font-semibold text-violet-900">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-violet-200 bg-white px-4 py-3">@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', optional($managedUser->roles->first())->id) == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Rol actual: <strong>{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</strong>. No tienes permiso para modificar roles.</div>
                @endif
                <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                    <p class="mb-4 text-xs font-bold uppercase tracking-[0.14em] text-amber-700">Restablecer contraseña</p>
                    <div class="grid gap-5 sm:grid-cols-2"><div><label class="text-sm font-semibold">Nueva contraseña <span class="font-normal text-slate-400">(opcional)</span></label><input type="password" name="password" class="mt-2 w-full rounded-xl border border-amber-200 bg-white px-4 py-3"></div><div><label class="text-sm font-semibold">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="mt-2 w-full rounded-xl border border-amber-200 bg-white px-4 py-3"></div></div>
                </div>
                <div class="flex justify-end border-t border-fuchsia-100 pt-5"><button class="rounded-xl bg-gradient-to-r from-fuchsia-600 to-rose-500 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-fuchsia-700 hover:to-rose-600">Guardar cambios</button></div>
            </form>
        </section>
    </div>
</x-app-shell>
