<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">URPE</p>
                <h1 class="text-xl font-bold">Administración de usuarios</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Volver al dashboard</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Usuarios</h2>
                <p class="mt-2 text-sm text-slate-500">Cuentas internas, roles y acceso a URPE Gestión Clínica.</p>
            </div>
            @can('users.create')
                <a href="{{ route('users.create') }}" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-800">Nuevo usuario</a>
            @endcan
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $managedUser)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800">{{ $managedUser->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $managedUser->email }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $managedUser->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($managedUser->is_active)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Activo</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @can('users.update')
                                            <a href="{{ route('users.edit', $managedUser) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Editar</a>
                                        @endcan

                                        @can('users.deactivate')
                                            @if (! auth()->user()->is($managedUser))
                                                <form
                                                    method="POST"
                                                    action="{{ route('users.toggle-active', $managedUser) }}"
                                                    data-swal-confirm
                                                    data-swal-title="{{ $managedUser->is_active ? '¿Desactivar usuario?' : '¿Activar usuario?' }}"
                                                    data-swal-text="{{ $managedUser->is_active ? 'El usuario perderá acceso al sistema.' : 'El usuario recuperará el acceso al sistema.' }}"
                                                    data-swal-confirm-text="{{ $managedUser->is_active ? 'Sí, desactivar' : 'Sí, activar' }}"
                                                    data-swal-icon="{{ $managedUser->is_active ? 'warning' : 'question' }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-sm font-semibold {{ $managedUser->is_active ? 'text-rose-700 hover:text-rose-900' : 'text-emerald-700 hover:text-emerald-900' }}">
                                                        {{ $managedUser->is_active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">No hay usuarios registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <x-sweet-alerts />
</body>
</html>
