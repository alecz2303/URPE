<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo usuario — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<main class="mx-auto max-w-3xl px-6 py-10">
    <a href="{{ route('users.index') }}" class="text-sm font-semibold text-cyan-700">← Volver a usuarios</a>
    <div class="mt-5 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h1 class="text-3xl font-bold">Nuevo usuario</h1>
        <p class="mt-2 text-sm text-slate-500">Crea una cuenta interna de URPE Gestión Clínica.</p>

        <form method="POST" action="{{ route('users.store') }}" class="mt-8 space-y-5">
            @csrf
            <div><label class="text-sm font-semibold">Nombre</label><input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Correo electrónico</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>

            @if($canManageRoles)
                <div><label class="text-sm font-semibold">Rol</label><select name="role_id" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">Selecciona un rol</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>@endforeach</select></div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Tu cuenta puede crear usuarios, pero no asignar roles. El usuario se creará sin rol hasta que alguien con permiso <strong>Administrar roles y permisos</strong> lo asigne.</div>
            @endif

            <div><label class="text-sm font-semibold">Contraseña</label><input type="password" name="password" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <div><label class="text-sm font-semibold">Confirmar contraseña</label><input type="password" name="password_confirmation" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></div>
            <button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">Crear usuario</button>
        </form>
    </div>
</main>

<x-sweet-alerts />
</body>
</html>
