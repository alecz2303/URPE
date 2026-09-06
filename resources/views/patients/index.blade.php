<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pacientes — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Pacientes</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Dashboard</a>
            @can('patients.manage')
                <a href="{{ route('patients.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Nuevo paciente</a>
            @endcan
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Estructura clínica</p>
        <h2 class="mt-1 text-3xl font-bold">Pacientes y responsables</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Consulta los datos administrativos del paciente y sus responsables vinculados. El expediente clínico se incorporará en una etapa posterior.</p>
    </div>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Paciente</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Folio</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nacimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Responsable principal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($patients as $patient)
                    @php($primaryGuardian = $patient->guardians->first(fn ($guardian) => (bool) $guardian->pivot->is_primary))
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $patient->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $patient->phone ?: $patient->email ?: 'Sin contacto directo' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-slate-600">{{ $patient->folio }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $patient->date_of_birth?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if($primaryGuardian)
                                <p class="font-semibold text-slate-700">{{ $primaryGuardian->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $primaryGuardian->pivot->relationship ?: 'Relación no especificada' }}</p>
                            @else
                                <span class="text-slate-400">Sin responsable principal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $patient->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $patient->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('patients.show', $patient) }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">Aún no hay pacientes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<x-sweet-alerts />
</body>
</html>
