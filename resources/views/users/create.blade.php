<x-app-shell title="Nuevo usuario" eyebrow="Administración">
    <x-slot:actions><a href="{{ route('users.index') }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Volver a usuarios</a></x-slot:actions>

    <div class="mx-auto max-w-3xl">
        <div class="mb-6 overflow-hidden rounded-3xl border border-sky-200 bg-gradient-to-r from-sky-50 via-cyan-50 to-violet-50 shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-sky-500 via-cyan-400 to-violet-400"></div>
            <div class="px-6 py-5 sm:px-7">
                <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">Acceso interno</span>
                <h2 class="mt-3 text-xl font-black text-slate-900">Crear cuenta de acceso</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Registra una nueva cuenta interna para URPE Gestión Clínica y asigna su rol cuando tu permiso lo permita.</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm">
            <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50/80 to-white px-6 py-4 sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-sky-700">Datos de la cuenta</p>
            </div>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5 p-6 sm:p-8">
                @csrf
                <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
                <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
                @if($canManageRoles)
                    <div class="rounded-2xl border border-violet-100 bg-violet-50/50 p-4"><label class="text-sm font-semibold text-violet-900">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-violet-200 bg-white px-4 py-3"><option value="">Selecciona un rol</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800">Tu cuenta puede crear usuarios, pero no asignar roles. El usuario se creará sin rol hasta que alguien con permiso para administrar roles lo asigne.</div>
                @endif
                <div class="grid gap-5 sm:grid-cols-2"><div><label class="text-sm font-semibold">Contraseña</label><input type="password" name="password" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div><div><label class="text-sm font-semibold">Confirmar contraseña</label><input type="password" name="password_confirmation" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div></div>
                <div class="flex justify-end border-t border-sky-100 pt-5"><button class="rounded-xl bg-gradient-to-r from-sky-600 to-cyan-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:from-sky-700 hover:to-cyan-700">Crear usuario</button></div>
            </form>
        </section>
    </div>
</x-app-shell>
