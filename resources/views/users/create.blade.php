<x-app-shell title="Nuevo usuario" eyebrow="Administración">
    <x-slot:actions><a href="{{ route('users.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-cyan-300 hover:text-cyan-800">Volver a usuarios</a></x-slot:actions>

    <section class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm text-slate-500">Crea una cuenta interna de URPE Gestión Clínica.</p>
        <form method="POST" action="{{ route('users.store') }}" class="mt-7 space-y-5">
            @csrf
            <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            @if($canManageRoles)
                <div><label class="text-sm font-semibold">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Selecciona un rol</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Tu cuenta puede crear usuarios, pero no asignar roles. El usuario se creará sin rol hasta que alguien con permiso para administrar roles lo asigne.</div>
            @endif
            <div><label class="text-sm font-semibold">Contraseña</label><input type="password" name="password" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Confirmar contraseña</label><input type="password" name="password_confirmation" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div class="flex justify-end border-t border-slate-100 pt-5"><button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-bold text-white hover:bg-cyan-800">Crear usuario</button></div>
        </form>
    </section>
</x-app-shell>
