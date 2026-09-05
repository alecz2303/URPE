<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuario — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<main class="mx-auto max-w-3xl px-6 py-10">
    <a href="{{ route('users.index') }}" class="text-sm font-semibold text-cyan-700">← Volver a usuarios</a>
    <div class="mt-5 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h1 class="text-3xl font-bold">Editar usuario</h1>
        <p class="mt-2 text-sm text-slate-500">Actualiza los datos de {{ $managedUser->name }}.</p>

        <form method="POST" action="{{ route('users.update', $managedUser) }}" class="mt-8 space-y-5">
            @csrf
            @method('PUT')
            <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name', $managedUser->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email', $managedUser->email) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>

            @if($canManageRoles)
                <div><label class="text-sm font-semibold">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3">@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', optional($managedUser->roles->first())->id) == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Rol actual: <strong>{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</strong>. No tienes permiso para modificar roles.</div>
            @endif

            <div><label class="text-sm font-semibold">Nueva contraseña <span class="font-normal text-slate-400">(opcional)</span></label><input type="password" name="password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">Guardar cambios</button>
        </form>
    </div>
</main>
</body>
</html>
