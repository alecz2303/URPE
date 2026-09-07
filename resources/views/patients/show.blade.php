<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $patient->full_name }} — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Ficha del paciente</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('patients.index') }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Pacientes</a>
            @can('clinical_records.view')
                <a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2 text-sm font-semibold text-cyan-800 hover:bg-cyan-100">Expediente clínico</a>
            @endcan
            @can('patients.manage')
                <a href="{{ route('patients.edit', $patient) }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Editar paciente</a>
            @endcan
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-[1fr_1.25fr]">
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">{{ $patient->folio }}</p>
                    <h2 class="mt-2 text-3xl font-bold">{{ $patient->full_name }}</h2>
                </div>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $patient->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $patient->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <dl class="mt-7 grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nacimiento</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->date_of_birth?->format('d/m/Y') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sexo</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ ['female' => 'Femenino', 'male' => 'Masculino', 'other' => 'Otro', 'unspecified' => 'No especificado'][$patient->sex] ?? 'No especificado' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Teléfono</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->phone ?: '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Correo</dt><dd class="mt-1 break-all text-sm font-semibold text-slate-700">{{ $patient->email ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dirección</dt><dd class="mt-1 text-sm leading-6 text-slate-700">{{ $patient->address_line ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Notas administrativas</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $patient->administrative_notes ?: '—' }}</dd></div>
            </dl>

            @can('patients.manage')
                <form method="POST" action="{{ route('patients.toggle-active', $patient) }}" class="js-confirm-patient-status mt-7" data-name="{{ $patient->full_name }}" data-active="{{ $patient->is_active ? '1' : '0' }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-sm font-semibold {{ $patient->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-700 hover:text-emerald-900' }}">
                        {{ $patient->is_active ? 'Desactivar paciente' : 'Activar paciente' }}
                    </button>
                </form>
            @endcan
        </section>

        <section class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Contactos</p>
                        <h3 class="mt-1 text-xl font-bold">Responsables vinculados</h3>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $patient->guardians->count() }}</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse($patient->guardians as $guardian)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-bold text-slate-800">{{ $guardian->full_name }}</h4>
                                        @if($guardian->pivot->is_primary)
                                            <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-xs font-semibold text-cyan-700">Principal</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $guardian->pivot->relationship ?: 'Relación no especificada' }}</p>
                                    <p class="mt-2 text-sm text-slate-700">{{ $guardian->phone }}</p>
                                    @if($guardian->email)<p class="text-sm text-slate-600">{{ $guardian->email }}</p>@endif
                                </div>

                                @can('patients.manage')
                                    @unless($guardian->pivot->is_primary)
                                        <form method="POST" action="{{ route('patients.guardians.primary', [$patient, $guardian]) }}" class="js-confirm-primary" data-name="{{ $guardian->full_name }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">Marcar principal</button>
                                        </form>
                                    @endunless
                                @endcan
                            </div>

                            @can('patients.manage')
                                <details class="mt-4 border-t border-slate-100 pt-4">
                                    <summary class="cursor-pointer text-sm font-semibold text-slate-600 hover:text-cyan-700">Editar responsable</summary>
                                    <form method="POST" action="{{ route('patients.guardians.update', [$patient, $guardian]) }}" class="mt-5 grid gap-x-5 gap-y-5 md:grid-cols-2">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Nombre *</label>
                                            <input name="first_name" value="{{ $guardian->first_name }}" required class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Segundo nombre</label>
                                            <input name="middle_name" value="{{ $guardian->middle_name }}" class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Apellido paterno *</label>
                                            <input name="last_name" value="{{ $guardian->last_name }}" required class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Apellido materno</label>
                                            <input name="second_last_name" value="{{ $guardian->second_last_name }}" class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Teléfono principal *</label>
                                            <input name="phone" value="{{ $guardian->phone }}" required class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Teléfono secundario</label>
                                            <input name="secondary_phone" value="{{ $guardian->secondary_phone }}" class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                                            <input type="email" name="email" value="{{ $guardian->email }}" class="w-full">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Parentesco o relación</label>
                                            <input name="relationship" value="{{ $guardian->pivot->relationship }}" class="w-full">
                                        </div>
                                        <div class="md:col-span-2 rounded-xl bg-slate-50 px-4 py-3">
                                            <input type="hidden" name="is_primary" value="0">
                                            <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                                                <input type="checkbox" name="is_primary" value="1" @checked($guardian->pivot->is_primary)>
                                                Responsable principal
                                            </label>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-2 block text-sm font-semibold text-slate-700">Notas administrativas</label>
                                            <textarea name="administrative_notes" rows="3" class="w-full">{{ $guardian->administrative_notes }}</textarea>
                                        </div>
                                        <div class="md:col-span-2 flex justify-end border-t border-slate-100 pt-5">
                                            <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Guardar responsable</button>
                                        </div>
                                    </form>
                                </details>
                            @endcan
                        </article>
                    @empty
                        <p class="rounded-xl bg-slate-50 px-4 py-5 text-sm text-slate-500">Aún no hay responsables vinculados.</p>
                    @endforelse
                </div>
            </div>

            @can('patients.manage')
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Nuevo contacto</p>
                    <h3 class="mt-1 text-xl font-bold">Agregar responsable</h3>
                    <p class="mt-2 text-sm text-slate-500">Los responsables son contactos administrativos y no se convierten en usuarios del sistema.</p>

                    <form method="POST" action="{{ route('patients.guardians.store', $patient) }}" class="mt-6 grid gap-x-5 gap-y-5 md:grid-cols-2">
                        @csrf

                        <div>
                            <label for="guardian_first_name" class="mb-2 block text-sm font-semibold text-slate-700">Nombre *</label>
                            <input id="guardian_first_name" name="first_name" value="{{ old('first_name') }}" required class="w-full">
                            @error('first_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="guardian_middle_name" class="mb-2 block text-sm font-semibold text-slate-700">Segundo nombre</label>
                            <input id="guardian_middle_name" name="middle_name" value="{{ old('middle_name') }}" class="w-full">
                        </div>
                        <div>
                            <label for="guardian_last_name" class="mb-2 block text-sm font-semibold text-slate-700">Apellido paterno *</label>
                            <input id="guardian_last_name" name="last_name" value="{{ old('last_name') }}" required class="w-full">
                            @error('last_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="guardian_second_last_name" class="mb-2 block text-sm font-semibold text-slate-700">Apellido materno</label>
                            <input id="guardian_second_last_name" name="second_last_name" value="{{ old('second_last_name') }}" class="w-full">
                        </div>
                        <div>
                            <label for="guardian_phone" class="mb-2 block text-sm font-semibold text-slate-700">Teléfono principal *</label>
                            <input id="guardian_phone" name="phone" value="{{ old('phone') }}" required class="w-full">
                            @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="guardian_secondary_phone" class="mb-2 block text-sm font-semibold text-slate-700">Teléfono secundario</label>
                            <input id="guardian_secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}" class="w-full">
                        </div>
                        <div>
                            <label for="guardian_email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                            <input id="guardian_email" type="email" name="email" value="{{ old('email') }}" class="w-full">
                            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="guardian_relationship" class="mb-2 block text-sm font-semibold text-slate-700">Parentesco o relación</label>
                            <input id="guardian_relationship" name="relationship" value="{{ old('relationship') }}" class="w-full">
                        </div>

                        <div class="md:col-span-2 rounded-xl bg-slate-50 px-4 py-3">
                            <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                                <input type="checkbox" name="is_primary" value="1" @checked(old('is_primary'))>
                                Marcar como responsable principal
                            </label>
                            <p class="mt-1 pl-7 text-xs text-slate-500">Será el contacto prioritario del paciente. Solo puede existir un responsable principal.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="guardian_notes" class="mb-2 block text-sm font-semibold text-slate-700">Notas administrativas</label>
                            <textarea id="guardian_notes" name="administrative_notes" rows="3" class="w-full">{{ old('administrative_notes') }}</textarea>
                        </div>

                        <div class="md:col-span-2 flex justify-end border-t border-slate-100 pt-5">
                            <button type="submit" class="rounded-xl bg-cyan-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">Agregar responsable</button>
                        </div>
                    </form>
                </div>
            @endcan
        </section>
    </div>
</main>

<x-sweet-alerts />
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-confirm-patient-status').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === '1') return;
            event.preventDefault();
            const active = form.dataset.active === '1';
            const result = await Swal.fire({
                title: active ? '¿Desactivar paciente?' : '¿Activar paciente?',
                text: active
                    ? `${form.dataset.name} permanecerá en el sistema, pero quedará inactivo para nuevas operaciones.`
                    : `${form.dataset.name} volverá a quedar activo.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: active ? 'Sí, desactivar' : 'Sí, activar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            });
            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    });

    document.querySelectorAll('.js-confirm-primary').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === '1') return;
            event.preventDefault();
            const result = await Swal.fire({
                title: '¿Cambiar responsable principal?',
                text: `${form.dataset.name} quedará como contacto principal y cualquier responsable principal anterior dejará de serlo.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            });
            if (result.isConfirmed) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    });
});
</script>
</body>
</html>