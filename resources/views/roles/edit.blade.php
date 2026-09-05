<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar permisos — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<main class="mx-auto max-w-4xl px-6 py-10">
    <a href="{{ route('roles.index') }}" class="text-sm font-semibold text-cyan-700">← Volver a roles</a>

    <div class="mt-5 rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Rol</p>
                <h1 class="mt-2 text-3xl font-bold">{{ $managedRole->name }}</h1>
                <p class="mt-2 text-sm text-slate-500">Selecciona los permisos efectivos para este rol. El nombre y slug de los roles base permanecen estables.</p>
            </div>
            @if($managedRole->is_system)
                <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Rol de sistema</span>
            @endif
        </div>

        <form method="POST" action="{{ route('roles.update', $managedRole) }}" class="mt-8">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                @foreach($permissions as $permission)
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            @checked(in_array($permission->id, old('permissions', $managedRole->permissions->pluck('id')->all())))
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600"
                        >
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">{{ $permission->name }}</span>
                            <span class="mt-1 block text-xs font-medium text-slate-400">{{ $permission->slug }}</span>
                            @if($permission->description)
                                <span class="mt-2 block text-sm leading-5 text-slate-500">{{ $permission->description }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>

            @if($errors->any())
                <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-8 flex items-center gap-3">
                <button class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">Guardar permisos</button>
                <a href="{{ route('roles.index') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancelar</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>
